<?php
App::uses('AppController', 'Controller');
/**
 * TempReportsVectors Controller
 *
 * @property TempReportsVector $TempReportsVector
 */
class TempReportsVectorsController extends AppController 
{
//
	public function temp_report($temp_report_id = false) 
	{
	/**
	 * temp_report method
	 * Shows only good temp_vectors associated with this temp_report
	 * @return void
	 */
		// get the temp_report details
		$this->set('temp_report', $this->TempReportsVector->TempReport->read(null, $temp_report_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->TempReportsVector->searchFields = array('TempVector.temp_vector', 'TempVector.type', 'VectorType.name');
		
		$conditions = array('TempReportsVector.temp_report_id' => $temp_report_id);
		
		$this->TempReportsVector->recursive = 0;
		$this->paginate['order'] = array('TempReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_reports_vectors', $this->paginate());
	}
	
//
	public function add($temp_report_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->TempReportsVector->TempReport->id = $temp_report_id;
		if (!$this->TempReportsVector->TempReport->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		
		$this->set('temp_report_id', $temp_report_id);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempReportsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'temp_reports', 'action' => 'view', $temp_report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->TempReportsVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if ($this->TempReportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->TempReportsVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function multiselect()
	{
	/*
	 * batch manage multiple items
	 */
		if (!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		if($this->request->data['TempReportsVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.TempReportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['TempReportsVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.TempReportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		
		if ($this->TempReportsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Temp Report.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Report.'));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempReportsVector');
			$multiselect_value = (isset($this->request->data['TempReportsVector']['vector_type_id'])?$this->request->data['TempReportsVector']['vector_type_id']:0);
			
			if($this->TempReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempReportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Temp Report.'));
				return $this->redirect($this->TempReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Report.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->TempReportsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempReportsVector');
			$multiselect_value = (isset($this->request->data['TempReportsVector'])?$this->request->data['TempReportsVector']:array());
			
			if($this->TempReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempReportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Report.'));
				return $this->redirect($this->TempReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Report.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.TempReportsVector');
		
		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('TempReportsVector.id' => $ids, 'TempVector.bad' => 0);
		$this->TempReportsVector->recursive = 0;
		$this->paginate['contain'] = array('TempVector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('TempReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_reports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->TempReportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($temp_report_id = false)
	{
		$this->TempReportsVector->TempReport->id = $temp_report_id;
		if (!$this->TempReportsVector->TempReport->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempReportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Report has been saved'));
				return $this->redirect(array('controller' => 'temp_reports', 'action' => 'view', $temp_report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Temp Report could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempReportsVector']['temp_report_id'] = $temp_report_id;
		}
		
		// get the object types
		$this->set('temp_report_id', $temp_report_id);
		$this->set('vectorTypes', $this->TempReportsVector->VectorType->typeFormList());
	}
	
//
	public function assign_vector_multitypes($temp_report_id = false)
	{
		$this->TempReportsVector->TempReport->id = $temp_report_id;
		if (!$this->TempReportsVector->TempReport->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempReportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Report\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'temp_reports', 'action' => 'view', $temp_report_id));
			} 
			else
			{
				$this->Session->setFlash(__('The Temp Report could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempReportsVector']['temp_report_id'] = $temp_report_id;
		}
		
		$this->TempReportsVector->searchFields = array('TempVector.temp_vector');
		
		$this->set('temp_report_id', $temp_report_id);
		$this->set('temp_reports_vectors', $this->TempReportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('TempVector', 'VectorType'),
			'conditions' => $this->TempImportsVector->conditions(array('TempReportsVector.temp_report_id' => $temp_report_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->TempReportsVector->VectorType->typeFormList());
	}
	
//
	public function delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->TempReportsVector->id = $id;
		if (!$this->TempReportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->TempReportsVector->delete()) {
			$this->Session->setFlash(__('The Vector was removed from this Report.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Report.'));
		$this->redirect($this->referer());
	}
}

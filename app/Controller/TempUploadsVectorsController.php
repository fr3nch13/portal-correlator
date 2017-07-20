<?php
App::uses('AppController', 'Controller');
/**
 * TempUploadsVectors Controller
 *
 * @property TempUploadsVector $TempUploadsVector
 */
class TempUploadsVectorsController extends AppController 
{
	
//
	public function temp_upload($temp_upload_id = false) 
	{
	/**
	 * temp_upload method
	 * Shows only good temp_vectors associated with this temp_upload
	 * @return void
	 */
		// get the temp_upload details
		$this->set('temp_upload', $this->TempUploadsVector->TempUpload->read(null, $temp_upload_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->TempUploadsVector->searchFields = array('TempVector.temp_vector', 'TempVector.type', 'VectorType.name');
		
		$conditions = array('TempUploadsVector.temp_upload_id' => $temp_upload_id);
		
		$this->TempUploadsVector->recursive = 0;
		$this->paginate['order'] = array('TempUploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_uploads_vectors', $this->paginate());
	}
	
//
	public function add($temp_upload_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->TempUploadsVector->TempUpload->id = $temp_upload_id;
		if (!$this->TempUploadsVector->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid temp_upload'));
		}
		
		$this->set('temp_upload_id', $temp_upload_id);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempUploadsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'temp_uploads', 'action' => 'view', $temp_upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->TempUploadsVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if ($this->TempUploadsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->TempUploadsVector->modelError);
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
		if($this->request->data['TempUploadsVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.TempUploadsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['TempUploadsVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.TempUploadsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		
		if ($this->TempUploadsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Temp Upload.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Upload.'));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempUploadsVector');
			$multiselect_value = (isset($this->request->data['TempUploadsVector']['vector_type_id'])?$this->request->data['TempUploadsVector']['vector_type_id']:0);
			
			if($this->TempUploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempUploadsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Temp Upload.'));
				return $this->redirect($this->TempUploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Upload.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->TempUploadsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempUploadsVector');
			$multiselect_value = (isset($this->request->data['TempUploadsVector'])?$this->request->data['TempUploadsVector']:array());
			
			if($this->TempUploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempUploadsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Upload.'));
				return $this->redirect($this->TempUploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Upload.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.TempUploadsVector');
		
		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('TempUploadsVector.id' => $ids, 'TempVector.bad' => 0);
		$this->TempUploadsVector->recursive = 0;
		$this->paginate['contain'] = array('TempVector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('TempUploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_uploads_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->TempUploadsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($temp_upload_id = false)
	{
		$this->TempUploadsVector->TempUpload->id = $temp_upload_id;
		if (!$this->TempUploadsVector->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempUploadsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('controller' => 'temp_uploads', 'action' => 'view', $temp_upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempUploadsVector']['temp_upload_id'] = $temp_upload_id;
		}
		
		// get the object types
		$this->set('temp_upload_id', $temp_upload_id);
		$this->set('vectorTypes', $this->TempUploadsVector->VectorType->typeFormList());
	}
	
//
	public function assign_vector_multitypes($temp_upload_id = false)
	{
		$this->TempUploadsVector->TempUpload->id = $temp_upload_id;
		if (!$this->TempUploadsVector->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempUploadsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Upload\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'temp_uploads', 'action' => 'view', $temp_upload_id));
			} 
			else
			{
				$this->Session->setFlash(__('The Temp Upload could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempUploadsVector']['temp_upload_id'] = $temp_upload_id;
		}
		
		$this->TempUploadsVector->searchFields = array('TempVector.temp_vector');
		
		$this->set('temp_upload_id', $temp_upload_id);
		$this->set('temp_uploads_vectors', $this->TempUploadsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('TempVector', 'VectorType'),
			'conditions' => $this->TempUploadsVector->conditions(array('TempUploadsVector.temp_upload_id' => $temp_upload_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->TempUploadsVector->VectorType->typeFormList());
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
		$this->TempUploadsVector->id = $id;
		if (!$this->TempUploadsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->TempUploadsVector->delete()) {
			$this->Session->setFlash(__('The Vector was removed from this File.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this File.'));
		$this->redirect($this->referer());
	}
}

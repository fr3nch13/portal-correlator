<?php
App::uses('AppController', 'Controller');
/**
 * TempImportsVectors Controller
 *
 * @property TempImportsVector $TempImportsVector
 */
class TempImportsVectorsController extends AppController 
{
	
//
	public function import($import_id = false) 
	{
		// get the import details
		$this->set('import', $this->TempImportsVector->Import->read(null, $import_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->TempImportsVector->searchFields = array('TempVector.temp_vector', 'TempVector.type', 'VectorType.name');
		
		$conditions = array('TempImportsVector.import_id' => $import_id);
		
		$this->TempImportsVector->recursive = 0;
		$this->paginate['order'] = array('TempImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_imports_vectors', $this->paginate());
	}
	
//
	public function add($import_id = null) 
	{
		$this->TempImportsVector->Import->id = $import_id;
		if (!$this->TempImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid import'));
		}
		
		$this->set('import_id', $import_id);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempImportsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->TempImportsVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
		if ($this->TempImportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->TempImportsVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function multiselect()
	{
		if (!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		if($this->request->data['TempImportsVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.TempImportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['TempImportsVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.TempImportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		
		if ($this->TempImportsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Import.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempImportsVector');
			$multiselect_value = (isset($this->request->data['TempImportsVector']['vector_type_id'])?$this->request->data['TempImportsVector']['vector_type_id']:0);
			
			if($this->TempImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Import.'));
				return $this->redirect($this->TempImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->TempImportsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempImportsVector');
			$multiselect_value = (isset($this->request->data['TempImportsVector'])?$this->request->data['TempImportsVector']:array());
			
			if($this->TempImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Import.'));
				return $this->redirect($this->TempImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.TempImportsVector');
		
		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('TempImportsVector.id' => $ids, 'TempVector.bad' => 0);
		$this->TempImportsVector->recursive = 0;
		$this->paginate['contain'] = array('TempVector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('TempImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_imports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->TempImportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($import_id = false)
	{
		$this->TempImportsVector->Import->id = $import_id;
		if (!$this->TempImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempImportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Import Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The Import Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
		$this->set('vectorTypes', $this->TempImportsVector->VectorType->typeFormList());
	}
	
//
	public function assign_vector_multitypes($import_id = false)
	{
		$this->TempImportsVector->Import->id = $import_id;
		if (!$this->TempImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempImportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Import\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			} 
			else
			{
				$this->Session->setFlash(__('The Temp Import could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempImportsVector']['import_id'] = $import_id;
		}
		
		$this->TempImportsVector->searchFields = array('TempVector.temp_vector');
		
		$this->set('import_id', $import_id);
		$this->set('temp_imports_vectors', $this->TempImportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('TempVector', 'VectorType'),
			'conditions' => $this->TempImportsVector->conditions(array('TempImportsVector.import_id' => $import_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->TempImportsVector->VectorType->typeFormList());
	}
	
//
	public function delete($id = null) 
	{
		$this->TempImportsVector->id = $id;
		if (!$this->TempImportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->TempImportsVector->delete()) {
			$this->Session->setFlash(__('The Vector was removed from this Import.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Import.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_import($import_id = false) 
	{
		// get the import details
		$this->set('import', $this->TempImportsVector->Import->read(null, $import_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->TempImportsVector->searchFields = array('TempVector.temp_vector', 'TempVector.type', 'VectorType.name');
		
		$conditions = array('TempImportsVector.import_id' => $import_id);
		
		$this->TempImportsVector->recursive = 0;
		$this->paginate['order'] = array('TempImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_imports_vectors', $this->paginate());
	}
}

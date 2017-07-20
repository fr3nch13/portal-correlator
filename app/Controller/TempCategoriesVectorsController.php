<?php
App::uses('AppController', 'Controller');
/**
 * TempCategoriesVectors Controller
 *
 * @property TempCategoriesVector $TempCategoriesVector
 */
class TempCategoriesVectorsController extends AppController 
{
//
	public function temp_category($temp_category_id = false) 
	{
	/**
	 * temp_category method
	 * Shows only good temp_vectors associated with this temp_category
	 * @return void
	 */
		// get the temp_category details
		$this->set('temp_category', $this->TempCategoriesVector->TempCategory->read(null, $temp_category_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->TempCategoriesVector->searchFields = array('TempVector.temp_vector', 'TempVector.type', 'VectorType.name');
		
		$conditions = array('TempCategoriesVector.temp_category_id' => $temp_category_id);
		
		$this->TempCategoriesVector->recursive = 0;
		$this->paginate['order'] = array('TempCategoriesVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempCategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_categories_vectors', $this->paginate());
	}
	
//
	public function add($temp_category_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->TempCategoriesVector->TempCategory->id = $temp_category_id;
		if (!$this->TempCategoriesVector->TempCategory->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Category'));
		}
		
		$this->set('temp_category_id', $temp_category_id);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempCategoriesVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'temp_categories', 'action' => 'view', $temp_category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->TempCategoriesVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if ($this->TempCategoriesVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->TempCategoriesVector->modelError);
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
		if($this->request->data['TempCategoriesVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.TempCategoriesVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['TempCategoriesVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.TempCategoriesVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		
		if ($this->TempCategoriesVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Temp Category.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Category.'));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempCategoriesVector');
			$multiselect_value = (isset($this->request->data['TempCategoriesVector']['vector_type_id'])?$this->request->data['TempCategoriesVector']['vector_type_id']:0);
			
			if($this->TempCategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempCategoriesVector');
				$this->Session->setFlash(__('The Vectors were updated for this Temp Category.'));
				return $this->redirect($this->TempCategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Category.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->TempCategoriesVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.TempCategoriesVector');
			$multiselect_value = (isset($this->request->data['TempCategoriesVector'])?$this->request->data['TempCategoriesVector']:array());
			
			if($this->TempCategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.TempCategoriesVector');
				$this->Session->setFlash(__('The Vectors were updated for this Temp Category.'));
				return $this->redirect($this->TempCategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Temp Category.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.TempCategoriesVector');
		
		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('TempCategoriesVector.id' => $ids, 'TempVector.bad' => 0);
		$this->TempCategoriesVector->recursive = 0;
		$this->paginate['contain'] = array('TempVector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('TempCategoriesVector.id' => 'desc');
		$this->paginate['conditions'] = $this->TempCategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('temp_categories_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->TempCategoriesVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($temp_category_id = false)
	{
		$this->TempCategoriesVector->TempCategory->id = $temp_category_id;
		if (!$this->TempCategoriesVector->TempCategory->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempCategoriesVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Category has been saved'));
				return $this->redirect(array('controller' => 'temp_categories', 'action' => 'view', $temp_category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Temp Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempCategoriesVector']['temp_category_id'] = $temp_category_id;
		}
		
		// get the object types
		$this->set('temp_category_id', $temp_category_id);
		$this->set('vectorTypes', $this->TempCategoriesVector->VectorType->typeFormList());
	}
	
	public function assign_vector_multitypes($temp_category_id = false)
	{
		$this->TempCategoriesVector->TempCategory->id = $temp_category_id;
		if (!$this->TempCategoriesVector->TempCategory->exists()) 
		{
			throw new NotFoundException(__('Invalid Temp Category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempCategoriesVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Temp Category\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'temp_categories', 'action' => 'view', $temp_category_id));
			} 
			else
			{
				$this->Session->setFlash(__('The Temp Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['TempCategoriesVector']['temp_category_id'] = $temp_category_id;
		}
		
		$this->TempCategoriesVector->searchFields = array('TempVector.temp_vector');
		
		$this->set('temp_category_id', $temp_category_id);
		$this->set('temp_categories_vectors', $this->TempCategoriesVector->find('all', array(
			'recursive' => 0,
			'contain' => array('TempVector', 'VectorType'),
			'conditions' => $this->TempCategoriesVector->conditions(array('TempCategoriesVector.temp_category_id' => $temp_category_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->TempCategoriesVector->VectorType->typeFormList());
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

		$this->TempCategoriesVector->id = $id;
		if (!$this->TempCategoriesVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->TempCategoriesVector->delete()) {
			$this->Session->setFlash(__('The Temp Vector was removed from this Temp Category.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Temp Vector was NOT removed from this Temp Category.'));
		$this->redirect($this->referer());
	}
}

<?php
App::uses('AppController', 'Controller');
/**
 * VectorTypes Controller
 *
 * @property VectorType $VectorType
 */
class VectorTypesController extends AppController 
{
	
	public function index() 
	{
	/**
	 * index method
	 *
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		// include just the user information
		$this->paginate['order'] = array('VectorType.name' => 'desc');
		$this->paginate['conditions'] = $this->VectorType->conditions($conditions, $this->passedArgs); 
		$this->set('vectorTypes', $this->paginate());
	}
	
	public function view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->VectorType->id = $id;
		if (!$this->VectorType->exists()) 
		{
			throw new NotFoundException(__('Invalid vector type'));
		}
		
		// get the counts
		$this->VectorType->getCounts = array(
			'CategoriesVector' => array(
				'all' => array(
					'recursive' => 0,
					'contain' => array('Vector', 'Category'),
					'conditions' => array(
						'CategoriesVector.vector_type_id' => $id,
						'CategoriesVector.active' => 1,
						'Vector.bad' => 0,
						'Category.org_group_id' => AuthComponent::user('org_group_id'),
					),
				),
			),
			'ReportsVector' => array(
				'all' => array(
					'recursive' => 0,
					'contain' => array('Vector', 'Report'),
					'conditions' => array(
						'ReportsVector.vector_type_id' => $id,
						'ReportsVector.active' => 1,
						'Vector.bad' => 0,
						'Report.org_group_id' => AuthComponent::user('org_group_id'),
					),
				),
			),
			'UploadsVector' => array(
				'all' => array(
					'recursive' => 0,
					'contain' => array('Vector', 'Upload'),
					'conditions' => array(
						'UploadsVector.vector_type_id' => $id,
						'UploadsVector.active' => 1,
						'Vector.bad' => 0,
						'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					),
				),
			),
			'Vector' => array(
				'all' => array(
					'conditions' => array(
						'Vector.vector_type_id' => $id,
						'Vector.bad' => 0,
					),
				),
			),
/*
			'TempCategoriesVector' => array(
				'all' => array('conditions' => array('TempCategoriesVector.vector_type_id' => $id)),
			),
			'TempReportsVector' => array(
				'all' => array('conditions' => array('TempReportsVector.vector_type_id' => $id)),
			),
			'TempUploadsVector' => array(
				'all' => array('conditions' => array('TempUploadsVector.vector_type_id' => $id)),
			),
*/
		);
		
		$this->set('vectorType', $this->VectorType->read(null, $id));
	}
	
	public function vector_type($id = null)
	{
		$this->VectorType->id = $id;
		if (!$this->VectorType->exists()) 
		{
			throw new NotFoundException(__('Invalid vector type'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		// include just the user information
		$this->paginate['order'] = array('VectorType.name' => 'desc');
		$this->paginate['conditions'] = $this->VectorType->conditions($conditions, $this->passedArgs); 
		$this->set('vectorTypes', $this->paginate());
	}
	
	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		// include just the user information
		$this->paginate['order'] = array('VectorType.name' => 'desc');
		$this->paginate['conditions'] = $this->VectorType->conditions($conditions, $this->passedArgs); 
		$this->set('vectorTypes', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->VectorType->id = $id;
		if (!$this->VectorType->exists()) 
		{
			throw new NotFoundException(__('Invalid vector type'));
		}
		
		// get the counts
		$this->VectorType->getCounts = array(
			'Vector' => array(
				'all' => array(
					'conditions' => array(
						'Vector.vector_type_id' => $id,
					),
				),
				'good' => array(
					'conditions' => array(
						'Vector.vector_type_id' => $id,
						'Vector.bad' => 0,
					),
				),
				'bad' => array(
					'conditions' => array(
						'Vector.vector_type_id' => $id,
						'Vector.bad' => 1,
					),
				),
			),
			'CategoriesVector' => array(
				'all' => array('conditions' => array('CategoriesVector.vector_type_id' => $id)),
			),
			'ReportsVector' => array(
				'all' => array('conditions' => array('ReportsVector.vector_type_id' => $id)),
			),
			'UploadsVector' => array(
				'all' => array('conditions' => array('UploadsVector.vector_type_id' => $id)),
			),
			'TempCategoriesVector' => array(
				'all' => array('conditions' => array('TempCategoriesVector.vector_type_id' => $id)),
			),
			'TempReportsVector' => array(
				'all' => array('conditions' => array('TempReportsVector.vector_type_id' => $id)),
			),
			'TempUploadsVector' => array(
				'all' => array('conditions' => array('TempUploadsVector.vector_type_id' => $id)),
			),
		);
		
		$this->set('vectorType', $this->VectorType->read(null, $id));
	}
	
	public function admin_add() 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post')) 
		{
			$this->VectorType->create();
			if ($this->VectorType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vector Type has been saved'));
				return $this->redirect(array('action' => 'view', $this->VectorType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Vector Type could not be saved. Please, try again.'));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->VectorType->id = $id;
		if (!$this->VectorType->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector Type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->VectorType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vector Type has been saved'));
				return $this->redirect(array('action' => 'view', $this->VectorType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Vector Type could not be saved. Please, try again.'));
			}
		} 
		else 
		{
			$this->request->data = $this->VectorType->read(null, $id);
		}
	}
	
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->VectorType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->VectorType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->VectorType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->VectorType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->VectorType->id = $id;
		if (!$this->VectorType->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector Type'));
		}
		if ($this->VectorType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Vector type deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Vector type was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

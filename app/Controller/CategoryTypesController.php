<?php
App::uses('AppController', 'Controller');
/**
 * CategoryTypes Controller
 *
 * @property CategoryType $CategoryType
 */
class CategoryTypesController extends AppController 
{
	public function isAuthorized($user = array())
	{
	/*
	 * Only users that are a part of the same org as these can view
	 */
		if (in_array($this->action, array('view'))) 
		{
			$categoryTypeId = $this->request->params['pass'][0];
			if($this->CategoryType->isSameOrgGroup($categoryTypeId, AuthComponent::user('org_group_id')))
			{
				return true;
			}	
		}
		return parent::isAuthorized($user);
	}
	
	public function index() 
	{
	/**
	 * index method
	 *
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoryType.org_group_id' => array(AuthComponent::user('org_group_id'), 0),
		);
		
		// include just the user information
		$this->paginate['order'] = array('CategoryType.name' => 'desc');
		$this->paginate['conditions'] = $this->CategoryType->conditions($conditions, $this->passedArgs); 
		$this->set('categoryTypes', $this->paginate());
	}
	
	public function view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid category type'));
		}
		
		// get the counts
		$this->CategoryType->getCounts = array(
			'Category' => array(
				'all' => array('conditions' => array('Category.category_type_id' => $id)),
				'public' => array(
					'conditions' => array(
						'Category.category_type_id' => $id,
						'OR' => array(
							'Category.public' => 2,
							array(
								'Category.public' => 1,
								'Category.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Category.public' => 0,
								'Category.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
			'TempCategory' => array(
				'mine' => array(
					'conditions' => array(
						'TempCategory.category_type_id' => $id,
						'TempCategory.user_id' => AuthComponent::user('id'),
					)
				),
			),
		);
		
		$this->set('categoryType', $this->CategoryType->read(null, $id));
	}
	
	public function manager_index() 
	{
	/**
	 * index method
	 *
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoryType.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('CategoryType.name' => 'desc');
		$this->paginate['conditions'] = $this->CategoryType->conditions($conditions, $this->passedArgs); 
		$this->set('categoryTypes', $this->paginate());
	}
	
	public function manager_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		
		// get the counts
		$this->CategoryType->getCounts = array(
			'Category' => array(
				'public' => array(
					'conditions' => array(
						'Category.category_type_id' => $id,
						'OR' => array(
							'Category.public' => 2,
							array(
								'Category.public' => 1,
								'Category.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Category.public' => 0,
								'Category.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
		);
		
		$this->set('categoryType', $this->CategoryType->read(null, $id));
	}
	
	public function manager_add() 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post')) 
		{
			$this->CategoryType->create();
			// assign this group to the same as the manager's
			$this->request->data['CategoryType']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if ($this->CategoryType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->CategoryType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Category Group could not be saved. Please, try again.'));
			}
		}
	}
	
	public function manager_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CategoryType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->CategoryType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Category Group could not be saved. Please, try again.'));
			}
		} 
		
		$this->request->data = $this->CategoryType->read(null, $id);
	}
	
	public function manager_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->CategoryType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Category Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoryType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function manager_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->CategoryType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The Category Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoryType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function manager_delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		if ($this->CategoryType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Category Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Category Group was not deleted'));
		$this->redirect(array('action' => 'index'));
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
		$this->CategoryType->recursive = 0;
		$this->paginate['contain'] = array('OrgGroup');
		
		// include just the user information
		$this->paginate['order'] = array('CategoryType.name' => 'desc');
		$this->paginate['conditions'] = $this->CategoryType->conditions($conditions, $this->passedArgs); 
		$this->set('categoryTypes', $this->paginate());
	}
	
	public function admin_listfromuserid($user_id = 0) 
	{
	/**
	 * json list method
	 * returns a list of categorytypes based on the org_group_id of the selected user
	 *
	 * @return void
	 */
	 	$category_types = array();
	 	$user_org_group_id = 0;
	 	$this->CategoryType->Category->User->id = $user_id;
	 	$user_org_group_id = $this->CategoryType->Category->User->field('org_group_id');
		
		$category_types = $this->CategoryType->typeFormList($user_org_group_id);
		$this->set('category_types', $category_types);
	}
	
	public function admin_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		
		// get the counts
		$this->CategoryType->getCounts = array(
			'Category' => array(
				'all' => array('conditions' => array('Category.category_type_id' => $id)),
			),
			'TempCategory' => array(
				'all' => array('conditions' => array('TempCategory.category_type_id' => $id)),
			),
		);
		
		$this->CategoryType->recursive = 0;
		$this->CategoryType->contain(array('OrgGroup'));
		$this->set('categoryType', $this->CategoryType->read(null, $id));
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
			$this->CategoryType->create();
			if ($this->CategoryType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->CategoryType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Category Group could not be saved. Please, try again.'));
			}
		}
		
		// get the org groups
		$orgGroups = $this->CategoryType->OrgGroup->find('list', array(
			'order' => 'OrgGroup.name',
		));
		$this->set('orgGroups', $orgGroups);
	}
	
	public function admin_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CategoryType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->CategoryType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Category Group could not be saved. Please, try again.'));
			}
		}
		
		$this->request->data = $this->CategoryType->read(null, $id);
		
		// get the org groups
		$orgGroups = $this->CategoryType->OrgGroup->find('list', array(
			'order' => 'OrgGroup.name',
		));
		$this->set('orgGroups', $orgGroups);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->CategoryType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Category Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoryType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->CategoryType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The Category Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoryType->modelError);
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
		$this->CategoryType->id = $id;
		if (!$this->CategoryType->exists()) 
		{
			throw new NotFoundException(__('Invalid Category Group'));
		}
		if ($this->CategoryType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Category Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Category Group was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

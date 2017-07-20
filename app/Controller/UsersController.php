<?php
// app/Controller/UsersController.php

class UsersController extends AppController
{
	public $allowAdminDelete = true;
	
	public function isAuthorized($user = array())
	{
	/*
	 * Checks permissions for a user when trying to access a category
	 */
		// unregistered users
		if (in_array($this->action, array('login'))) 
		{
			return true;
		}
		
		if ($this->action === 'admin_login')
		{
			return $this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false));
		}
		
		// The owner of a Category can view, edit and delete it
		if (in_array($this->action, array('index'))) 
		{
			if($this->Auth->user('id'))
			{
				return true;
			}
		}
		
		if ($this->action === 'admin_logout')
		{
			return $this->redirect(array('controller' => 'users', 'action' => 'logout', 'admin' => false));
		}
		
		// The owner of a Category can toggle a field
		if (in_array($this->action, array('toggle'))) 
		{
			$categoryId = $this->request->params['pass'][1];
			if ($this->Category->isOwnedBy($categoryId, $this->Auth->user('id')))
			{
				return true;
			}
		}
		
		// if anyone else can view this category. if it's a public one and part of the same org group
		if (in_array($this->action, array('view'))) 
		{
			$userId = $this->request->params['pass'][0];
			if ($this->User->isSameOrgGroup($userId, $this->Auth->user('org_group_id')))
			{
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
	public function beforeFilter()
	{
		$this->Auth->allow(
			'logout',
			'admin_login',
			'admin_logout'
			);
		return parent::beforeFilter();
	}

	public function login()
	{
		// have the OAuthClient component handle everything for this action
		return $this->OAuthClient->OAC_Login();
	}
	
	public function admin_login() 
	{
		return 	$this->login();
	}

	public function logout()
	{
		$this->Session->setFlash(__('You have successfully logged out.'));
		return $this->redirect($this->Auth->logout());
	}
	
	public function admin_logout() 
	{
		return 	$this->logout();
	}

	public function index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'User.org_group_id' => $this->Auth->user('org_group_id'),
		);
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->User->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'User.org_group_id' => $this->Auth->user('org_group_id'),
		);
		$conditions[] = $this->User->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'User');
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

	public function view($id = null)
	{
	/*
	 * View a user's information
	 */
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid user'));
		}
		
		// get the counts
		$this->User->getCounts = array(
			'Category' => array(
				'public' => array(
					'conditions' => array(
						'Category.user_id' => $id,
						'OR' => array(
							'Category.public' => 2,
							array(
								'Category.public' => 1,
								'Category.org_group_id' => $this->Auth->user('org_group_id'),
							),
						),
					),
				),
			),
			'Report' => array(
				'public' => array(
					'conditions' => array(
						'Report.user_id' => $id,
						'OR' => array(
							'Report.public' => 2,
							array(
								'Report.public' => 1,
								'Report.org_group_id' => $this->Auth->user('org_group_id'),
							),
						),
					),
				),
			),
			'Upload' => array(
				'public' => array(
					'recursive' => 0,
					'conditions' => array(
						'Upload.user_id' => $id,
						'OR' => array(
							'Upload.public' => 2,
							'OR' => array(
								array('Upload.category_id !=' => 0, 'Category.public' => 2),
								array('Upload.report_id !=' => 0, 'Report.public' => 2),
								array('Upload.category_id' => 0, 'Upload.report_id' => 0),
							),
							array(
								'Upload.public' => 1,
								'Upload.org_group_id' => $this->Auth->user('org_group_id'),
								'OR' => array(
									array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => $this->Auth->user('org_group_id')),
									array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => $this->Auth->user('org_group_id')),
									array('Upload.category_id' => 0, 'Upload.report_id' => 0),
								),
							),
						),
					),
				),
			),
		);
		
		$this->User->recursive = 0;
		$this->set('user', $this->User->read(null, $id));
	}

	public function edit()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		$this->User->includeOrgName = false;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if(isset($this->request->query['flashmsg']))
		{
			$this->Session->setFlash($this->request->query['flashmsg']);
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->request->data['User']))
				{
					foreach($this->request->data['User'] as $k => $v)
					{
						if ($this->Session->check('Auth.User.'. $k))
						{
							$this->Session->write('Auth.User.'. $k, $v);
						}
					}
				}
				if (isset($this->request->data['UsersSetting']))
				{
					foreach($this->request->data['UsersSetting'] as $k => $v)
					{
						$this->Session->write('Auth.User.UsersSetting.'. $k, $v);
					}
				}
				
				$this->Session->setFlash(__('Your settings have been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Session->setFlash(__('We could not update your settings. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $user;
		}
	}

	public function edit_session()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		$availableRoles = $this->User->availableRoles(AuthComponent::user('id'));
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->bypassReferer = true;
			$chosenRole = false;
			if(!isset($this->request->data['User']['role']))
			{
				$this->Flash->error(__('Unknown %s %s - %s', __('User'), __('Role'), '1'));
				return $this->redirect(array('action' => 'edit_session'));
			}
			
			$chosenRole = $this->request->data['User']['role'];
			if(!in_array($chosenRole, array_keys($availableRoles)))
			{
				$this->Flash->error(__('Unknown %s %s - %s', __('User'), __('Role'), '2'));
				return $this->redirect(array('action' => 'edit_session'));
			}
			
			$this->Session->write('Auth.User.role', $chosenRole);
			
			$this->Flash->success(__('Your %s has been changed to %s for this session', __('Role'), $availableRoles[$chosenRole]));
		}
		
		$availableRoles = $this->User->availableRoles(AuthComponent::user('id'));
		$this->set('availableRoles', $availableRoles);
	}
	
////// Manager pages

	public function manager_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'User.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

//
	public function manager_view($id = null)
	{
	/*
	 * View a user's information
	 */
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid user'));
		}
		
		// get the counts
		$this->User->getCounts = array(
			'Category' => array(
				'public' => array(
					'conditions' => array(
						'Category.user_id' => $id,
						'OR' => array(
							'Category.public' => 2,
							array(
								'Category.public' => 1,
								'Category.org_group_id' => $this->Auth->user('org_group_id'),
							),
						),
					),
				),
			),
			'Report' => array(
				'public' => array(
					'conditions' => array(
						'Report.user_id' => $id,
						'OR' => array(
							'Report.public' => 2,
							array(
								'Report.public' => 1,
								'Report.org_group_id' => $this->Auth->user('org_group_id'),
							),
						),
					),
				),
			),
			'Upload' => array(
				'public' => array(
					'recursive' => 0,
					'conditions' => array(
						'Upload.user_id' => $id,
						'OR' => array(
							'Upload.public' => 2,
							'OR' => array(
								array('Upload.category_id !=' => 0, 'Category.public' => 2),
								array('Upload.report_id !=' => 0, 'Report.public' => 2),
								array('Upload.category_id' => 0, 'Upload.report_id' => 0),
							),
							array(
								'Upload.public' => 1,
								'Upload.org_group_id' => $this->Auth->user('org_group_id'),
								'OR' => array(
									array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => $this->Auth->user('org_group_id')),
									array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => $this->Auth->user('org_group_id')),
									array('Upload.category_id' => 0, 'Upload.report_id' => 0),
								),
							),
						),
					),
				),
			),
/*
			'Tagged' => array( 
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'User',
						'Tagged.foreign_key' => $id
					),
				),
			),
*/
		);
		
		$this->set('user', $this->User->read(null, $id));
	}

	public function manager_edit($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->request->data = $user;
		}
		
		// get the User groups
		$this->set('org_groups', $this->User->OrgGroup->find('list', array('order' => 'OrgGroup.name')));
	}

	public function manager_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The User has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}

//
	public function admin_admin()
	{
	/*
	 * The Admin Dashboard for accessing the admin side of things
	 */
	}

	public function admin_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->User->recursive = 0;
		$this->paginate['contain'] = array('OrgGroup');
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

	public function admin_view($id = null)
	{
	/*
	 * View all of the user's details
	 */
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid user'));
		}
		
		// get the counts
		$this->User->getCounts = array(
			'Category' => array(
				'all' => array(
					'conditions' => array(
						'Category.user_id' => $id,
					)
				),
				'public' => array(
					'conditions' => array(
						'Category.user_id' => $id,
						'Category.public >' => 0,
					),
				),
			),
			'Report' => array(
				'all' => array(
					'conditions' => array(
						'Report.user_id' => $id,
					),
				),
				'public' => array(
					'conditions' => array(
						'Report.user_id' => $id,
						'Report.public >' => 0,
					),
				),
			),
			'Upload' => array(
				'all' => array(
					'conditions' => array(
						'Upload.user_id' => $id,
					),
				),
				'public' => array(
					'conditions' => array(
						'Upload.user_id' => $id,
						'Upload.public >' => 0,
					),
				),
			),
/*
			'Tag' => array(
				'all' => array(),
			),
*/
		);
		$this->User->recursive = 0;
		$this->set('user', $this->User->read(null, $id));
	}

	public function admin_group($id = 0)
	{
	 
		$this->Prg->commonProcess();
		
		$conditions = array('User.org_group_id' => $id);
		
		$this->User->recursive = 0;
		$this->paginate['order'] = array('User.id' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs); 
		$this->set('users', $this->paginate());
	}
	
	public function admin_tag($tag_id = null) 
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->User->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->User->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'User');
		
		$this->User->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->paginate['order'] = array('User.id' => 'desc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

	public function admin_add()
	{
		if ($this->request->is('post'))
		{
			$this->User->create();
			if ($this->User->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		
		// get the User groups
		$this->set('org_groups', $this->User->OrgGroup->find('list', array('order' => 'OrgGroup.name')));
	}

	public function admin_edit($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		$this->User->includeOrgName = false;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->request->data = $user;
		}
		
		// get the User groups
		$this->set('org_groups', $this->User->OrgGroup->find('list', array('order' => 'OrgGroup.name')));
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Session->setFlash($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	/// Config for the app
	public function admin_config()
	{
		// check that we can read/write to the config
		if(!$this->User->configCheck())
		{
			throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			// check that we can read/write to the config
			if(!$this->User->configCheck(true))
			{
				throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
			}
			if ($this->User->configSave($this->request->data))
			{
				$this->Session->setFlash(__('The config has been saved'));
				return $this->redirect(array('action' => 'config'));
			}
			else
			{
				$this->Session->setFlash(__('The config could not be saved. Please, try again.'));
			}
		}
		
		$this->set('fields', $this->User->configKeys());
		
		$this->request->data = $this->User->configRead();
	}
}
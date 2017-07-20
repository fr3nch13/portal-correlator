<?php
App::uses('AppController', 'Controller');
/**
 * UploadTypes Controller
 *
 * @property UploadType $UploadType
 */
class UploadTypesController extends AppController 
{
	public function isAuthorized($user = array())
	{
	/*
	 * Only users that are a part of the same org as these can view
	 */
		if (in_array($this->action, array('view'))) 
		{
			$uploadTypeId = $this->request->params['pass'][0];
			if($this->UploadType->isSameOrgGroup($uploadTypeId, AuthComponent::user('org_group_id')))
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
			'UploadType.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('UploadType.name' => 'desc');
		$this->paginate['conditions'] = $this->UploadType->conditions($conditions, $this->passedArgs); 
		$this->set('uploadTypes', $this->paginate());
	}
	
	public function view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid File Group'));
		}
		
		// get the counts
		$this->UploadType->getCounts = array(
			'Upload' => array(
				'public' => array(
					'recursive' => 0,
					'conditions' => array(
						'Upload.upload_type_id' => $id, 
						'OR' => array(
							'Upload.public' => 2,
							'OR' => array(
								array('Upload.category_id !=' => 0, 'Category.public' => 2),
								array('Upload.report_id !=' => 0, 'Report.public' => 2),
								array('Upload.category_id' => 0, 'Upload.report_id' => 0),
							),
							array(
								'Upload.public' => 1,
								'Upload.org_group_id' => AuthComponent::user('org_group_id'),
								'OR' => array(
									array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
									array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
									array('Upload.category_id' => 0, 'Upload.report_id' => 0),
								),
							),
							array(
								'Upload.public' => 0,
								'Upload.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
			'TempUpload' => array(
				'mine' => array(
					'conditions' => array(
						'TempUpload.upload_type_id' => $id,
						'TempUpload.user_id' => AuthComponent::user('id'),
					)
				),
			),
		);
		
		$this->set('uploadType', $this->UploadType->read(null, $id));
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
			'UploadType.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('UploadType.name' => 'desc');
		$this->paginate['conditions'] = $this->UploadType->conditions($conditions, $this->passedArgs); 
		$this->set('uploadTypes', $this->paginate());
	}
	
	public function manager_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid File Group'));
		}
		
		// get the counts
		$this->UploadType->getCounts = array(
			'Upload' => array(
				'public' => array(
					'recursive' => 0,
					'conditions' => array(
						'Upload.upload_type_id' => $id, 
						'OR' => array(
							'Upload.public' => 2,
							'OR' => array(
								array('Upload.category_id !=' => 0, 'Category.public' => 2),
								array('Upload.report_id !=' => 0, 'Report.public' => 2),
								array('Upload.category_id' => 0, 'Upload.report_id' => 0),
							),
							array(
								'Upload.public' => 1,
								'Upload.org_group_id' => AuthComponent::user('org_group_id'),
								'OR' => array(
									array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
									array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
									array('Upload.category_id' => 0, 'Upload.report_id' => 0),
								),
							),
							array(
								'Upload.public' => 0,
								'Upload.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
		);
		
		$this->set('uploadType', $this->UploadType->read(null, $id));
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
			$this->UploadType->create();
			// assign this group to the same as the manager's
			$this->request->data['UploadType']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if ($this->UploadType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The File Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->UploadType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The File Group could not be saved. Please, try again.'));
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
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid File Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The File Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->UploadType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The File Group could not be saved. Please, try again.'));
			}
		} 
		
		$this->request->data = $this->UploadType->read(null, $id);
	}
	
	public function manager_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->UploadType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The File Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function manager_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->UploadType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The File Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadType->modelError);
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
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid File Group'));
		}
		if ($this->UploadType->delete($id, false)) 
		{
			$this->Session->setFlash(__('File Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('File Group was not deleted'));
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
		$this->UploadType->recursive = 0;
		$this->paginate['contain'] = array('OrgGroup');
		$this->paginate['order'] = array('UploadType.name' => 'desc');
		$this->paginate['conditions'] = $this->UploadType->conditions($conditions, $this->passedArgs); 
		$this->set('uploadTypes', $this->paginate());
	}
	
	public function admin_listfromuserid($user_id = 0) 
	{
	/**
	 * json list method
	 * returns a list of ReportTypes based on the org_group_id of the selected user
	 *
	 * @return void
	 */
	 	$category_types = array();
	 	$user_org_group_id = 0;
	 	$this->UploadType->Upload->User->id = $user_id;
	 	$user_org_group_id = $this->UploadType->Upload->User->field('org_group_id');
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UploadType.org_group_id' => $user_org_group_id,
		);
		$upload_types = $this->UploadType->find('all', array(
			'conditions' => $conditions,
			'fields' => array('UploadType.id', 'UploadType.name'),
		));
		$this->set('upload_types', $upload_types);
	}
	
	public function admin_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid upload type'));
		}
		
		// get the counts
		$this->UploadType->getCounts = array(
			'Upload' => array(
				'all' => array('conditions' => array('Upload.upload_type_id' => $id)),
			),
			'TempUpload' => array(
				'all' => array('conditions' => array('TempUpload.upload_type_id' => $id)),
			),
		);
		
		$this->UploadType->recursive = 0;
		$this->UploadType->contain(array('OrgGroup'));
		$this->set('uploadType', $this->UploadType->read(null, $id));
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
			$this->UploadType->create();
			if ($this->UploadType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Upload Type has been saved'));
				return $this->redirect(array('action' => 'view', $this->UploadType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Upload Type could not be saved. Please, try again.'));
			}
		}
		
		// get the org groups
		$orgGroups = $this->UploadType->OrgGroup->find('list', array(
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
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload Type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Upload Type has been saved'));
				return $this->redirect(array('action' => 'view', $this->UploadType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Upload Type could not be saved. Please, try again.'));
			}
		} 
		else 
		{
			$this->request->data = $this->UploadType->read(null, $id);
		}
		
		// get the org groups
		$orgGroups = $this->UploadType->OrgGroup->find('list', array(
			'order' => 'OrgGroup.name',
		));
		$this->set('orgGroups', $orgGroups);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->UploadType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The File Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->UploadType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The File Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadType->modelError);
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
		$this->UploadType->id = $id;
		if (!$this->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload Type'));
		}
		if ($this->UploadType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Upload type deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Upload type was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

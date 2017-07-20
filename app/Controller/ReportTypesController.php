<?php
App::uses('AppController', 'Controller');
/**
 * ReportTypes Controller
 *
 * @property ReportType $ReportType
 */
class ReportTypesController extends AppController 
{
	public function isAuthorized($user = array())
	{
	/*
	 * Only users that are a part of the same org as these can view
	 */
		if (in_array($this->action, array('view'))) 
		{
			$reportTypeId = $this->request->params['pass'][0];
			if($this->ReportType->isSameOrgGroup($reportTypeId, AuthComponent::user('org_group_id')))
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
			'ReportType.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('ReportType.name' => 'desc');
		$this->paginate['conditions'] = $this->ReportType->conditions($conditions, $this->passedArgs); 
		$this->set('reportTypes', $this->paginate());
	}
	
	public function view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid report type'));
		}
		
		// get the counts
		$this->ReportType->getCounts = array(
			'Report' => array(
				'all' => array('conditions' => array('Report.report_type_id' => $id)),
				'public' => array(
					'conditions' => array(
						'Report.report_type_id' => $id, 
						'OR' => array(
							'Report.public' => 2,
							array(
								'Report.public' => 1,
								'Report.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Report.public' => 0,
								'Report.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
			'TempReport' => array(
				'mine' => array(
					'conditions' => array(
						'TempReport.report_type_id' => $id,
						'TempReport.user_id' => AuthComponent::user('id'),
					)
				),
			),
		);
		
		$this->set('reportType', $this->ReportType->read(null, $id));
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
			'ReportType.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('ReportType.name' => 'desc');
		$this->paginate['conditions'] = $this->ReportType->conditions($conditions, $this->passedArgs); 
		$this->set('reportTypes', $this->paginate());
	}
	
	public function manager_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid Report Group'));
		}
		
		// get the counts
		$this->ReportType->getCounts = array(
			'Report' => array(
				'public' => array(
					'conditions' => array(
						'Report.report_type_id' => $id,
						'OR' => array(
							'Report.public' => 2,
							array(
								'Report.public' => 1,
								'Report.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Report.public' => 0,
								'Report.user_id' => AuthComponent::user('id'),
							),
						),
					)
				),
			),
		);
		
		$this->set('reportType', $this->ReportType->read(null, $id));
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
			$this->ReportType->create();
			// assign this group to the same as the manager's
			$this->request->data['ReportType']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if ($this->ReportType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->ReportType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Report Group could not be saved. Please, try again.'));
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
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid Report Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->ReportType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Report Group could not be saved. Please, try again.'));
			}
		} 
		
		$this->request->data = $this->ReportType->read(null, $id);
	}
	
	public function manager_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->ReportType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Report Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ReportType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function manager_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->ReportType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The Report Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ReportType->modelError);
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
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid Report Group'));
		}
		if ($this->ReportType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Report Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Report Group was not deleted'));
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
		$this->ReportType->recursive = 0;
		$this->paginate['contain'] = array('OrgGroup');
		
		$this->paginate['order'] = array('ReportType.name' => 'desc');
		$this->paginate['conditions'] = $this->ReportType->conditions($conditions, $this->passedArgs); 
		$this->set('reportTypes', $this->paginate());
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
	 	$this->ReportType->Report->User->id = $user_id;
	 	$user_org_group_id = $this->ReportType->Report->User->field('org_group_id');
		
		$report_types = $this->ReportType->typeFormList($user_org_group_id);
		$this->set('report_types', $report_types);
	}
	
	public function admin_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid report type'));
		}
		
		// get the counts
		$this->ReportType->getCounts = array(
			'Report' => array(
				'all' => array('conditions' => array('Report.report_type_id' => $id)),
			),
			'TempReport' => array(
				'all' => array('conditions' => array('TempReport.report_type_id' => $id)),
			),
		);
		
		$this->ReportType->recursive = 0;
		$this->ReportType->contain(array('OrgGroup'));
		$this->set('reportType', $this->ReportType->read(null, $id));
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
			$this->ReportType->create();
			if ($this->ReportType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->ReportType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Report Group could not be saved. Please, try again.'));
			}
		}
		
		// get the org groups
		$orgGroups = $this->ReportType->OrgGroup->find('list', array(
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
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid Report Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportType->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report Group has been saved'));
				return $this->redirect(array('action' => 'view', $this->ReportType->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Report Group could not be saved. Please, try again.'));
			}
		} 
		else 
		{
			$this->request->data = $this->ReportType->read(null, $id);
		}
		
		// get the org groups
		$orgGroups = $this->ReportType->OrgGroup->find('list', array(
			'order' => 'OrgGroup.name',
		));
		$this->set('orgGroups', $orgGroups);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if ($this->ReportType->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Report Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ReportType->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_setdefault($field = null, $id = null)
	{
	/*
	 * Used to mark an object as the primary/default one
	 */
		if ($this->ReportType->defaultRecord($id, $field))
		{
			$this->Session->setFlash(__('The Report Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ReportType->modelError);
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
		$this->ReportType->id = $id;
		if (!$this->ReportType->exists()) 
		{
			throw new NotFoundException(__('Invalid Report Group'));
		}
		if ($this->ReportType->delete($id, false)) 
		{
			$this->Session->setFlash(__('Report Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Report Group was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

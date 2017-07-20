<?php
App::uses('AppController', 'Controller');

class AssessmentOrganizationsController extends AppController 
{
	public $allowAdminDelete = true;

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->AssessmentOrganization->recursive = -1;
		$this->paginate['order'] = array('AssessmentOrganization.name' => 'asc');
		$this->paginate['conditions'] = $this->AssessmentOrganization->conditions($conditions, $this->passedArgs); 
		$this->set('assessmentOrganizations', $this->paginate());
	}

	public function view($id = false) 
	{
		$this->AssessmentOrganization->recursive = 0;
		if(!$assessmentOrganization = $this->AssessmentOrganization->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Organization')));
		}
		$this->set('assessmentOrganization', $assessmentOrganization);
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->AssessmentOrganization->create();
			
			if ($this->AssessmentOrganization->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('Organization')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Organization')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$assessmentOrganization = $this->AssessmentOrganization->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Organization')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->AssessmentOrganization->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Organization')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Organization')));
			}
		}
		else
		{
			$this->request->data = $assessmentOrganization;
		}
	}
}

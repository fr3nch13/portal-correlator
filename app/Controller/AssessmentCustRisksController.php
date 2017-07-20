<?php
App::uses('AppController', 'Controller');

class AssessmentCustRisksController extends AppController 
{
	public $allowAdminDelete = true;

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->AssessmentCustRisk->recursive = -1;
		$this->paginate['order'] = array('AssessmentCustRisk.name' => 'asc');
		$this->paginate['conditions'] = $this->AssessmentCustRisk->conditions($conditions, $this->passedArgs); 
		$this->set('assessmentCustRisks', $this->paginate());
	}

	public function view($id = false) 
	{
		$this->AssessmentCustRisk->recursive = 0;
		if(!$assessmentCustRisk = $this->AssessmentCustRisk->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Customer Risk')));
		}
		$this->set('assessmentCustRisk', $assessmentCustRisk);
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->AssessmentCustRisk->create();
			
			if ($this->AssessmentCustRisk->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('Customer Risk')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Customer Risk')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$assessmentCustRisk = $this->AssessmentCustRisk->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Customer Risk')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->AssessmentCustRisk->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Customer Risk')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Customer Risk')));
			}
		}
		else
		{
			$this->request->data = $assessmentCustRisk;
		}
	}
}

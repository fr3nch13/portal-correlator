<?php
App::uses('AppController', 'Controller');

class AssessmentNihRisksController extends AppController 
{
	public $allowAdminDelete = true;

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->AssessmentNihRisk->recursive = -1;
		$this->paginate['order'] = array('AssessmentNihRisk.name' => 'asc');
		$this->paginate['conditions'] = $this->AssessmentNihRisk->conditions($conditions, $this->passedArgs); 
		$this->set('assessmentNihRisks', $this->paginate());
	}

	public function view($id = false) 
	{
		$this->AssessmentNihRisk->recursive = 0;
		if(!$assessmentNihRisk = $this->AssessmentNihRisk->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('NIH Risk')));
		}
		$this->set('assessmentNihRisk', $assessmentNihRisk);
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->AssessmentNihRisk->create();
			
			if ($this->AssessmentNihRisk->save($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('NIH Risk')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('NIH Risk')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$assessmentNihRisk = $this->AssessmentNihRisk->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('NIH Risk')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->AssessmentNihRisk->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('NIH Risk')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('NIH Risk')));
			}
		}
		else
		{
			$this->request->data = $assessmentNihRisk;
		}
	}
}

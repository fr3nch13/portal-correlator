<?php
App::uses('AppController', 'Controller');

class AssessmentOfficesController extends AppController 
{
	public $allowAdminDelete = true;

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->AssessmentOffice->recursive = -1;
		$this->paginate['order'] = array('AssessmentOffice.name' => 'asc');
		$this->paginate['conditions'] = $this->AssessmentOffice->conditions($conditions, $this->passedArgs); 
		$this->set('assessmentOffices', $this->paginate());
	}

	public function view($id = false) 
	{
		$this->AssessmentOffice->recursive = 0;
		if(!$assessmentOffice = $this->AssessmentOffice->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Office')));
		}
		$this->set('assessmentOffice', $assessmentOffice);
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->AssessmentOffice->create();
			
			if ($this->AssessmentOffice->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('Office')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Office')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$assessmentOffice = $this->AssessmentOffice->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Office')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->AssessmentOffice->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Office')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Office')));
			}
		}
		else
		{
			$this->request->data = $assessmentOffice;
		}
	}
}

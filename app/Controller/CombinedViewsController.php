<?php
App::uses('AppController', 'Controller');

class CombinedViewsController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = [
			'CombinedView.user_id' => AuthComponent::user('id')
		];
		
		$this->paginate['conditions'] = $this->CombinedView->conditions($conditions, $this->passedArgs); 
		$this->set('combinedViews', $this->paginate());
	}

	public function view($id = false) 
	{
		$this->CombinedView->recursive = 0;
		if(!$combinedView = $this->CombinedView->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('View')));
		}
		$this->set('combinedView', $combinedView);
	}
	
	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->CombinedView->create();
			$this->request->data['CombinedView']['user_id'] = AuthComponent::user('id');
			
			if ($this->CombinedView->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('View')));
				$this->bypassReferer = true;
				return $this->redirect(['action' => 'index']);
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('View')));
			}
		}
	}
	
	public function edit($id = null) 
	{
		if (!$combinedView = $this->CombinedView->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('View')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CombinedView->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('View')));
				return $this->redirect(['action' => 'index']);
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('View')));
			}
		}
		else
		{
			$this->request->data = $combinedView;
		}
	}
	
	public function delete($id = null) 
	{
		$this->CombinedView->id = $id;
		if (!$this->CombinedView->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('View')));
		}
		$this->bypassReferer = true;
		if ($this->CombinedView->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('View')));
			$this->redirect(['action' => 'index']);
		}
		$this->Flash->error(__('%s was not deleted', __('View')));
		$this->redirect(['action' => 'index']);
	}
	
	public function add_categories($id = null)
	{
		if (!$combinedView = $this->CombinedView->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('View')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CombinedView->addCategories($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been updated.', __('View')));
				$this->bypassReferer = true;
				return $this->redirect(['action' => 'view', $id, 'tab' => 'categories']);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated, reason: %s', __('View'), $this->CombinedView->modelError));
			}
		}
		else
		{
			$this->request->data = $combinedView;
		}
		
		$categories = $this->CombinedView->listAvailableCategories(AuthComponent::user('id'), $id);
		
		$this->set(compact(['combinedView', 'categories']));
	}
	
	public function add_reports($id = null)
	{
		if (!$combinedView = $this->CombinedView->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('View')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CombinedView->addReports($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been updated.', __('View')));
				$this->bypassReferer = true;
				return $this->redirect(['action' => 'view', $id, 'tab' => 'reports']);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated, reason: %s', __('View'), $this->CombinedView->modelError));
			}
		}
		else
		{
			$this->request->data = $combinedView;
		}
		
		$reports = $this->CombinedView->listAvailableReports(AuthComponent::user('id'), $id);
		
		$this->set(compact(['combinedView', 'reports']));
	}
}

<?php

App::uses('ContactsFismaSystemsController', 'Contacts.Controller');

class FismaSystemsController extends ContactsFismaSystemsController
{
	public $uses = array('FismaSystem');
	
	public function report($id = false)
	{
		if(!$report = $this->FismaSystem->OwnerContact->Report->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->set('report', $report);
		
		if(!$conditions = $this->FismaSystem->correlateCorRToFismaSystems($report))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function category($id = false)
	{
		if(!$category = $this->FismaSystem->OwnerContact->Category->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->set('category', $category);
		
		if(!$conditions = $this->FismaSystem->correlateCorRToFismaSystems($category))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
}

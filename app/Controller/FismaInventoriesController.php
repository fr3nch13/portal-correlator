<?php

App::uses('ContactsFismaInventoriesController', 'Contacts.Controller');

class FismaInventoriesController extends ContactsFismaInventoriesController
{
	public $uses = array('FismaInventory');
	
	public function report($id = false)
	{
		if(!$report = $this->FismaInventory->FismaSystem->OwnerContact->Report->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->set('report', $report);
		
		if(!$conditions = $this->FismaInventory->correlateCorRToFismaInventories($report))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function category($id = false)
	{
		if(!$category = $this->FismaInventory->FismaSystem->OwnerContact->Category->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->set('category', $category);
		
		if(!$conditions = $this->FismaInventory->correlateCorRToFismaInventories($category))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
}

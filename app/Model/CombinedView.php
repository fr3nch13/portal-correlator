<?php
App::uses('AppModel', 'Model');

class CombinedView extends AppModel 
{
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
	];
	
	public $hasAndBelongsToMany = [
		'Category' => [
			'className' => 'Category',
			'joinTable' => 'combined_view_categories',
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'combined_view_id',
			'unique' => 'keepExisting',
			'with' => 'CombinedViewCategory',
		],
		'Report' => [
			'className' => 'Report',
			'joinTable' => 'combined_view_reports',
			'foreignKey' => 'report_id',
			'associationForeignKey' => 'combined_view_id',
			'unique' => 'keepExisting',
			'with' => 'CombinedViewReport',
		],
	];
	
	
/***** Categories *****/
	public function listAvailableCategories($user_id = false, $combined_view_id = false)
	{
		if(!$user_id)
			$user_id = AuthComponent::user('id');
		
		$conditions = [];
		if($combined_view_id) // remove all of the ones allready assigned to this view
		{
			if($currentIds = $this->categoryIds($combined_view_id, true))
			{
				$conditions['Category.id NOT IN'] = $currentIds;
			}
		}
		
		return $this->Category->listforUser($user_id, $conditions);
	}
	
	public function categoryIds($combined_view_id = false)
	{
		return $this->CombinedViewCategory->listForView($combined_view_id, true);
	}
	
	public function addCategories($requestData = [])
	{
		$this->modelError = false;
		if(!isset($requestData[$this->alias]))
		{
			$this->modelError = __('addCategories: Unknown format.');
			return false;
		}
		
		if(!isset($requestData[$this->alias]['id']) or !$requestData[$this->alias]['id'])
		{
			$this->modelError = __('addCategories: Unknown %s id.', __('View'));
			return false;
		}
		$id = $requestData[$this->alias]['id'];
		
		if(!isset($requestData[$this->alias]['categories']) or !is_array($requestData[$this->alias]['categories']) or !$requestData[$this->alias]['categories'])
		{
			$this->modelError = __('addCategories: No %s were selected.', __('Categories'));
			return false;
		}
		$ids = $requestData[$this->alias]['categories'];
		
		$existing = $this->CombinedViewCategory->listForView($id, true);
			
		if(!$existing) $existing = [];
			
		// get just the new ones
		$ids = array_diff($ids, $existing);
		
		// build the proper save array
		$data = [];
			
		foreach($ids as $object_id)
		{
			$data[$object_id] = array('category_id' => $object_id, 'combined_view_id' => $id, 'active' => 1);
		}
			
		if(!empty($data))
		{
			return $this->CombinedViewCategory->saveMany($data);
		}
		return true;
	}
	
/***** Reports *****/
	public function listAvailableReports($user_id = false, $combined_view_id = false)
	{
		if(!$user_id)
			$user_id = AuthComponent::user('id');
		
		$conditions = [];
		if($combined_view_id) // remove all of the ones allready assigned to this view
		{
			if($currentIds = $this->reportIds($combined_view_id, true))
			{
				$conditions['Report.id NOT IN'] = $currentIds;
			}
		}
		
		return $this->Report->listforUser($user_id, $conditions);
	}
	
	public function reportIds($combined_view_id = false)
	{
		return $this->CombinedViewReport->listForView($combined_view_id, true);
	}
	
	public function addReports($requestData = [])
	{
		$this->modelError = false;
		if(!isset($requestData[$this->alias]))
		{
			$this->modelError = __('addReports: Unknown format.');
			return false;
		}
		
		if(!isset($requestData[$this->alias]['id']) or !$requestData[$this->alias]['id'])
		{
			$this->modelError = __('addReports: Unknown %s id.', __('View'));
			return false;
		}
		$id = $requestData[$this->alias]['id'];
		
		if(!isset($requestData[$this->alias]['reports']) or !is_array($requestData[$this->alias]['reports']) or !$requestData[$this->alias]['reports'])
		{
			$this->modelError = __('addReports: No %s were selected.', __('Reports'));
			return false;
		}
		$ids = $requestData[$this->alias]['reports'];
		
		$existing = $this->CombinedViewReport->listForView($id, true);
			
		if(!$existing) $existing = [];
			
		// get just the new ones
		$ids = array_diff($ids, $existing);
		
		// build the proper save array
		$data = [];
			
		foreach($ids as $object_id)
		{
			$data[$object_id] = array('report_id' => $object_id, 'combined_view_id' => $id, 'active' => 1);
		}
			
		if(!empty($data))
		{
			return $this->CombinedViewReport->saveMany($data);
		}
		return true;
	}
}
<?php
App::uses('AppModel', 'Model');

class CombinedViewCategory extends AppModel 
{
	public $belongsTo = [
		'CombinedView' => [
			'className' => 'CombinedView',
			'foreignKey' => 'combined_view_id',
		],
		'Category' => [
			'className' => 'Category',
			'foreignKey' => 'category_id',
		],
	];
	
	public function listForView($combined_view_id = false, $justIds = false)
	{
		$categories = $this->find('list', [
			'conditions' => [
				$this->alias. '.combined_view_id' => $combined_view_id,
			],
			'fields' => [$this->alias.'.category_id', $this->alias.'.category_id']
		]);
		
		if($justIds)
			return $categories;
		
		return $this->Category->find('list', [
			'conditions' => ['Category.id' => $categories]
		]);
	}
}
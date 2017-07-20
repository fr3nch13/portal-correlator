<?php
App::uses('AppModel', 'Model');

class CombinedViewReport extends AppModel 
{
	public $belongsTo = [
		'CombinedView' => [
			'className' => 'CombinedView',
			'foreignKey' => 'combined_view_id',
		],
		'Report' => [
			'className' => 'Report',
			'foreignKey' => 'report_id',
		],
	];
	
	public function listForView($combined_view_id = false, $justIds = false)
	{
		$reports = $this->find('list', [
			'conditions' => [
				$this->alias. '.combined_view_id' => $combined_view_id,
			],
			'fields' => [$this->alias.'.report_id', $this->alias.'.report_id']
		]);
		
		if($justIds)
			return $reports;
		
		return $this->Report->find('list', [
			'conditions' => ['Report.id' => $reports]
		]);
	}
}
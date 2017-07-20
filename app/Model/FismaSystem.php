<?php
App::uses('AppModel', 'Model');
App::uses('ContactsFismaSystem', 'Contacts.Model');

class FismaSystem extends ContactsFismaSystem 
{
	public $hasMany = [
		'FismaInventory' => [
			'className' => 'FismaInventory',
			'foreignKey' => 'fisma_system_id',
		],
	];
	
	public $actsAs = [
		'Correlation',
	];
	
	public $searchFields = [
		'FismaSystem.name',
		'FismaSystem.fullname',
	];
}

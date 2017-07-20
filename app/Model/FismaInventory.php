<?php
App::uses('AppModel', 'Model');
App::uses('ContactsFismaInventory', 'Contacts.Model');

class FismaInventory extends ContactsFismaInventory 
{
	public $belongsTo = [
		'FismaSystem' => [
			'className' => 'FismaSystem',
			'foreignKey' => 'fisma_system_id',
		],
	];
	
	public $actsAs = [
		'Correlation',
	];
	
	public $searchFields = [
		'FismaInventory.nat_ip_address',
		'FismaInventory.ip_address',
		'FismaInventory.mac_address',
		'FismaInventory.dns_name',
		'FismaInventory.asset_tag',
	];
}

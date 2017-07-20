<?php
App::uses('AppModel', 'Model');
App::uses('ContactsAdAccount', 'Contacts.Model');

class AdAccount extends ContactsAdAccount 
{
	public $hasMany = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'ad_account_id',
			'dependent' => false,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'ad_account_id',
			'dependent' => false,
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'ad_account_id',
			'dependent' => false,
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'ad_account_id',
			'dependent' => false,
		),
		'FismaSystem' => array(
			'className' => 'FismaSystem',
			'foreignKey' => 'owner_contact_id',
			'dependent' => false,
		),
	);
}

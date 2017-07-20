<?php
App::uses('AppModel', 'Model');
/**
 * WhoisLog Model
 *
 * @property Hostname $Hostname
 * @property Ipaddress $Ipaddress
 */
class WhoisLog extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Whois' => array(
			'className' => 'Whois',
			'foreignKey' => 'whois_id',
		),
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
	);
}

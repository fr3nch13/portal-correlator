<?php
App::uses('AppModel', 'Model');
/**
 * NslookupLog Model
 *
 * @property Hostname $Hostname
 * @property Ipaddress $Ipaddress
 */
class NslookupLog extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Nslookup' => array(
			'className' => 'Nslookup',
			'foreignKey' => 'nslookup_id',
		),
		'NslookupHostname' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_hostname_id',
		),
		'NslookupIpaddress' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_ipaddress_id',
		),
	);
	
	function getAverageTTL($nslookup_id = false)
	{
		// get all ttls for this nslookup history
		$ttls = $this->find('list', array(
			'conditions' => array('NslookupLog.nslookup_id' => $nslookup_id),
			'fields' => array('NslookupLog.id', 'NslookupLog.ttl'),
			'order' => array('NslookupLog.created' => 'desc'),
		));
	}
}

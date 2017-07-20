<?php
App::uses('AppModel', 'Model');
/**
 * Ipaddress Model
 *
 * @property Nameserver $Nameserver
 */
class Nameserver extends AppModel 
{

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasAndBelongsToMany = array(
		'Whois' => array(
			'className' => 'Whois',
			'joinTable' => 'whois_nameservers',
			'foreignKey' => 'whois_id',
			'associationForeignKey' => 'nameserver_id',
			'unique' => 'keepExisting',
			'with' => 'WhoisNameserver',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Nameserver.nameserver',
	);
	
//
	public function saveMany($data = array(), $options = array())
	{
	/*
	 * Filter out the nameservers that already exist based on the nameserver column
	 */
	 	$return = false;
	 	
	 	// reset the ids array
	 	$this->saveManyIds = array();
	 	
	 	if($data)
	 	{
	 		// find the existing nameservers
	 		$nameservers = array_keys($data);
	 		$existing = $this->find('all', array(
	 			'recursive' => -1,
				'conditions' => array('Nameserver.nameserver' => $nameservers),
			));
			
			// some do exist, filter them out
			if($existing)
			{	
				// update the existing ones from the current data set
				foreach($existing as $item)
				{
					$nameserver = $item['Nameserver']['nameserver'];
					if(isset($data[$nameserver]))
					{
						$data[$nameserver]['id'] = $item['Nameserver']['id'];
					}
					$this->saveManyIds[$nameserver] = $item['Nameserver']['id'];
				}
			}
			
			// add the new ones, and update the old ones
			if($data)
			{
				$return = parent::saveMany($data);
				
				// get the ids of the new records
				if($return)
				{
					$new = $this->find('list', array(
						'recursive' => -1,
						'fields' => array('Nameserver.nameserver', 'Nameserver.id'),
						'conditions' => array('Nameserver.nameserver' => array_keys($data)),
					));
					if($new)
					{
						foreach($new as $nameserver => $nameserver_id)
						{
							$this->saveManyIds[$nameserver] = $nameserver_id;
						}
					}
				}
			}
	 	}
		return $return;
	}
}
	
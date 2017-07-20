<?php
App::uses('AppModel', 'Model');
/**
 * WhoisNameserver Model
 *
 * @property Upload $Upload
 * @property Nameserver $Nameserver
 */
class WhoisNameserver extends AppModel 
{

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
		'Nameserver' => array(
			'className' => 'Nameserver',
			'foreignKey' => 'nameserver_id',
		),
	);
	
	
	// define the fields that can be searched
	public $searchFields = array(
		'Whois.registrarName',
		'Whois.contactEmail',
		'Nameserver.nameserver',
	);
	
	public function saveAssociations($whois_id = false, $nameserver_ids = array())
	{
	/*
	 * Saves associations between a upload and vectors
	 * 
	 */
			// remove the existing records (incase they add a vector that is already associated with this upload)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('WhoisNameserver.id', 'WhoisNameserver.nameserver_id'),
				'conditions' => array(
					'WhoisNameserver.whois_id' => $whois_id,
				),
			));
			
			// get just the new ones
			$nameserver_ids = array_diff($nameserver_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($nameserver_ids as $nameserver => $nameserver_id)
			{
				$data[$nameserver] = array('whois_id' => $whois_id, 'nameserver_id' => $nameserver_id);
			}
			return $this->saveMany($data);
	}
	
	function add($data)
	{
	/*
	 * Save relations with a upload
	 */
		if(isset($data[$this->alias]['nameservers']) and isset($data[$this->alias]['whois_id']))
		{
			$_nameservers = $data[$this->alias]['nameservers'];
			
			if(is_string($data[$this->alias]['nameservers']))
			{
				$_nameservers = split("\n", trim($data[$this->alias]['nameservers']));
			}
			
			// clean them up and format them for a saveMany()
			$nameservers = array();
			foreach($_nameservers as $i => $nameserver)
			{
				$nameserver = strtolower(trim($nameserver));
				if(!$nameserver) continue;
				$nameservers[$nameserver] = array('nameserver' => $nameserver); // format and make unique
			}
			
			// save only the new vectors
			$this->Nameserver->saveMany($nameservers);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['whois_id'], $this->Nameserver->saveManyIds);
		}
		return true;
	}
}
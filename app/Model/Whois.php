<?php
App::uses('AppModel', 'Model');
/**
 * Ipaddress Model
 *
 * @property Vector $Vector
 */
class Whois extends AppModel 
{
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
	);
	
	public $hasMany = array(
		'WhoisLog' => array(
			'className' => 'WhoisLog',
			'foreignKey' => 'whois_id',
			'dependent' => true,
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Nameserver' => array(
			'className' => 'Nameserver',
			'joinTable' => 'whois_nameservers',
			'foreignKey' => 'whois_id',
			'associationForeignKey' => 'nameserver_id',
			'unique' => 'keepExisting',
			'with' => 'WhoisNameserver',
		),
	);
	
	// add plugins and other behaviors
	public $actsAs = [
		'Utilities.Whois', 
		'Utilities.Whoiser',
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
			],
		],
	];
	
	// switch when adding a vector source from a pre-existing whois record
	public $recordCreatedDate = false;
	
	// define the fields that can be searched
	public $searchFields = array(
		'Whois.registrarName',
		'Whois.contactEmail',
		'Vector.vector',
	);
	
	// used with the cron job
	public $addUpdate = 0;
	
	// used with the cron job
	public $final_results = false;
	
	// used with the cron to track all of the hostnames that have been updated
	public $updated_vector_ids = array();
	
	public $update_results = false;
	
	public function updateWhois($vector_id = false, $whois_auto_lookup = 0, $automatic = false)
	{
	 	// reset the tracking array
	 	$this->updated_vector_ids = array();
	 
	 	if(!$vector_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, 'whois');
	 		return false;
	 	}
	 	
	 	$this->Vector->recursive = -1;
		if(!$vector = $this->Vector->read(null, $vector_id))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_id);
	 		$this->shellOut($this->modelError, 'whois');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector['Vector']['vector']), 'whois');
		
		$results = $this->Whois_records($vector['Vector']['vector']); // always returns an array, even if empty
		
		// downgrade $whois_auto_lookup by 1
		$whois_auto_lookup_original = $whois_auto_lookup;
		if($whois_auto_lookup)
		{
			if($whois_auto_lookup == 3) 
				$whois_auto_lookup = 0;
		}
		if(!$whois_auto_lookup) $whois_auto_lookup = 0;
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $details)
			{
				if($vector['Vector']['type'] == 'ipaddress')
				{
					$this->checkAdd($vector_id, $source, $details);
				}
				elseif($vector['Vector']['type'] == 'hostname')
				{
					// try to figure out the domain name
					if(trim($details['tld']) == '')
					{
						$parts = explode('.', $vector['Vector']['vector']);
						array_shift($parts);
						if(count($parts) > 1)
						{
							$details['tld'] = implode('.', $parts);
						}
					}
					
					//update the record in the whois table
					$this->updateAllRecords($vector_id, $source, $details);
					$this->Vector->updated_vector_ids = $this->updated_vector_ids;
				}
				
				// add a vector source entry
				$this->Vector->VectorSource->add($vector_id, 'whois', $source, $this->recordCreatedDate, $this->alias. '::updateWhois');
				
				$result_count++;
				$transaction_sources[$source] = $source;
			}
		}
		
		// mark the dates in the ipaddress record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
			
		// update the $whois_auto_lookup on the hostnames table
		if($vector['Vector']['type'] == 'ipaddress')
		{
			$this->Vector->Ipaddress->updateWhoisLookupLevel($vector_id, $whois_auto_lookup);
			$this->Vector->Ipaddress->updateWhoisDates($vector_id, true, $updated, $whois_auto_lookup_original);
		}
		elseif($vector['Vector']['type'] == 'hostname')
		{
			$this->Vector->Hostname->updateWhoisLookupLevel($vector_id, $whois_auto_lookup);
			$this->Vector->Hostname->updateWhoisDates($vector_id, true, $updated, $whois_auto_lookup_original);
		}
		
		// track this transaction
		// add errors later
		$this->Vector->WhoisTransactionLog->addLog($vector_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		// return the results back
		
		return __('Added/updated %s Whois records for vector: %s', count($this->updated_vector_ids), $vector['Vector']['vector']);
	}
	
	public function updateAllRecords($vector_id = false, $source = false, $details = array())
	{
		if(!$vector_id)
		{
			$this->modelError = __('Unknown host.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		if(!$source)
		{
			$this->modelError = __('Unknown source.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		if(!$details)
		{
			$this->modelError = __('No details given.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		// update this record
		if(!$results = $this->checkAdd($vector_id, $source, $details))
		{
			$this->modelError = __('Initial record didn\'t update.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		$this->updated_vector_ids = array(
			$vector_id => $vector_id,
		);
		
		// add/update all of the records with this source and tld
		if(isset($details['tld']) and trim($details['tld']) != '')
		{
			$tld = '.'. $details['tld'];
			
			$this->shellOut(__('Updating records with the tld: %s', $tld), 'whois', 'info');
			
			$vectors = $this->Vector->find('all', array(
				'recursive' => 0,
				'contain' => array('Hostname', 'Ipaddress'),
				'conditions' => array(
					'Vector.id !=' => $vector_id,
					'or' => array(
						'Vector.vector LIKE ' => '%'. $tld,
						'Vector.vector' => $details['tld'],
					),
				),
			));
			
			$i = 0;
			foreach($vectors as $vector)
			{
				if(!$this->checkAdd($vector['Vector']['id'], $source, $details))
				{
					$this->modelError = __('Unable to add/update record for (%s) %s.', $vector['Vector']['id'], $vector);
					$this->shellOut($this->modelError, 'whois', 'error');
					continue;
				}
				
				// update their dates
				// update the $whois_auto_lookup on the hostnames table
				if($vector['Vector']['type'] == 'ipaddress')
				{
					$this->Vector->Ipaddress->updateWhoisLookupLevel($vector['Vector']['id'], $vector['Ipaddress']['whois_auto_lookup']);
					$this->Vector->Ipaddress->updateWhoisDates($vector['Vector']['id'], true, $this->recordUpdated, $vector['Ipaddress']['whois_auto_lookup']);
				}
				elseif($vector['Vector']['type'] == 'hostname')
				{
					$this->Vector->Hostname->updateWhoisLookupLevel($vector['Vector']['id'], $vector['Hostname']['whois_auto_lookup']);
					$this->Vector->Hostname->updateWhoisDates($vector['Vector']['id'], true, $this->recordUpdated, $vector['Hostname']['whois_auto_lookup']);
				}
				
				$vectorId = $vector['Vector']['id'];
				$this->updated_vector_ids[$vectorId] = $vectorId;
				$i++;
			}
			
			$this->shellOut(__('Added/Updated %s records - tld: %s - source: %s', $i, $tld, $source), 'whois', 'info');
		}
		return true;
	}
	
	public function checkAdd($vector_id = false, $source = false, $details = array())
	{
		if(!$vector_id)
		{
			$this->modelError = __('Unknown host.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		if(!$source)
		{
			$this->modelError = __('Unknown source.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		if(!$details)
		{
			$this->modelError = __('No details given.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		$tld = (isset($details['tld'])?$details['tld']:'');
		
		// for later use
		$this->recordUpdated = false;
		$return = false;
////
		$record = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'Whois.vector_id' => $vector_id,
				'Whois.source' => $source,
				'Whois.tld' => $tld,
			),
		));
		
		// new
		if(!$record)
		{
			$this->create();
			$this->data = $details;
			$this->data['vector_id'] = $vector_id;
			$this->data['source'] = $source;
			
			$this->data['whois_checked'] = date('Y-m-d H:i:s');
			$this->data['whois_updated'] = date('Y-m-d H:i:s');
			
			if(!$return = $this->save($this->data))
			{
				$this->modelError = __('Unable to add a new record');
				return false;
			}
			
			$whois_id = $this->id;
			$this->recordNew = true;
			$this->recordCreatedDate = (isset($data['created'])?$data['created']:date('Y-m-d H:i:s'));
			
			// if name servers, add them as well
			if(isset($details['nameServers']) and is_array($details['nameServers']) and !empty($details['nameServers']))
			{
				$data = array(
					'WhoisNameserver' => array(
						'nameservers' => $details['nameServers'],
						'whois_id' => $this->id,
					),
				);
				$this->WhoisNameserver->add($data);
			}
			$this->addUpdate = 1;
		}
		// existing
		else
		{
			$whois_id = $record[$this->alias]['id'];
			$this->id = $whois_id;
			
			$this->data = array();
			$this->data['whois_checked'] = date('Y-m-d H:i:s');
			$this->recordCreatedDate = $this->field('created');
			
			if($record['Whois']['sha1'] != $details['sha1'])
			{
				$this->recordUpdated = true;
				$this->data = $details;
				$this->data['whois_updated'] = date('Y-m-d H:i:s');
			
				// if name servers, update them as well
				if(isset($details['nameServers']) and is_array($details['nameServers']) and !empty($details['nameServers']))
				{
					$data = array(
						'WhoisNameserver' => array(
							'nameservers' => $details['nameServers'],
							'whois_id' => $this->id,
						),
					);
					$this->WhoisNameserver->add($data);
				}
				$this->addUpdate = 2;
			}
			
			if(!$return = $this->save($this->data))
			{
				$this->modelError = __('Unable to update an existing record');
				$this->shellOut($this->modelError, 'whois', 'error');
				return false;
			}
		}
		
		if($return)
		{
			// record a transaction log for this
			$this->WhoisLog->create();
			$this->WhoisLog->data = array_merge(array(
				'whois_id' => $this->id,
				'vector_id' => $vector_id,
				'source' => $source,
				'tld' => $tld,
			), $details);
			
			$this->WhoisLog->save($this->WhoisLog->data);
		}
		
		return $return;
	}
	
	public function lookupVector($vector = '', $force_update = false)
	{
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
		if(!$vector_id = $this->Vector->field('id', array('vector' => $vector)))
		{
			$this->modelError = __('Unknown Vector ID');
			return false;
		}
		
		return $this->lookupVectorId($vector_id, $vector, $force_update);
	}
	
	public function lookupVectorId($vector_id = false, $vector = false, $force_update = false)
	{
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector Id');
			return false;
		}
		
		if(!$vector)
		{
			$this->Vector->id = $vector_id;
			if(!$vector = $this->Vector->field('vector'))
			{
				$this->modelError = __('Unknown Vector');
				return false;
			}
		}
		
		return $this->checkAdd($vector_id, $vector, $force_update);
	}
	
	public function dashboardOverviewStats()
	{
		$stats = array(
			'total' => array('name' => __('Total'), 'value' => $this->find('count')),
		);
		
		return $stats;
	}
}

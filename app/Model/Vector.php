<?php
App::uses('AppModel', 'Model');
/**
 * Vector Model
 *
 */
class Vector extends AppModel 
{

	public $displayField = 'vector';
	
	public $validate = array(
		'vector' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Category' => array(
			'className' => 'Category',
			'joinTable' => 'categories_vectors',
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesVector',
		),
		'Report' => array(
			'className' => 'Report',
			'joinTable' => 'reports_vectors',
			'foreignKey' => 'report_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsVector',
		),
		'Upload' => array(
			'className' => 'Upload',
			'joinTable' => 'uploads_vectors',
			'foreignKey' => 'upload_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'UploadsVector',
		),
		'Dump' => array(
			'className' => 'Dump',
			'joinTable' => 'dumps_vectors',
			'foreignKey' => 'dump_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'DumpsVector',
		),
		'Import' => array(
			'className' => 'Import',
			'joinTable' => 'imports_vectors',
			'foreignKey' => 'import_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'ImportsVector',
		),
	);
	
	public $hasMany = array(
		'NslookupHostname' => array(
			'className' => 'Nslookup',
			'foreignKey' => 'vector_hostname_id',
			'dependent' => true,
		),
		'NslookupHostnameLog' => array(
			'className' => 'NslookupLog',
			'foreignKey' => 'vector_hostname_id',
			'dependent' => true,
		),
		'NslookupIpaddress' => array(
			'className' => 'Nslookup',
			'foreignKey' => 'vector_ipaddress_id',
			'dependent' => true,
		),
		'NslookupIpaddressLog' => array(
			'className' => 'NslookupLog',
			'foreignKey' => 'vector_ipaddress_id',
			'dependent' => true,
		),
		'DnsTransactionLog' => array(
			'className' => 'DnsTransactionLog',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'VectorSource' => array(
			'className' => 'VectorSource',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'Whois' => array(
			'className' => 'Whois',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'WhoisLog' => array(
			'className' => 'WhoisLog',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'WhoisTransactionLog' => array(
			'className' => 'WhoisTransactionLog',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'VtNtRecordLookup' => array(
			'className' => 'VtNtRecord',
			'foreignKey' => 'vector_lookup_id',
		),
		'VtNtRecordSrc' => array(
			'className' => 'VtNtRecord',
			'foreignKey' => 'vector_src_id',
		),
		'VtNtRecordDst' => array(
			'className' => 'VtNtRecord',
			'foreignKey' => 'vector_dst_id',
		),
		'VtRelatedSampleLookup' => array(
			'className' => 'VtRelatedSample',
			'foreignKey' => 'vector_lookup_id',
		),
		'VtRelatedSampleSample' => array(
			'className' => 'VtRelatedSample',
			'foreignKey' => 'vector_sample_id',
		),
		'VtDetectedUrlLookup' => array(
			'className' => 'VtDetectedUrl',
			'foreignKey' => 'vector_lookup_id',
		),
		'VtDetectedUrlUrl' => array(
			'className' => 'VtDetectedUrl',
			'foreignKey' => 'vector_url_id',
		),
	);
	
	public $hasOne = array(
		'VectorDetail' => array(
			'className' => 'VectorDetail',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'Hostname' => array(
			'className' => 'Hostname',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'Ipaddress' => array(
			'className' => 'Ipaddress',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'HashSignature' => array(
			'className' => 'HashSignature',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'Geoip' => array(
			'className' => 'Geoip',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
		'VectorSourceFirst' => array(
			'className' => 'VectorSource',
			'foreignKey' => 'vector_id',
			'dependent' => true,
            'conditions' => array('VectorSourceFirst.first' => true)
		),
		'VectorSourceLast' => array(
			'className' => 'VectorSource',
			'foreignKey' => 'vector_id',
			'dependent' => true,
            'conditions' => array('VectorSourceLast.last' => true)
		),
		'WhoisLast' => array(
			'className' => 'Whois',
			'foreignKey' => 'vector_id',
			'dependent' => true,
            'order' => array('WhoisLast.created' => 'desc', 'WhoisLast.modified' => 'desc')
		),
		'WhoiserTransaction' => array(
			'className' => 'WhoiserTransaction',
			'foreignKey' => 'vector_id',
			'dependent' => true,
		),
	);
	
	public $belongsTo = array(
		'VectorType' => array(
			'className' => 'VectorType',
			'foreignKey' => 'vector_type_id',
			'plugin_snapshot' => true,
		),
		'VectorTypeUser' => array(
			'className' => 'User',
			'foreignKey' => 'user_vtype_id',
		),
	);
	
	public $actsAs = [
		'Tags.Taggable', 
		'Utilities.Nslookup', 
		'Utilities.VirusTotal',
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
			],
		],
	];
	
	public $filterArgs = array(
			array('name' => 'q', 'type' => 'query', 'method' => 'orConditions'),
			array('name' => 'auto_lookup', 'type' => 'value', 'field' => 'Hostname.dns_auto_lookup'),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Vector.vector',
		'Vector.type',
		'VectorSourceFirst.source_type',
		'VectorSourceLast.source_type',
	);
	
	public $saveManyIds = array();
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'bad', 'notbad', 'type', 'multitype');
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('bad');
	
	// used to track the dnsdbapi count
	public $dnsdbapi_stats = array();
	
	// true if all available keys are used
	public $dnsdbapi_none = false;
	
	// used with the cron to track all of the hostnames that have been updated
	public $updated_vector_ids = array();
	
	// used to track the bad state for vector groups incase we're updating a bunch of vectors
	public $vector_types_bad_states = array();
	
	// user id of the person trying to push a whois update
	// mainly only applied to whoiser and WhoiserTransactions Model
	public $whois_user_id = 0;
	
	// used with validateType() below
	public $found_type = false;
	
	// used to track vector ids that we are using with VirusTotal report processing
	public $vt_vector_ids = array();
	
	// used to track who initiated VirusTotal lookup
	public $vt_user_id = 0;
	
	public $hex_balance = false; // track the hexillion balance 
	
	public $checkAddCache = array();
	
	public function beforeSave($options = array())
	{
		if(isset($this->data[$this->alias]['vector']) and !isset($this->data[$this->alias]['type']))
		{
			$this->data[$this->alias]['type'] = $this->EX_discoverType($this->data[$this->alias]['vector']);
		}
		
		if(isset($this->data[$this->alias]['vector_type_id']) and !$this->data[$this->alias]['vector_type_id'])
		{
			$this->data[$this->alias]['vector_type_id'] = '0';
		}
		
		// mimic the bad state of the vector group it's in, ONLY if this vector is a new vector
		if(!isset($this->data[$this->alias]['id']) and isset($this->data[$this->alias]['vector_type_id']) and !isset($this->data[$this->alias]['bad']))
		{
			// get the vector type's benign state
			$this->data[$this->alias]['bad'] = (int)$this->VectorType->field('bad', array('id' => $this->data[$this->alias]['vector_type_id']));
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// make sure there is a proper alternate record for types like hostnames/ipaddresses, etc
		if(isset($this->data[$this->alias]['type']) and (isset($this->data[$this->alias]['id']) or $this->id))
		{
			if(!isset($this->data[$this->alias]['id']))
			{
				$this->data[$this->alias]['id'] = $this->id;
			}
			
			$dns_auto_lookup = 0;
			if(isset($this->data[$this->alias]['dns_auto_lookup'])) $dns_auto_lookup = $this->data[$this->alias]['dns_auto_lookup'];
			
			$hexillion_auto_lookup = 0;
			if(isset($this->data[$this->alias]['hexillion_auto_lookup'])) $hexillion_auto_lookup = $this->data[$this->alias]['hexillion_auto_lookup'];
			
			$whois_auto_lookup = 0;
			if(isset($this->data[$this->alias]['whois_auto_lookup'])) $whois_auto_lookup = $this->data[$this->alias]['whois_auto_lookup'];
			
			$source = false;
			if(isset($this->data[$this->alias]['source'])) $source = $this->data[$this->alias]['source'];
			
			$subsource = false;
			if(isset($this->data[$this->alias]['subsource'])) $subsource = $this->data[$this->alias]['subsource'];
			
			$subsource2 = false;
			if(isset($this->data[$this->alias]['subsource2'])) $subsource2 = $this->data[$this->alias]['subsource2'];
			

			if($this->data[$this->alias]['type'] == 'hostname')
			{
				// check/add a hostname record
				$this->Hostname->checkAddBlank($this->data[$this->alias]['id'], $dns_auto_lookup, $source, $subsource, $subsource2, $whois_auto_lookup, $hexillion_auto_lookup);
			}
			// make sure there is a proper alternate record for types like hostnames/ipaddresses, etc
			elseif($this->data[$this->alias]['type'] == 'ipaddress')
			{
				// check/add a hostname record
				$this->Ipaddress->checkAddBlank($this->data[$this->alias]['id'], $dns_auto_lookup, $source, $subsource, $subsource2, $whois_auto_lookup, $hexillion_auto_lookup);
				
				// check/add the geoip record
				$vector = (isset($this->data[$this->alias]['vector'])?$this->data[$this->alias]['vector']:false);
				$this->Geoip->lookupVectorId($this->data[$this->alias]['id'], $vector);
			}
			elseif($this->EX_isHash($this->data[$this->alias]['type']))
			{
			
				// check/add a hash record
				$this->HashSignature->checkAddBlank($this->data[$this->alias]['id'], array(
					'type' => $this->data[$this->alias]['type'],
				));
			}
			
			$vt_lookup = 0;
			if(isset($this->data[$this->alias]['vt_lookup']))
			{
				$vt_lookup = $this->data[$this->alias]['vt_lookup'];
				$vd_data = array(
					'vt_lookup' => $vt_lookup,
				);
				$this->VectorDetail->checkAddUpdate($this->data[$this->alias]['id'], $vd_data);
			}
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function beforeFind($query = array())
	{
		return parent::beforeFind($query);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		// checking to make sure this vector has a related detail, only if it's requested
		foreach($results as $i => $result)
		{
			if(!isset($result[$this->VectorDetail->alias])) continue;
			if(!isset($result[$this->VectorDetail->alias]['id'])) continue;
			if(!$result[$this->VectorDetail->alias]['id'])
			{
				if($vector_detail_id = $this->VectorDetail->checkAddUpdate($result[$this->alias]['id']))
				{
					$results[$i][$this->VectorDetail->alias]['id'] = $vector_detail_id;
					$results[$i][$this->VectorDetail->alias]['vector_id'] = $result[$this->alias]['id'];
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function saveMany($data = array(), $from_xref = false)
	{
	/*
	 * Filter out the vectors that already exist based on the vector column
	 */
	 	$return = false;
	 	
	 	// reset the ids array
	 	$this->saveManyIds = array();
	 	
	 	if($data)
	 	{
	 		// find the existing vectors
	 		$vectors = array_keys($data);
	 		$existing = $this->find('all', array(
	 			'recursive' => -1,
				'conditions' => array('Vector.vector' => $vectors),
			));
			
			// some do exist, filter them out
			if($existing)
			{
				// update the existing ones from the current data set
				foreach($existing as $item)
				{
					$vector = $item[$this->alias]['vector'];
					if(isset($data[$vector]))
					{
						unset($data[$vector]['vector']);
						$data[$vector]['id'] = $item[$this->alias]['id'];
						// don't allow overwriting of the global vector type for existing ones, when added to an object like a report, etc
						if($from_xref)
						{							unset($data[$vector]);
							if(isset($item[$this->alias]['vector_type_id'])) unset($data[$vector]['vector_type_id']);
							
						}
					}
					$this->saveManyIds[$vector] = $item[$this->alias]['id'];
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
						'fields' => array('Vector.vector', 'Vector.id'),
						'conditions' => array('Vector.vector' => array_keys($data)),
					));
					if($new)
					{
						foreach($new as $vector => $vector_id)
						{
							$this->saveManyIds[$vector] = $vector_id;
						}
					}
				}
			}
	 	}
		return $return;
	}
	
	public function reviewed($data = array())
	{
	/*
	 * Filter out the vectors that already exist based on the vector
	 */
	 	$reviewedIds = false;
	 	$existing = false;
	 	$new = false;
	 	
	 	if($data)
	 	{
	 		$reviewedIds = array();
	 		
	 		//
	 		$existing = $this->find('list', array(
				'fields' => array('Vector.vector', 'Vector.id'),
				'conditions' => array('Vector.vector' => array_keys($data)),
			));
			
			// some do exist, filter them out
			if($existing)
			{
				// track the existing vector_ids
				$reviewedIds = $existing;
				
				foreach(array_keys($existing) as $_vector)
				{
					if(isset($data[$_vector])) unset($data[$_vector]);
				}
			}
			
			// some are still new, unflatten the array
			if($data)
			{	
				$return = parent::saveMany($data);
				
				// get the ids of the new records
				if($return)
				{
					$new = $this->find('list', array(
						'fields' => array('Vector.vector', 'Vector.id'),
						'conditions' => array('Vector.vector' => array_keys($data)),
					));
					
					if($new)
					{
						// add the new ids to the id tracking
						$reviewedIds = array_merge($reviewedIds, $new);
					}
				}
			}
	 	}
		return $reviewedIds;
	}
	
	public function validateType($vector = false, $guessed_type = false)
	{
		// reset it
		$this->found_type = false;
		
		$vector = trim($vector);
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
		$this->found_type = $discovered_type = $this->EX_discoverType($vector);
		
		// compare for validation
		if($guessed_type)
		{
			$guessed_type = trim(strtolower($guessed_type));
			if($discovered_type !== $guessed_type)
			{
				return false;
			}
			return true;
		}
		else
		{
			return $discovered_type;
		}
	}
	
	public function fixType($vector = false, $newType = false, $automaic = true, $manual_type_user_id = 0)
	{
		$this->modelError = false;
		
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector - 1');
			return false;
		}
		
		// actually a vector_id, get the record
		if(is_string($vector) or is_int($vector))
		{
			$this->id = $vector;
	 		$this->recursive = 0;
	 		$this->contain('Hostname', 'Ipaddress');
			$vector = $this->read(null, $vector);
		}
		
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector - 2');
			return false;
		}
		
		// if it had been manually changed, leave it
		if($vector['Vector']['manual_type_user_id'] and $automaic)
		{
			$this->modelError = __('Manually set, we won\'t change it.');
			return $vector;
		}
		
		if(!$newType)
		{
			$newType = $this->validateType($vector['Vector']['vector']);
		}
		
		// if it hasn't changed, just return the vector
		if($newType === $vector['Vector']['type'])
		{
			$this->modelError = __('The old and the new type were the same');
			return $vector;
		}
		
		//// change things for the new type
		// if it's detected type is a hostname/ipaddress, then move some data
		if(in_array($newType, array('hostname', 'ipaddress')))
		{
			$fromKey = $toKey = false;
			// get the hostname record to move
			if($newType == 'hostname')
			{
				$fromKey = 'Ipaddress';
				$nsFromforeignKey = 'vector_ipaddress_id';
				$toKey = 'Hostname';
				$nsToforeignKey = 'vector_hostname_id';
			}
			elseif($newType == 'ipaddress')
			{
				$fromKey = 'Hostname';
				$nsFromforeignKey = 'vector_hostname_id';
				$toKey = 'Ipaddress';
				$nsToforeignKey = 'vector_ipaddress_id';
			}
			if(!$fromKey or !$toKey) 
			{
				return false;
			}
			
			// populate the new data from the old data
			$hostData = $vector[$fromKey];
			// add the vector id incase we're going from filename to hostname for example
			$hostData['vector_id'] = $vector['Vector']['id']; 
			
			$old_id = false;
			if(isset($hostData['id'])) 
			{
				if($hostData['id']) $old_id = $hostData['id'];
				unset($hostData['id']);
			}
			
			// remove and of the fields that are null/false
			foreach($hostData as $hk => $hv)
			{
				if(!trim($hv)) unset($hostData[$hk]);
			}
			
			// delete any previous records from the new data
			$this->{$toKey}->deleteAll(array($toKey. '.vector_id' => $vector['Vector']['id']));
			
			// create the new record from the old data
			$this->{$toKey}->create();
			$this->{$toKey}->data = $hostData;
			$this->{$toKey}->save($this->{$toKey}->data);
			
			// delete the record from the old data
			if($old_id)
			{
				$this->{$fromKey}->delete($old_id);
			}
			
			//move over all of the nslookup records, and nslookup logs
			$nsToKey = 'Nslookup'. $toKey;
			$nsFromKey = 'Nslookup'. $fromKey;
			
			// transfer over all of the nslookups
			$this->{$nsToKey}->updateAll(
				array($nsToKey.'.'.$nsToforeignKey => $vector['Vector']['id'], $nsToKey.'.'.$nsFromforeignKey => 0),
				array($nsToKey.'.'.$nsFromforeignKey => $vector['Vector']['id'])
			);
			
			// move over all of the nslookup logs
			$nsToKey .= 'Log';
			$nsFromKey .= 'Log';
			
			$this->{$nsToKey}->updateAll(
				array($nsToKey.'.'.$nsToforeignKey => $vector['Vector']['id'], $nsToKey.'.'.$nsFromforeignKey => 0),
				array($nsToKey.'.'.$nsFromforeignKey => $vector['Vector']['id'])
			);
			
		}
		//// remove the hostname/ip records, nslookup records, and nslookup log records if the old type was a hostname/ipaddress
		elseif(in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			// get the hostname record to move
			if($vector['Vector']['type'] == 'hostname')
			{
				$deleteKey = 'Hostname';
				$nsDeleteforeignKey = 'vector_hostname_id';
			}
			elseif($vector['Vector']['type'] == 'ipaddress')
			{
				$deleteKey = 'Ipaddress';
				$nsDeleteforeignKey = 'vector_ipaddress_id';
			}
			if(!$deleteKey) 
			{
				return false;
			}
			// delete the hostname/ipaddress record
			$this->{$deleteKey}->deleteAll(array($deleteKey. '.vector_id' => $vector['Vector']['id']));
			// delete the nslookup records
			$deleteKey = 'Nslookup'. $deleteKey;
			$this->{$deleteKey}->deleteAll(array($deleteKey. '.'. $nsDeleteforeignKey => $vector['Vector']['id']));
			// delete the nslookup log records
			$deleteKey .= 'Log';
			$this->{$deleteKey}->deleteAll(array($deleteKey. '.'. $nsDeleteforeignKey => $vector['Vector']['id']));
		}
		
		// update the vector to have the new type
		$this->id = $vector['Vector']['id'];
		$this->data = array('type' => $newType, 'manual_type_user_id' => $manual_type_user_id);
		if($this->save($this->data))
		{
			
	 		$this->recursive = 0;
	 		$this->contain('Hostname', 'Ipaddress');
			return $this->read(null, $vector['Vector']['id']);
		}
		return false;
	}
	
	public function removeDnsStuff($id = false)
	{
		if(!$id)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
		// delete the hostname/ipaddress record
		$this->Hostname->deleteAll(array('Hostname.vector_id' => $vector['Vector']['id']));
		$this->Ipaddress->deleteAll(array('Ipaddress.vector_id' => $vector['Vector']['id']));
		
		// delete the nslookup records
		$this->NslookupHostname->deleteAll(array('NslookupHostname.vector_hostname_id' => $vector['Vector']['id']));
		$this->NslookupIpaddress->deleteAll(array('NslookupIpaddress.vector_ipaddress_id' => $vector['Vector']['id']));
		
		// delete the nslookup log records
		$this->NslookupHostnameLog->deleteAll(array('NslookupHostnameLog.vector_hostname_id' => $vector['Vector']['id']));
		$this->NslookupIpaddressLog->deleteAll(array('NslookupIpaddressLog.vector_ipaddress_id' => $vector['Vector']['id']));
		
		return true;
	}
	
	public function updateDns($id = false, $dns_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Update the DNS for either a hostname or an ip address
	 * Mainly used in manually updating the vector from the website details page
	 * dns_auto_lookup = the lookup level of this vector
	 * automatic = if this was looked up automatically via the cron
	 */
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		// validate the type
		if(!$this->validateType($vector['Vector']['vector'], $vector['Vector']['type']))
		{
			$this->modelError = __('Vector is not a %s, detected as a %s', $vector['Vector']['type'], $this->found_type);
			$this->shellOut($this->modelError, 'nslookup');
			
			$vector_vector = $vector['Vector']['vector'];
			// fix the type
			if(!$vector = $this->fixType($vector))
			{
				$this->modelError = __('Unable to properly fix the vector type for: %s - %s', $id, $vector_vector);
				$this->shellOut($this->modelError, 'nslookup', 'error');
				return false;
			}
		}
		
		if(!in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			$this->modelError = __('Vector is neither a hostname, or ip address. (%s - %s - %s)', $vector['Vector']['vector'], $vector['Vector']['type'], $id);
			$this->shellOut($this->modelError, 'nslookup', 'warning');
			$this->removeDnsStuff($vector['Vector']['id']);
			return false;
		}
		
		if(!$dns_auto_lookup)
		{
			if($vector['Hostname']['id']) $dns_auto_lookup = $vector['Hostname']['dns_auto_lookup'];
		}
		if(!$dns_auto_lookup)
		{
			if($vector['Ipaddress']['id']) $dns_auto_lookup = $vector['Ipaddress']['dns_auto_lookup'];
		}
		
		$this->cronOut(__('Looking up: %s', $vector['Vector']['vector']), 'nslookup');
		
		if($vector['Vector']['type'] == 'hostname')
		{
			$results = $this->NslookupHostname->updateHostnameDNS($id, $dns_auto_lookup, $automatic);
			$this->hex_balance = $this->NslookupHostname->hex_balance;
			return $results;
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$results = $this->NslookupIpaddress->updateIpaddressDNS($id, $dns_auto_lookup, $automatic);
			$this->hex_balance = $this->NslookupHostname->hex_balance;
			return $results;
		}
		
		return false;
	}
	
	public function updateDnsDbapi($id = false, $dns_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Update the DNS for either a hostname or an ip address
	 * Mainly used in manually updating the vector from the website details page
	 * dns_auto_lookup = the lookup level of this vector
	 * automatic = if this was looked up automatically via the cron
	 */
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		// validate the type
		if(!$this->validateType($vector['Vector']['vector'], $vector['Vector']['type']))
		{
			$this->modelError = __('Vector is not a %s, detected as a %s', $vector['Vector']['type'], $this->found_type);
			$this->shellOut($this->modelError, 'dnsdbapi');
			
			$vector_vector = $vector['Vector']['vector'];
			// fix the type
			if(!$vector = $this->fixType($vector))
			{
				$this->modelError = __('Unable to properly fix the vector type for: %s - %s', $id, $vector_vector);
				$this->shellOut($this->modelError, 'dnsdbapi', 'error');
				return false;
			}
		}
		
		if(!in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			$this->modelError = __('Vector is neither a hostname, or ip address. (%s - %s - %s)', $vector['Vector']['vector'], $vector['Vector']['type'], $id);
			$this->shellOut($this->modelError, 'dnsdbapi', 'warning');
			$this->removeDnsStuff($vector['Vector']['id']);
			return false;
		}
		
		if(!$dns_auto_lookup)
		{
			if($vector['Hostname']['id']) $dns_auto_lookup = $vector['Hostname']['dns_auto_lookup'];
		}
		if(!$dns_auto_lookup)
		{
			if($vector['Ipaddress']['id']) $dns_auto_lookup = $vector['Ipaddress']['dns_auto_lookup'];
		}
		
		$this->cronOut(__('Looking up: %s', $vector['Vector']['vector']), 'dnsdbapi');
		
		$this->dnsdbapi_stats = array();
		$this->dnsdbapi_none = false;
		
		if($vector['Vector']['type'] == 'hostname')
		{
			$results = $this->NslookupHostname->updateHostnameDNS($id, $dns_auto_lookup, $automatic, true);
			$this->dnsdbapi_stats = $this->NslookupHostname->dnsdbapi_stats;
			$this->dnsdbapi_none = $this->NslookupHostname->dnsdbapi_none;
			return $results;
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$results = $this->NslookupIpaddress->updateIpaddressDNS($id, $dns_auto_lookup, $automatic, true);
			$this->dnsdbapi_stats = $this->NslookupIpaddress->dnsdbapi_stats;
			$this->dnsdbapi_none = $this->NslookupIpaddress->dnsdbapi_none;
			return $results;
		}
		
		return false;
	}
	
	public function updateVirusTotal($id = false, $auto_lookup_virustotal = 0, $automatic = false)
	{
	/*
	 * Update the DNS for either a hostname or an ip address
	 *** For the VirusTotal Reports, see updateVirusTotalReport();
	 * Mainly used in manually updating the vector from the website details page
	 * auto_lookup_virustotal = the lookup level of this vector
	 * automatic = if this was looked up automatically via the cron
	 */
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		// validate the type
		if(!$this->validateType($vector['Vector']['vector'], $vector['Vector']['type']))
		{
			$this->modelError = __('Vector is not a %s, detected as a %s', $vector['Vector']['type'], $this->found_type);
			$this->shellOut($this->modelError, 'virustotal');
			
			$vector_vector = $vector['Vector']['vector'];
			// fix the type
			if(!$vector = $this->fixType($vector))
			{
				$this->modelError = __('Unable to properly fix the vector type for: %s - %s', $id, $vector_vector);
				$this->shellOut($this->modelError, 'virustotal', 'error');
				return false;
			}
		}
		
		if(!in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			$this->modelError = __('Vector is neither a hostname, or ip address. (%s - %s - %s)', $vector['Vector']['vector'], $vector['Vector']['type'], $id);
			$this->shellOut($this->modelError, 'virustotal', 'warning');
			$this->removeDnsStuff($vector['Vector']['id']);
			return false;
		}
		
		if(!$auto_lookup_virustotal)
		{
			if($vector['Hostname']['id']) $auto_lookup_virustotal = $vector['Hostname']['auto_lookup_virustotal'];
		}
		if(!$auto_lookup_virustotal)
		{
			if($vector['Ipaddress']['id']) $auto_lookup_virustotal = $vector['Ipaddress']['auto_lookup_virustotal'];
		}
		
		$this->cronOut(__('Looking up: %s', $vector['Vector']['vector']), 'virustotal');
		
		if($count_type = $this->VT_isDisabled())
		{
			$this->modelError = __('Keys have hit their rate limits, exiting for the %s.', $count_type);
			$this->shellOut($this->modelError, 'virustotal');
			return false;
		}
		
		if($vector['Vector']['type'] == 'hostname')
		{
			$results = $this->NslookupHostname->updateHostnameVirusTotal($id, $auto_lookup_virustotal, $automatic);
			$this->modelError = $this->NslookupHostname->modelError;
			return $results;
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$results = $this->NslookupIpaddress->updateIpaddressVirusTotal($id, $auto_lookup_virustotal, $automatic);
			$this->modelError = $this->NslookupIpaddress->modelError;
			return $results;
		}
		
		return false;
	}
	
	public function batchUpdateNewVirusTotal()
	{
		$time_start = microtime(true);
		
		$conditions = array(
			'Vector.bad' => 0,
			'Vector.type' => $this->vtTypeList(),
			'VectorDetail.vt_lookup >' => 0, 
			'VectorDetail.vt_checked' => null,
		);
		
		$conditions = $this->mergeConditions($conditions, $this->Hostname->getInternalHostConditions(true));
		$conditions = $this->mergeConditions($conditions, $this->Ipaddress->getInternalHostConditions(true));
		$conditions = $this->mergeConditions($conditions, $this->HashSignature->getInternalConditions(true));
		
		$order = array();
		foreach($this->vtTypeList() as $order_field)
		{
			$order[] = "(`Vector`.`type` = '".$order_field."') DESC";
		}
		$order[] = "`Vector`.`created` ASC";
		
		$vectors = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('VectorDetail'),
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $conditions,
			'order' => $order,
			'limit' => 100,
		));
		
		$this->final_result_count = $item_count = count($vectors);
		
		$this->final_results = __('Found %s %s to lookup.', $item_count, __('Vectors'));
		$this->shellOut($this->final_results, 'virustotal');
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Vectors'));
			$this->shellOut($this->final_results, 'virustotal', 'notice');
			return false;
		}
		
		$updated = 0;
		foreach($vectors as $vector_id => $vector)
		{
			if($results = $this->updateVirusTotalReport($vector_id, true)) $updated++;
		}
		
		$this->final_results = __('Updated %s of %s %s looked up.', $updated, $item_count, __('Vectors'));
		$this->shellOut($this->final_results, 'virustotal');
	}
	
	public function updateVirusTotalReport($id = false, $automatic = false)
	{
		$out = false;
		
		//// This is for the reports, to see the nslookups, see the Nslookup Model
		if(!$id) 
		{
			$this->modelError = __('Invalid %s', __('Vector'));
			$this->shellOut($this->modelError, 'virustotal', 'notice');
			return false;
		}
			
		// reset this for garbage cleanup
		$this->vt_vector_ids = array();
		
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('VectorDetail');
		if(!$vector = $this->read(null, $id))
		{
			$this->modelError = __('Invalid %s', __('Vector'));
			$this->shellOut($this->modelError, 'virustotal', 'notice');
			return false;
		}
		
		// maybe add a check later for vt_lookup, 
		// but for now, assume that check has already been made in the main cron method
		// for manual updates, assume they want to do the lookup
		
		$this->shellOut(__('Looking up: %s', $vector['Vector']['vector']), 'virustotal');
		
		// track in the vector_details
		$vector_details = array();
		
		$results = false;
		if($vector['Vector']['type'] == 'hostname')
		{
			$vector_details['vt_checked'] = date('Y-m-d H:i:s');
			$results = $this->VT_getHostnameReport($vector['Vector']['vector'], $automatic, $id);
			if(isset($results['virustotal'])) 
			{
				$results = $results['virustotal'];
				$this->vtProcessHostnameReport($vector, $results);
				$vector_details['vt_updated'] = date('Y-m-d H:i:s');
				$out = true;
			}
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$vector_details['vt_checked'] = date('Y-m-d H:i:s');
			$results = $this->VT_getIpaddressReport($vector['Vector']['vector'], $automatic, $id);
			if(isset($results['virustotal'])) 
			{
				$results = $results['virustotal'];
				$this->vtProcessIpaddressReport($vector, $results);
				$vector_details['vt_updated'] = date('Y-m-d H:i:s');
				$out = true;
			}
		}
		elseif(in_array($vector['Vector']['type'], array_keys($this->EX_listTypes('hash'))))
		{
			$vector_details['vt_checked'] = date('Y-m-d H:i:s');
			$results = $this->VT_getFileBehavior($vector['Vector']['vector'], $automatic, $id);
			if(isset($results['virustotal'])) 
			{
				$results = $results['virustotal'];
				$this->vtProcessFileBehavior($vector, $results);
				$vector_details['vt_updated'] = date('Y-m-d H:i:s');
				$out = true;
			}
		}
		
		// update the vector details
		if($vector_details)
		{
			if($this->vt_user_id)
			{
				$vector_details['vt_user_id'] = $this->vt_user_id;
				$this->vt_user_id - false;
			}
			$this->VectorDetail->checkAddUpdate($vector['Vector']['id'], $vector_details);
		}
		return $out;
	}
	
	public function vtProcessHostnameReport($vector = array(), $results = array())
	{
		// process the samples
		foreach($results as $result_key => $result_set)
		{
			if(preg_match('/samples/i', $result_key))
			{
				$this->shellOut(__('Hostname Report: %s: %s - Found %s %s', __('Vector'), $vector['Vector']['vector'], count($result_set), $result_key), 'virustotal');

				foreach($result_set as $result)
				{
					// no know vector to process
					if(!isset($result['sha256'])) continue;
					
					// check/add the hash
					$vector_sample = trim($result['sha256']);
					$vector_sample_id = false;
					if(!isset($this->vt_vector_ids[$vector_sample]))
					{
						if($vector_sample_id = $this->checkAddNew($vector_sample, array(
							'type' => 'sha256',
						)))
						{
							$this->VectorSource->add($vector_sample_id, 'cron', 'virustotal', date('Y-m-d H:i:s'), 'hostname_report');
							$this->vt_vector_ids[$vector_sample] = $vector_sample_id;
						}
					}
					else
					{
						$vector_sample_id = $this->vt_vector_ids[$vector_sample];
					}
					
					if(!$vector_sample_id) continue; // unknown sample id
					
					$sample_data = array(
						'type' => $result_key,
					);
					if(isset($result['date'])) $sample_data['date'] = date('Y-m-d H:i:s', strtotime($result['date']));
					if(isset($result['total'])) $sample_data['total'] = $result['total'];
					if(isset($result['positives'])) $sample_data['positives'] = $result['positives'];
					
					// check/add/update this record in the samples table
					$this->VtRelatedSampleLookup->checkAdd($vector['Vector']['id'], $vector_sample_id, $sample_data);
				}
			}
		}
		
		// Dns resolved results
		if(isset($results['resolutions']))
		{
			$this->shellOut(__('Hostname Report: %s: %s - Found %s DNS resolutions', __('Vector'), $vector['Vector']['vector'], count($results['resolutions'])), 'virustotal');
			
			if(!is_array($results['resolutions']))
			{
				$this->shellOut(__('Hostname Report: %s: %s - resolutions is not an array. Value: %s', __('Vector'), $vector['Vector']['vector'], serialize($results['resolutions'])), 'virustotal', 'warning');
			}
			else
			{
				foreach($results['resolutions'] as $dns_result)
				{
					if(!isset($dns_result['ip_address'])) continue;
					
					// check/add the ip address as a vector
					$vector_ipaddress = trim($dns_result['ip_address']);
					$vector_ipaddress_id = false;
					if(!isset($this->vt_vector_ids[$vector_ipaddress]))
					{
						if($vector_ipaddress_id = $this->checkAddNew($vector_ipaddress, array(
							'type' => 'ipaddress',
						)))
						{
							$this->VectorSource->add($vector_ipaddress_id, 'virustotal', 'hostname_report', date('Y-m-d H:i:s'), $vector['Vector']['id']);
							$this->vt_vector_ids[$vector_ipaddress] = $vector_ipaddress_id;
						}
					}
					else
					{
						$vector_ipaddress_id = $this->vt_vector_ids[$vector_ipaddress];
					}
					
					if(!$vector_ipaddress_id)
					{
						$this->modelError = __('Unknown %s ID for IP in the dns request', __('Vector'));
						$this->shellOut($this->modelError, 'virustotal', 'notice');
						continue;
					}
					
					// add a record of this in the Nslookup table
					$this->NslookupHostname->checkAdd($vector['Vector']['id'], $vector_ipaddress_id, 'virustotal', array(
						'first_seen' => date('Y-m-d H:i:s'),
						'subsource' => 'hostname_report',
					));
					
					// add a VT Network Traffic Record
					$this->VtNtRecordLookup->checkAdd(
						$vector['Vector']['id'], 
						$vector_ipaddress_id, 0,
						$vector['Vector']['id'], 0,
						array(
							'protocol' => 'dns',
							'last_seen' => (isset($dns_result['last_resolved'])?date('Y-m-d H:i:s', strtotime($dns_result['last_resolved'])):null),
						)
					);
				}
			}
		}
		
		// known urls related to this hostname
		if(isset($results['detected_urls']))
		{
			$this->shellOut(__('Hostname Report: %s: %s - Found %s Detected URLS', __('Vector'), $vector['Vector']['vector'], count($results['detected_urls'])), 'virustotal');
			
			if(!is_array($results['detected_urls']))
			{
				$this->shellOut(__('Hostname Report: %s: %s - Detected URLS is not an array. Value: %s', __('Vector'), $vector['Vector']['vector'], serialize($results['detected_urls'])), 'virustotal', 'warning');
			}
			else
			{
				foreach($results['detected_urls'] as $url_result)
				{
					if(!isset($url_result['url'])) continue;
					
					// check/add the ip address as a vector
					$vector_url = trim($url_result['url']);
					$vector_url_id = false;
					if(!isset($this->vt_vector_ids[$vector_url]))
					{
						if($vector_url_id = $this->checkAddNew($vector_url, array(
							'type' => 'url',
						)))
						{
							$this->VectorSource->add($vector_url_id, 'virustotal', 'hostname_report', date('Y-m-d H:i:s'), $vector['Vector']['id']);
							$this->vt_vector_ids[$vector_url] = $vector_url_id;
						}
					}
					else
					{
						$vector_url_id = $this->vt_vector_ids[$vector_url];
					}
					
					if(!$vector_url_id)
					{
						$this->modelError = __('Unknown %s ID for IP in the detected url', __('Vector'));
						$this->shellOut($this->modelError, 'virustotal', 'notice');
						continue;
					}
					
					$url_data = array();
					if(isset($url_result['scan_date'])) $url_data['scan_date'] = date('Y-m-d H:i:s', strtotime($url_result['scan_date']));
					if(isset($url_result['total'])) $url_data['total'] = $url_result['total'];
					if(isset($url_result['positives'])) $url_data['positives'] = $url_result['positives'];
					
					// check/add/update this record in the detected urls
					$this->VtDetectedUrlLookup->checkAdd($vector['Vector']['id'], $vector_url_id, $url_data);
				}
			}
		}
	}
	
	public function vtProcessIpaddressReport($vector = array(), $results = array())
	{
		// process the samples
		foreach($results as $result_key => $result_set)
		{
			if(preg_match('/samples/i', $result_key))
			{
				$this->shellOut(__('Ipaddress Report: %s: %s - Found %s %s', __('Vector'), $vector['Vector']['vector'], count($result_set), $result_key), 'virustotal');

				foreach($result_set as $result)
				{
					// no know vector to process
					if(!isset($result['sha256'])) continue;
					
					// check/add the hash
					$vector_sample = trim($result['sha256']);
					$vector_sample_id = false;
					if(!isset($this->vt_vector_ids[$vector_sample]))
					{
						if($vector_sample_id = $this->checkAddNew($vector_sample, array(
							'type' => 'sha256',
						)))
						{
							$this->VectorSource->add($vector_sample_id, 'cron', 'virustotal', date('Y-m-d H:i:s'), 'ipaddress_report');
							$this->vt_vector_ids[$vector_sample] = $vector_sample_id;
						}
					}
					else
					{
						$vector_sample_id = $this->vt_vector_ids[$vector_sample];
					}
					
					if(!$vector_sample_id) continue; // unknown sample id
					
					$sample_data = array(
						'type' => $result_key,
					);
					if(isset($result['date'])) $sample_data['date'] = date('Y-m-d H:i:s', strtotime($result['date']));
					if(isset($result['total'])) $sample_data['total'] = $result['total'];
					if(isset($result['positives'])) $sample_data['positives'] = $result['positives'];
					
					// check/add/update this record in the samples table
					$this->VtRelatedSampleLookup->checkAdd($vector['Vector']['id'], $vector_sample_id, $sample_data);
				}
			}
		}
		
		// Dns resolved results
		if(isset($results['resolutions']))
		{
			$this->shellOut(__('Ipaddress Report: %s: %s - Found %s DNS resolutions', __('Vector'), $vector['Vector']['vector'], count($results['resolutions'])), 'virustotal');
			
			if(!is_array($results['resolutions']))
			{
				$this->shellOut(__('Ipaddress Report: %s: %s - resolutions is not an array. Value: %s', __('Vector'), $vector['Vector']['vector'], serialize($results['resolutions'])), 'virustotal', 'warning');
			}
			else
			{
				foreach($results['resolutions'] as $dns_result)
				{
					if(!isset($dns_result['hostname'])) continue;
					
					// check/add the ip address as a vector
					$vector_hostname = trim($dns_result['hostname']);
					$vector_hostname_id = false;
					if(!isset($this->vt_vector_ids[$vector_hostname]))
					{
						if($vector_hostname_id = $this->checkAddNew($vector_hostname, array(
							'type' => 'hostname',
						)))
						{
							$this->VectorSource->add($vector_hostname_id, 'virustotal', 'ipaddress_report', date('Y-m-d H:i:s'), $vector['Vector']['id']);
							$this->vt_vector_ids[$vector_hostname] = $vector_hostname_id;
						}
					}
					else
					{
						$vector_hostname_id = $this->vt_vector_ids[$vector_hostname];
					}
					
					if(!$vector_hostname_id)
					{
						$this->modelError = __('Unknown %s ID for Hostname in the dns request: %s', __('Vector'), $vector_hostname);
						$this->shellOut($this->modelError, 'virustotal', 'notice');
						continue;
					}
					
					// add a record of this in the Nslookup table
					$this->NslookupHostname->checkAdd($vector_hostname_id, $vector['Vector']['id'], 'virustotal', array(
						'first_seen' => date('Y-m-d H:i:s'),
						'subsource' => 'ipaddress_report',
					));
					
					// add a VT Network Traffic Record
					$this->VtNtRecordLookup->checkAdd(
						$vector['Vector']['id'], 
						$vector['Vector']['id'], 0,
						$vector_hostname_id, 0,
						array(
							'protocol' => 'dns',
							'last_seen' => (isset($dns_result['last_resolved'])?date('Y-m-d H:i:s', strtotime($dns_result['last_resolved'])):null),
						)
					);
				}
			}
		}
		
		// known urls related to this hostname
		if(isset($results['detected_urls']))
		{
			$this->shellOut(__('Ipaddress Report: %s: %s - Found %s Detected URLS', __('Vector'), $vector['Vector']['vector'], count($results['detected_urls'])), 'virustotal');
			
			if(!is_array($results['detected_urls']))
			{
				$this->shellOut(__('Ipaddress Report: %s: %s - Detected URLS is not an array. Value: %s', __('Vector'), $vector['Vector']['vector'], serialize($results['detected_urls'])), 'virustotal', 'warning');
			}
			else
			{
				foreach($results['detected_urls'] as $url_result)
				{
					if(!isset($url_result['url'])) continue;
					
					// check/add the ip address as a vector
					$vector_url = trim($url_result['url']);
					$vector_url_id = false;
					if(!isset($this->vt_vector_ids[$vector_url]))
					{
						if($vector_url_id = $this->checkAddNew($vector_url, array(
							'type' => 'url',
						)))
						{
							$this->VectorSource->add($vector_url_id, 'virustotal', 'ipaddress_report', date('Y-m-d H:i:s'), $vector['Vector']['id']);
							$this->vt_vector_ids[$vector_url] = $vector_url_id;
						}
					}
					else
					{
						$vector_url_id = $this->vt_vector_ids[$vector_url];
					}
					
					if(!$vector_url_id)
					{
						$this->modelError = __('Unknown %s ID for IP in the detected url', __('Vector'));
						$this->shellOut($this->modelError, 'virustotal', 'notice');
						continue;
					}
					
					$url_data = array();
					if(isset($url_result['scan_date'])) $url_data['scan_date'] = date('Y-m-d H:i:s', strtotime($url_result['scan_date']));
					if(isset($url_result['total'])) $url_data['total'] = $url_result['total'];
					if(isset($url_result['positives'])) $url_data['positives'] = $url_result['positives'];
					
					// check/add/update this record in the detected urls
					$this->VtDetectedUrlLookup->checkAdd($vector['Vector']['id'], $vector_url_id, $url_data);
				}
			}
		}
	}
	
	public function vtProcessFileBehavior($vector = array(), $results = array())
	{
		// check/add the discovered hosts as a vector
		if(isset($results['network']['hosts']))
		{
			$this->shellOut(__('File Report: %s: %s - Found %s network hosts', __('Vector'), $vector['Vector']['vector'], count($results['network']['hosts'])), 'virustotal');
			
			if(!is_array($results['network']['hosts']))
			{
				$this->shellOut(__('File Report: %s: %s - network hosts is not an array. Value: %s', __('Vector'), $vector['Vector']['vector'], serialize($results['network']['hosts'])), 'virustotal', 'warning');
			}
			else
			{
				foreach($results['network']['hosts'] as $host)
				{
					$host = trim($host);
					if(isset($this->vt_vector_ids[$host])) continue;
					
					$vector_type = $this->EX_discoverType($host);
					$vector_id = $this->checkAddNew($host, array(
						'type' => $vector_type,
					));
					
					// add this as a tracked source
					$this->VectorSource->add($vector_id, 'cron', 'virustotal', date('Y-m-d H:i:s'), 'file_behavior');
					
					$this->vt_vector_ids[$host] = $vector_id;
				}
			}
		}
		
		// check/add the discovered dns requests
		if(isset($results['network']['dns']))
		{
			$this->shellOut(__('File Report: %s: %s - Found %s DNS requests', __('Vector'), $vector['Vector']['vector'], count($results['network']['dns'])), 'virustotal');
			
			foreach($results['network']['dns'] as $dns_request)
			{
				// check/add the ip address as a vector
				$vector_ipaddress = trim($dns_request['ip']);
				$vector_ipaddress_id = false;
				if(!isset($this->vt_vector_ids[$vector_ipaddress]))
				{
					if($vector_ipaddress_id = $this->checkAddNew($vector_ipaddress, array(
						'type' => 'ipaddress',
					)))
					{
						$this->VectorSource->add($vector_ipaddress_id, 'virustotal', 'file_behavior', date('Y-m-d H:i:s'), $vector['Vector']['id']);
						$this->vt_vector_ids[$vector_ipaddress] = $vector_ipaddress_id;
					}
				}
				else
				{
					$vector_ipaddress_id = $this->vt_vector_ids[$vector_ipaddress];
				}
				
				if(!$vector_ipaddress_id)
				{
					$this->modelError = __('Unknown %s ID for IP in the dns request', __('Vector'));
					$this->shellOut($this->modelError, 'virustotal', 'notice');
					continue;
				}
				
				// check/add the hostname as a vector
				$vector_hostname = trim($dns_request['hostname']);				
				$vector_hostname_id = false;
				if(!isset($this->vt_vector_ids[$vector_hostname]))
				{
					if($vector_hostname_id = $this->checkAddNew($vector_hostname, array(
						'type' => 'hostname',
					)))
					{
						$this->VectorSource->add($vector_hostname_id, 'virustotal', 'file_behavior', date('Y-m-d H:i:s'), $vector['Vector']['id']);
						$this->vt_vector_ids[$vector_hostname] = $vector_hostname_id;
					}
				}
				else
				{
					$vector_hostname_id = $this->vt_vector_ids[$vector_hostname];
				}
				
				if(!$vector_hostname_id)
				{
					$this->modelError = __('Unknown %s ID for HOST in the dns request', __('Vector'));
					$this->shellOut($this->modelError, 'virustotal', 'notice');
					continue;
				}
				
				// add a record of this in the Nslookup table
				$this->NslookupHostname->checkAdd($vector_hostname_id, $vector_ipaddress_id, 'virustotal', array(
					'first_seen' => date('Y-m-d H:i:s'),
					'subsource' => 'file_behavior',
				));
				
				// add a VT Network Traffic Record
				$this->VtNtRecordLookup->checkAdd(
					$vector['Vector']['id'], 
					$vector_ipaddress_id, 0,
					$vector_hostname_id, 0,
					array(
						'protocol' => 'dns',
					)
				);
			}
		}
		
		// check/add the discovered udp request records
		if(isset($results['network']['udp']))
		{
			$this->shellOut(__('File Report: %s: %s - Found %s UDP requests', __('Vector'), $vector['Vector']['vector'], count($results['network']['udp'])), 'virustotal');
			
			foreach($results['network']['udp'] as $request)
			{
				$vector_src = trim($request['src']);
				$vector_dst = trim($request['dst']);
				
				$vector_src_id = false;
				// check/add the src as a vector
				if(!isset($this->vt_vector_ids[$vector_src]))
				{
					$vector_type = $this->EX_discoverType($vector_src);
					$vector_id = $this->checkAddNew($vector_src, array(
						'type' => $vector_type,
					));
					$this->vt_vector_ids[$vector_src] = $vector_src_id = $vector_id;
				}
				else
				{
					$vector_src_id = $this->vt_vector_ids[$vector_src];
				}
				
				// check/add the dst as a vector
				$vector_dst_id = false;
				if(!isset($this->vt_vector_ids[$vector_dst]))
				{
					$vector_type = $this->EX_discoverType($vector_dst);
					$vector_id = $this->checkAddNew($vector_dst, array(
						'type' => $vector_type,
					));
					$this->vt_vector_ids[$vector_dst] = $vector_dst_id = $vector_id;
				}
				else
				{
					$vector_dst_id = $this->vt_vector_ids[$vector_dst];
				}
				
				// validate we have their vector ids
				if(!$vector_src_id or !$vector_dst_id)
				{
					$this->modelError = __('Unknown %s IDs for Sorc/Dst in the netword request', __('Vector'));
					$this->shellOut($this->modelError, 'virustotal', 'notice');
					continue;
				}
				
				// add track this request for the dns source
				$this->VectorSource->add($vector_src_id, 'cron', 'virustotal', date('Y-m-d H:i:s'), 'file_behavior');
				$this->VectorSource->add($vector_dst_id, 'cron', 'virustotal', date('Y-m-d H:i:s'), 'file_behavior');
				
				// add a VT Network Traffic Record
				$this->VtNtRecordLookup->checkAdd(
					$vector['Vector']['id'], 
					$vector_src_id, 
					$request['sport'], 
					$vector_src_id, 
					$request['dport'], 
					array(
						'protocol' => 'udp',
						'src_port' => $request['sport'],
						'dst_port' => $request['dport'],
					)
				);
			}
		}
	}
	
	public function vtGetRawFiles($id = null)
	{
		$out = array();
		$paths = $this->VT_rawPaths($id);
		if(is_readable($paths['sys']))
		{
			foreach (glob($paths['sys'].'*') as $filename)
			{
				$stats = stat($filename);
				$filename = basename($filename);
				$out[] = array(
					'filename' => $filename,
					'link' => $paths['web']. $filename,
					'size' => $stats[7],
					'mtime' => date('Y-m-d H:i:s', $stats[9]),
				);
			}
		}
		return $out;
	}
	
	public function updatePassiveTotal($id = false, $dns_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Update the DNS for either a hostname or an ip address
	 * Mainly used in manually updating the vector from the website details page
	 * dns_auto_lookup = the lookup level of this vector
	 * automatic = if this was looked up automatically via the cron
	 */
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		// validate the type
		if(!$this->validateType($vector['Vector']['vector'], $vector['Vector']['type']))
		{
			$this->modelError = __('Vector is not a %s, detected as a %s', $vector['Vector']['type'], $this->found_type);
			$this->shellOut($this->modelError, 'passivetotal');
			
			$vector_vector = $vector['Vector']['vector'];
			// fix the type
			if(!$vector = $this->fixType($vector))
			{
				$this->modelError = __('Unable to properly fix the vector type for: %s - %s', $id, $vector_vector);
				$this->shellOut($this->modelError, 'passivetotal', 'error');
				return false;
			}
		}
		
		if(!in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			$this->modelError = __('Vector is neither a hostname, or ip address. (%s - %s - %s)', $vector['Vector']['vector'], $vector['Vector']['type'], $id);
			$this->shellOut($this->modelError, 'passivetotal', 'warning');
			$this->removeDnsStuff($vector['Vector']['id']);
			return false;
		}
		
		if(!$dns_auto_lookup)
		{
			if($vector['Hostname']['id']) $dns_auto_lookup = $vector['Hostname']['dns_auto_lookup'];
		}
		if(!$dns_auto_lookup)
		{
			if($vector['Ipaddress']['id']) $dns_auto_lookup = $vector['Ipaddress']['dns_auto_lookup'];
		}
		
		$this->cronOut(__('Looking up: %s', $vector['Vector']['vector']), 'passivetotal');
		
		$this->passivetotal_stats = array();
		$this->passivetotal_none = false;
		
		if($vector['Vector']['type'] == 'hostname')
		{
			$results = $this->NslookupHostname->updateHostnamePassiveTotal($id, $dns_auto_lookup, $automatic);
			$this->passivetotal_stats = $this->NslookupHostname->passivetotal_stats;
			$this->PT_disabled = $this->NslookupHostname->PT_disabled;
			$this->modelError = $this->NslookupHostname->modelError;
			return $results;
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$results = $this->NslookupIpaddress->updateIpaddressPassiveTotal($id, $dns_auto_lookup, $automatic);
			$this->passivetotal_stats = $this->NslookupIpaddress->passivetotal_stats;
			$this->PT_disabled = $this->NslookupIpaddress->PT_disabled;
			$this->modelError = $this->NslookupIpaddress->modelError;
			return $results;
		}
		
		return false;
	}
	
	public function updateHexillion($id = false, $hexillion_auto_lookup = 0, $automatic = false)
	{
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		// validate the type
		if(!$this->validateType($vector['Vector']['vector'], $vector['Vector']['type']))
		{
			$this->modelError = __('Vector is not a %s, detected as a %s', $vector['Vector']['type'], $this->found_type);
			$this->shellOut($this->modelError, 'hexillion');
			
			$vector_vector = $vector['Vector']['vector'];
			// fix the type
			if(!$vector = $this->fixType($vector))
			{
				$this->modelError = __('Unable to properly fix the vector type for: %s - %s', $id, $vector_vector);
				$this->shellOut($this->modelError, 'hexillion', 'error');
				return false;
			}
		}
		
		if(!in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			$this->modelError = __('Vector is neither a hostname, or ip address. (%s - %s - %s)', $vector['Vector']['vector'], $vector['Vector']['type'], $id);
			$this->shellOut($this->modelError, 'hexillion', 'warning');
			$this->removeDnsStuff($vector['Vector']['id']);
			return false;
		}
		
		if(!$hexillion_auto_lookup)
		{
			if($vector['Hostname']['id']) $hexillion_auto_lookup = $vector['Hostname']['hexillion_auto_lookup'];
		}
		if(!$hexillion_auto_lookup)
		{
			if($vector['Ipaddress']['id']) $hexillion_auto_lookup = $vector['Ipaddress']['hexillion_auto_lookup'];
		}
		
		$this->cronOut(__('Looking up: %s', $vector['Vector']['vector']), 'hexillion');
		
		$this->hexillion_stats = array();
		$this->hexillion_none = false;
		
		if($vector['Vector']['type'] == 'hostname')
		{
			$results = $this->NslookupHostname->updateHostnameHexillion($id, $hexillion_auto_lookup, $automatic);
			$this->hexillion_stats = $this->NslookupHostname->hexillion_stats;
			$this->Hex_disabled = $this->NslookupHostname->Hex_disabled;
			$this->modelError = $this->NslookupHostname->modelError;
			return $results;
		}
		elseif($vector['Vector']['type'] == 'ipaddress')
		{
			$results = $this->NslookupIpaddress->updateIpaddressHexillion($id, $hexillion_auto_lookup, $automatic);
			$this->hexillion_stats = $this->NslookupIpaddress->hexillion_stats;
			$this->Hex_disabled = $this->NslookupIpaddress->Hex_disabled;
			$this->modelError = $this->NslookupIpaddress->modelError;
			return $results;
		}
		
		return false;
	}
	
	public function updateWhois($id = false, $whois_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Update the Whois for either a hostname or an ip address
	 * Mainly used in manually updating the vector from the website details page
	 * whois_auto_lookup = the lookup level of this vector
	 * automatic = if this was looked up automatically via the cron
	 */
	 	// reset the tracking array
	 	$this->updated_vector_ids = array();
	 	
	 	$this->id = $id;
	 	$this->recursive = 0;
	 	$this->contain('Hostname', 'Ipaddress');
		$vector = $this->read(null, $id);
		
		$results = false;
		
		// regular whois lookups
		if(in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
		{
			if(!$whois_auto_lookup and $vector['Hostname']['id'])
			{
				$whois_auto_lookup = $vector['Hostname']['whois_auto_lookup'];
			}
			elseif(!$whois_auto_lookup and $vector['Ipaddress']['id'])
			{
				$whois_auto_lookup = $vector['Ipaddress']['whois_auto_lookup'];
			}
			
			$this->shellOut(__('Looking up: %s', $vector['Vector']['vector']), 'whois');
			
			$this->Whois->updateWhois($id, $whois_auto_lookup, $automatic);
			
			$this->updated_vector_ids = $this->Whois->updated_vector_ids;
			
			$results = __('Added/updated %s Whois records for vector: %s', count($this->updated_vector_ids), $vector['Vector']['vector']);
		}
		// reverse whois
		else
		{
			if(!$results = $this->WhoiserTransaction->submitSearch($id, $vector, $this->whois_user_id))
			{
				$this->modelError = $this->WhoiserTransaction->modelError;
				return false;
			}
		}
		
		return $results;
	}
	
	public function checkAdd($vector = false, $type = false, $dns_auto_lookup = 0, $dns_auto_lookup_field_name = 'dns_auto_lookup')
	{
	/*
	 * Checks if a vector exists, if not, add it along with it's type
	 */
		$vector = trim($vector);
		if(!$vector) return false;
		
		if(isset($this->checkAddCache[$vector])) return $this->checkAddCache[$vector];
		
		$conditions = array();
		$conditions[$this->alias. '.vector'] = $vector;
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.vector' => $vector)))
		{
			$this->checkAddCache[$vector] = $id;
			return $id;
		}
	 	
		if(!$record)
		{
			$this->create();
			
			if(!$type)
				$type = $this->EX_discoverType($vector);
			
			$this->data = array(
				'vector' => $vector,
				'type' => $type,
			);
			
			if($dns_auto_lookup)
			{
				$this->data[$dns_auto_lookup_field_name] = $dns_auto_lookup;
				
				if($dns_auto_lookup_field_name == 'dns_auto_lookup')
					$this->data['auto_lookup_virustotal'] = $dns_auto_lookup;
			}
			
			if($this->save($this->data))
			{
				// add a new record to the vector_sources table
				return $this->id;
			}
		}
		return false;
	}
	
/*
 * Works just like above, but tries to do it all in as little of sql queries as possible
 */
	public function checkAddMany($vectors = array(), $type = array(), $dns_auto_lookup = array(), $dns_auto_lookup_field_name = array())
	{
		$type_default = false;
		$dns_auto_lookup_default = 0;
		$dns_auto_lookup_field_name_default = 'dns_auto_lookup';
		
		$old_existing = array();
		foreach($vectors as $i => $vector)
		{
			// trim the ones we've already looked up and have cached
			if(isset($this->checkAddCache[$vector]))
			{
				$existing[$vector] = $this->checkAddCache[$vector];
				unset($vectors[$vector]);
			}
		}
		
		$new_existing = $this->find('list', array(
	 		'recursive' => -1,
	 		'fields' => array($this->alias. '.vector', $this->alias. '.id'),
	 		'conditions' => array($this->alias. '.vector' => $vectors),
	 	));
	 	
	}
	
	public function checkAddNew($vector = false, $data = array())
	{
		$vector = trim($vector);
		if(!$vector) return false;
		
		$id = false;
		
		if(isset($this->checkAddCache[$vector])) return $this->checkAddCache[$vector];
		
		$record = $this->find('first', array(
	 		'recursive' => -1,
	 		'conditions' => array('vector' => $vector),
	 	));
	 	
		if(!$record)
		{
			$this->create();
			$data['vector'] = $vector;
		}
		else
		{
			$this->id = $record[$this->alias]['id'];
		}
		
		$this->data = $data;
		
		if($this->save($this->data))
		{
			$id = $this->id;
		}
		
		$this->checkAddCache[$vector] = $id;
		
		return $id;
	}
	
	public function typeList($type = false, $limit = false, $filter = false)
	{
	/*
	 * Provides a list of vectors based on the automatic type
	 */
		
		if(!$type)
		{
			$this->modelError = __('Unknown type.');
			$this->shellOut($this->modelError);
			return false;
		}
		
		$arguments = array(
			'conditions' => array(
				'type' => $type,
			),
			'fields' => array('id', 'vector'),
			'order' => 'vector',
		);
		
		if($limit and is_int($limit))
		{
			$arguments['limit'] = $limit;
		}
		
		$vectors = $this->find('list', $arguments);
		
		// no filtering needed
		if(!$filter) return $vectors;
		
		// empty
		if(!$vectors) return $vectors;
		
		if($type)
		{
			// hostname filter
			if($type == 'hostname')
			{
				$this->shellOut('Found %s vectors recognized as host names.', count($vectors));
				$this->shellOut('Filter out the hostnames that aren\'t actually host names.');
				$vectors_filtered = $this->whiteList($vectors, 'hostnames');
			}
			elseif($type == 'ipaddress')
			{
				$this->shellOut('Found %s vectors recognized as an ip address.', count($vectors));
				$this->shellOut('Filter out the ip addresses that aren\'t actually ip addresses.');
				$vectors_filtered = $this->whiteList($vectors, 'ipaddresses');
			}
		
			if(count($vectors) != count($vectors_filtered))
			{
				$this->shellOut('Remove type from false-positives.');
				$vector_ids = array_diff(array_keys($vectors), array_keys($vectors_filtered));
				
				$results = $this->updateAll(
					array('Vector.type' => '""'),
					array('Vector.id' => $vector_ids)
				);
				
				$vectors = $vectors_filtered;
			}
		}
		
		return $vectors;
	}
	
	public function vtTypeList($justhash = false)
	{
		$hashes = array_keys($this->EX_listTypes('hash'));
		
		if($justhash) return $hashes;
		
		return array_merge(array_keys($this->EX_listTypes('hash')), array('hostname', 'ipaddress'));
	}
	
	public function multiselect($data = false, $multiselect_value = false, $user_id = false)
	{
		$results = parent::multiselect($data, $multiselect_value);
		if($results and $user_id)
		{
			// track the last user that changed the vector group for these vectors
			$vector_ids = array_keys($data['multiple']);
			$this->updateAll(
				array($this->alias.'.user_vtype_id' => $user_id),
				array($this->alias.'.id' => $vector_ids)
			);
		}
		return $results;
	}
	
	public function multiselect_vectortype($data = false, $multiselect_value = false, $manual_type_user_id = false)
	{
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = array();
		if(isset($data['Vector']['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($data['Vector']['multiselect_referer']);
		}
		
		if(!isset($data['multiple']))
		{
			$this->modelError = __('No Vectors were selected');
			return false;
		}
		
		$ids = array();
		if(isset($data['multiple']))
		{
			foreach($data['multiple'] as $id => $selected)
			{
				if(!$selected) continue;
				
				$this->fixType($id, $multiselect_value, false, $manual_type_user_id);
			}
		}
		return true;
	}
	
	public function multiselect_multivectortype($sessionData = array(), $data = array(), $manual_type_user_id = false)
	{
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = array();
		if(isset($sessionData['Vector']['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($sessionData['Vector']['multiselect_referer']);
		}
		
		foreach($data as $item)
		{
			// no change
			if($item['type'] === $item['current_type']) continue;
			
			$this->fixType($item['id'], $item['type'], false, $manual_type_user_id);
		}
		
		return true;
	}
	
	public function multiselect_vttracking($data = false, $multiselect_value = false)
	{
		if(!isset($data['multiple']))
		{
			$this->modelError = __('No Vectors were selected');
			return false;
		}
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = unserialize($data['Vector']['multiselect_referer']);
		
		$ids = array();
		foreach($data['multiple'] as $id => $selected) { if($selected) $ids[$id] = $id; }
			
		// filter for only hostnames, ip addresses, and hashes
		$vector_ids = $this->find('list', array(
			'recursive' => 0,
			'fields' => array('Vector.vector', 'Vector.id'),
			'conditions' => array(
				'Vector.id' => $ids,
				'Vector.type' => $this->vtTypeList(),
			),
		));
		
		if(!$vector_ids)
		{
			$this->modelError = __('None of the selected %s as valid %s, %s, or %s', __('Vectors'), __('Hostnames'), __('IP Addresses'), __('Hashes'));
			return false;
		}
		
		// make sure this vector has a vector details entry
		$cnt=0;
		foreach($vector_ids as $vector_id)
		{
			if($this->VectorDetail->checkAddUpdate($vector_id, array(
				'vt_lookup' => $multiselect_value
			))) { $cnt++; }
		}
		
		$this->modelResults = $cnt;
		
		return true;
	}
	
	public function multiselect_dnstracking($data = false, $multiselect_value = false)
	{
		if(!isset($data['multiple']))
		{
			$this->modelError = __('No Vectors were selected');
			return false;
		}
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = unserialize($data['Vector']['multiselect_referer']);
		
		// get just the ip addresses
		$ipaddresses = $this->Ipaddress->find('list', array(
			'recursive' => -1,
			'fields' => array('Ipaddress.vector_id', 'Ipaddress.id'),
			'conditions' => array(
				'Ipaddress.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($ipaddresses)
		{
			$this->Ipaddress->updateAll(
				array('Ipaddress.dns_auto_lookup' => $multiselect_value, 'Ipaddress.auto_lookup_virustotal' => $multiselect_value, 'Ipaddress.dns_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Ipaddress.id' => $ipaddresses)
			);
		}
		
		// get just the hostnames
		$hostnames = $this->Hostname->find('list', array(
			'recursive' => -1,
			'fields' => array('Hostname.vector_id', 'Hostname.id'),
			'conditions' => array(
				'Hostname.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($hostnames)
		{
			$this->Hostname->updateAll(
				array('Hostname.dns_auto_lookup' => $multiselect_value, 'Hostname.auto_lookup_virustotal' => $multiselect_value, 'Hostname.dns_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Hostname.id' => $hostnames)
			);
		}
		return true;
	}
	
	public function multiselect_multidnstracking($sessionData = array(), $data = array())
	{
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = array();
		if(isset($sessionData['Vector']['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($sessionData['Vector']['multiselect_referer']);
		}
		if(isset($data['Hostname']))
		{
			foreach($data['Hostname'] as $i => $hostname)
			{
				if(isset($data['Hostname'][$i]['dns_auto_lookup'])) 
					$data['Hostname'][$i]['auto_lookup_virustotal'] = $data['Hostname'][$i]['dns_auto_lookup'];
				$data['Hostname'][$i]['dns_auto_lookup_user_id'] = AuthComponent::user('id');
			}
			if(!$this->Hostname->saveMany($data['Hostname']))
			{
				$this->modelError = __('Unalbe to update the Hostnames.');
			}
		}
		if(isset($data['Ipaddress']))
		{
			foreach($data['Ipaddress'] as $i => $ipaddress) 
			{
				if(isset($data['Ipaddress'][$i]['dns_auto_lookup'])) 
					$data['Ipaddress'][$i]['auto_lookup_virustotal'] = $data['Ipaddress'][$i]['dns_auto_lookup'];
				$data['Ipaddress'][$i]['dns_auto_lookup_user_id'] = AuthComponent::user('id');
			}
			if(!$this->Ipaddress->saveMany($data['Ipaddress']))
			{
				$this->modelError = __('Unalbe to update the Ip Addresses.');
			}
		}
		return true;
	}
	
	public function multiselect_hexilliontracking($data = false, $multiselect_value = false)
	{
		if(!isset($data['multiple']))
		{
			$this->modelError = __('No Vectors were selected');
			return false;
		}
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = unserialize($data['Vector']['multiselect_referer']);
		
		// get just the ip addresses
		$ipaddresses = $this->Ipaddress->find('list', array(
			'recursive' => -1,
			'fields' => array('Ipaddress.vector_id', 'Ipaddress.id'),
			'conditions' => array(
				'Ipaddress.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($ipaddresses)
		{
			$this->Ipaddress->updateAll(
				array('Ipaddress.hexillion_auto_lookup' => $multiselect_value, 'Ipaddress.hexillion_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Ipaddress.id' => $ipaddresses)
			);
		}
		
		// get just the hostnames
		$hostnames = $this->Hostname->find('list', array(
			'recursive' => -1,
			'fields' => array('Hostname.vector_id', 'Hostname.id'),
			'conditions' => array(
				'Hostname.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($hostnames)
		{
			$this->Hostname->updateAll(
				array('Hostname.hexillion_auto_lookup' => $multiselect_value, 'Hostname.hexillion_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Hostname.id' => $hostnames)
			);
		}
		return true;
	}
	
	public function multiselect_multihexilliontracking($sessionData = array(), $data = array())
	{
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = array();
		if(isset($sessionData['Vector']['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($sessionData['Vector']['multiselect_referer']);
		}
		if(isset($data['Hostname']))
		{
			foreach($data['Hostname'] as $i => $hostname)
			{
				$data['Hostname'][$i]['hexillion_auto_lookup_user_id'] = AuthComponent::user('id');
			}
			if(!$this->Hostname->saveMany($data['Hostname']))
			{
				$this->modelError = __('Unalbe to update the Hostnames.');
			}
		}
		if(isset($data['Ipaddress']))
		{
			foreach($data['Ipaddress'] as $i => $ipaddress) 
			{
				$data['Ipaddress'][$i]['hexillion_auto_lookup_user_id'] = AuthComponent::user('id');
			}
			if(!$this->Ipaddress->saveMany($data['Ipaddress']))
			{
				$this->modelError = __('Unalbe to update the Ip Addresses.');
			}
		}
		return true;
	}
	
	public function multiselect_whoistracking($data = false, $multiselect_value = false)
	{
		if(!isset($data['multiple']))
		{
			$this->modelError = __('No Vectors were selected');
			return false;
		}
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = unserialize($data['Vector']['multiselect_referer']);
		
		// get just the ip addresses
		$ipaddresses = $this->Ipaddress->find('list', array(
			'recursive' => -1,
			'fields' => array('Ipaddress.vector_id', 'Ipaddress.id'),
			'conditions' => array(
				'Ipaddress.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($ipaddresses)
		{
			$this->Ipaddress->updateAll(
				array('Ipaddress.whois_auto_lookup' => $multiselect_value, 'Ipaddress.whois_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Ipaddress.id' => $ipaddresses)
			);
		}
		
		// get just the hostnames
		$hostnames = $this->Hostname->find('list', array(
			'recursive' => -1,
			'fields' => array('Hostname.vector_id', 'Hostname.id'),
			'conditions' => array(
				'Hostname.vector_id' => array_keys($data['multiple']),
			),
		));
		
		// update the ip addresses
		if($hostnames)
		{
			$this->Hostname->updateAll(
				array('Hostname.whois_auto_lookup' => $multiselect_value, 'Hostname.whois_auto_lookup_user_id' => AuthComponent::user('id')),
				array('Hostname.id' => $hostnames)
			);
		}
		return true;
	}
	
	public function multiselect_multiwhoistracking($sessionData = array(), $data = array())
	{
		
		// see if we can figure out where to send the user after the update
		$this->multiselectReferer = array();
		if(isset($sessionData['Vector']['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($sessionData['Vector']['multiselect_referer']);
		}
		if(isset($data['Hostname']))
		{
			foreach($data['Hostname'] as $i => $hostname) {$data['Hostname'][$i]['whois_auto_lookup_user_id'] = AuthComponent::user('id'); }
			if(!$this->Hostname->saveMany($data['Hostname']))
			{
				$this->modelError = __('Unalbe to update the Hostnames.');
			}
		}
		if(isset($data['Ipaddress']))
		{
			foreach($data['Ipaddress'] as $i => $ipaddress) {$data['Ipaddress'][$i]['whois_auto_lookup_user_id'] = AuthComponent::user('id'); }
			if(!$this->Ipaddress->saveMany($data['Ipaddress']))
			{
				$this->modelError = __('Unalbe to update the Ip Addresses.');
			}
		}
		return true;
	}
	
/** Correlations SQL/List Functions **/

	public function combinedViewConditions($combined_view_id = false)
	{
		if(!$combined_view_id)
			return [];
		
		// get all of the category ids for this combined view
		$categoryIds = $this->Category->CombinedViewCategory->listForView($combined_view_id, true);
//		$categorySQL = $this->CategoriesVector->sqlCategoriesVector($categoryIds);
		$categoryVectorIds = $this->CategoriesVector->listVectorIds2($categoryIds);
		
		// get all of the report ids for this combined view
		$reportIds = $this->Report->CombinedViewReport->listForView($combined_view_id, true);
//		$reportSQL = $this->ReportsVector->sqlReportsVector($reportIds);
		$reportVectorIds = $this->ReportsVector->listVectorIds2($reportIds);
		$vectorIds = $categoryVectorIds + $reportVectorIds;
		
/*
		$conditions = [
			'OR' => [
				$this->alias.'.id IN ('.$categorySQL->value.') AND 1=' => '1',
				$this->alias.'.id IN ('.$reportSQL->value.') AND 1=' => '1',
			],
		];
*/
		$conditions = [
			$this->alias.'.id' => $vectorIds,
		];
		
		return $conditions;
		
	}

	public function listCategoriesVectorsUnique($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this category's vector ids
		if(!$object_vector_ids = $this->CategoriesVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in other categories
		/// get the list of category vectors that this user has access to (can see the category)
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'CategoriesVector.category_id !=' => $object_id,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Category';
			$conditions['OR'] = array(
				'Category.public' => 2,
				array( 'Category.public' => 1, 'Category.org_group_id' => $org_group_id),
				array('Category.public' => 0, 'Category.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoriesVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in other categories
		if($object_vector_other_ids = $this->CategoriesVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in reports
		/// get the list of reports vectors that this user has access to (can see the report)
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Report';
			$conditions['OR'] = array(
				'Report.public' => 2,
				array('Report.public' => 1, 'Report.org_group_id' => $org_group_id),
				array('Report.public' => 0, 'Report.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoriesVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in reports
		if($object_vector_other_ids = $this->ReportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in imports
		/// get the list of import vectors that this user has access to (can see the import)
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoriesVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in imports
		if($object_vector_other_ids = $this->ImportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		return $object_vector_ids;
	}
	
	public function listReportsVectorsUnique($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this report's vector ids
		if(!$object_vector_ids = $this->ReportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in other reports
		/// get the list of report vectors that this user has access to (can see the report)
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'ReportsVector.report_id !=' => $object_id,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Report';
			$conditions['OR'] = array(
				'Report.public' => 2,
				array( 'Report.public' => 1, 'Report.org_group_id' => $org_group_id),
				array('Report.public' => 0, 'Report.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in other reports
		if($object_vector_other_ids = $this->ReportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in categories
		/// get the list of categories vectors that this user has access to (can see the category)
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Category';
			$conditions['OR'] = array(
				'Category.public' => 2,
				array('Category.public' => 1, 'Category.org_group_id' => $org_group_id),
				array('Category.public' => 0, 'Category.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in categories
		if($object_vector_other_ids = $this->CategoriesVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in imports
		/// get the list of import vectors that this user has access to (can see the import)
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in imports
		if($object_vector_other_ids = $this->ImportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		return $object_vector_ids;
	}
	
	public function listImportsVectorsUnique($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this import's vector ids
		if(!$object_vector_ids = $this->ImportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in other imports
		/// get the list of import vectors that this user has access to (can see the import)
		$conditions = array(
			'ImportsVector.vector_id' => $object_vector_ids,
			'ImportsVector.import_id !=' => $object_id,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listImportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in other imports
		if($object_vector_other_ids = $this->ImportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in reports
		/// get the list of reports vectors that this user has access to (can see the report)
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Report';
			$conditions['OR'] = array(
				'Report.public' => 2,
				array('Report.public' => 1, 'Report.org_group_id' => $org_group_id),
				array('Report.public' => 0, 'Report.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in reports
		if($object_vector_other_ids = $this->ReportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in categories
		/// get the list of categories vectors that this user has access to (can see the category)
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Category';
			$conditions['OR'] = array(
				'Category.public' => 2,
				array('Category.public' => 1, 'Category.org_group_id' => $org_group_id),
				array('Category.public' => 0, 'Category.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoriesVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in categories
		if($object_vector_other_ids = $this->CategoriesVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		return $object_vector_ids;
	}
	
	public function listDumpsVectorsUnique($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this dump's vector ids
		if(!$object_vector_ids = $this->DumpsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in reports
		/// get the list of reports vectors that this user has access to (can see the report)
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Report';
			$conditions['OR'] = array(
				'Report.public' => 2,
				array('Report.public' => 1, 'Report.org_group_id' => $org_group_id),
				array('Report.public' => 0, 'Report.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportsVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in reports
		if($object_vector_other_ids = $this->ReportsVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		///////// filter the vector_ids out that exist in categories
		/// get the list of categories vectors that this user has access to (can see the category)
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		$contain = array('Vector');
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Category';
			$conditions['OR'] = array(
				'Category.public' => 2,
				array('Category.public' => 1, 'Category.org_group_id' => $org_group_id),
				array('Category.public' => 0, 'Category.user_id' => $user_id),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoriesVectorsUnique('.$object_id.')';
		}
		
		/// filter the vector_ids out that exist in categories
		if($object_vector_other_ids = $this->CategoriesVector->find('list', $options))
		{
			$object_vector_ids = array_diff($object_vector_ids, $object_vector_other_ids);
		}
		if(empty($object_vector_ids))
		{
			return false;
		}
		
		return $object_vector_ids;
	}
	//////////
	
	public function listCategoryToReportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
	/*
	 * Dns records related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$object_id) return false;
		/////// let try a different way then below
		
		/////////// this category's vectors
		if(!$object_vector_ids = $this->CategoriesVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.report_id', 'ReportsVector.report_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoryToReportsIds('.$object_id.')';
		}
		
		return $this->ReportsVector->find('list', $options);
	}
	
	public function sqlCategoryToReportsVectorsRelated($category_id = false, $admin = false)
	{
	/*
	 * Reports related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->CategoriesVector->recursive = 0;
		$db = $this->CategoriesVector->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		$subQuery = ' `ReportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listCategoryToImportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
	/*
	 * Dns records related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$object_id) return false;
		/////// let try a different way then below
		
		/////////// this category's vectors
		if(!$object_vector_ids = $this->CategoriesVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ImportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.import_id', 'ImportsVector.import_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoryToImportsIds('.$object_id.')';
		}
		
		return $this->ImportsVector->find('list', $options);
	}
	
	public function sqlCategoryToImportsVectorsRelated($category_id = false, $admin = false)
	{
	/*
	 * Imports related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->CategoriesVector->recursive = 0;
		$db = $this->CategoriesVector->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		$subQuery = ' `ImportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlCategoryToUploadsRelated($category_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->CategoriesVector->recursive = 0;
		$db = $this->CategoriesVector->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		$subQuery = ' `UploadsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the upload_ids from this model that share the same vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['UploadsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector2`.`upload_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`UploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `UploadsVector2`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Upload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlCategoryToUploadsVectorsRelated($category_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->CategoriesVector->recursive = 0;
		$db = $this->CategoriesVector->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		$subQuery = ' `UploadsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlCategoryToNslookupRelated($category_id = false, $admin = false)
	{
	/*
	 * Dns records related to a Category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		/////// let try a different way then below

		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.category_id' => $category_id,
		);
		if(!$admin)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$conditions['Vector.bad'] = 0;
		
		if($this->alias == 'VectorHostname')
		{
			$conditions['Vector.type'] = 'hostname';
		}
		elseif($this->alias == 'VectorIpaddress')
		{
			$conditions['Vector.type'] = 'ipaddress';
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::sqlCategoryToNslookupRelated('.$category_id.')';
		}
		
		if(!$object_vector_ids = $this->CategoriesVector->find('list', $options))
		{
			return false;
		}
		
		$subQuery_compiled = false;
		if($this->alias == 'VectorHostname')
		{
			$subQuery_compiled = ' `VectorHostname`.`id` IN (' . implode(',', $object_vector_ids) . ') ';
		}
		elseif($this->alias == 'VectorIpaddress')
		{
			$subQuery_compiled = ' `VectorIpaddress`.`id` IN (' . implode(',', $object_vector_ids) . ') ';
		}
		
		if($subQuery_compiled)
		{
			$db = $this->getDataSource();
			$subQueryExpression = $db->expression($subQuery_compiled);
			
			return $subQueryExpression;
		}
		
		return false;
	}
	
	public function listReportToCategoriesIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this category's vectors
		if(!$object_vector_ids = $this->ReportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.category_id', 'CategoriesVector.category_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportToCategoriesIds('.$object_id.')';
		}
		
		return $this->CategoriesVector->find('list', $options);
	}
	
	public function sqlReportToCategoriesVectorsRelated($report_id = false, $admin = false)
	{
	/*
	 * Categories related to a Report
	 * Builds the complex query for the conditions
	 */
		if(!$report_id) return false;
		
		// get the vector ids from this category
		$this->ReportsVector->recursive = 0;
		$db = $this->ReportsVector->getDataSource();
		
		$subQuery_conditions = array('ReportsVector1.report_id' => $report_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ReportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ReportsVector1`.`vector_id`'
					),
				),
			),
			$this->ReportsVector
		);
		$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listReportToImportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		if(!$object_vector_ids = $this->ReportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ImportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.import_id', 'ImportsVector.import_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportToImportsIds('.$object_id.')';
		}
		
		return $this->ImportsVector->find('list', $options);
	}
	
	public function sqlReportToImportsVectorsRelated($report_id = false, $admin = false)
	{
	/*
	 * Imports related to a Report
	 * Builds the complex query for the conditions
	 */
		if(!$report_id) return false;
		
		// get the vector ids from this import
		$this->ReportsVector->recursive = 0;
		$db = $this->ReportsVector->getDataSource();
		
		$subQuery_conditions = array('ReportsVector1.report_id' => $report_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ReportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ReportsVector1`.`vector_id`'
					),
				),
			),
			$this->ReportsVector
		);
		$subQuery = ' `ImportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlReportToUploadsRelated($report_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Report
	 * Builds the complex query for the conditions
	 */
		if(!$report_id) return false;
		
		// get the vector ids from this upload
		$this->ReportsVector->recursive = 0;
		$db = $this->ReportsVector->getDataSource();
		
		$subQuery_conditions = array('ReportsVector1.report_id' => $report_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ReportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ReportsVector1`.`vector_id`'
					),
				),
			),
			$this->ReportsVector
		);
		$subQuery = ' `UploadsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the upload_ids from this model that share the same vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['UploadsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector2`.`upload_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`UploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `UploadsVector2`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		// get the uploads themselves
		
		$subQuery2 = ' `Upload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlReportToUploadsVectorsRelated($report_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Report
	 * Builds the complex query for the conditions
	 */
		if(!$report_id) return false;
		
		// get the vector ids from this upload
		$this->ReportsVector->recursive = 0;
		$db = $this->ReportsVector->getDataSource();
		
		$subQuery_conditions = array('ReportsVector1.report_id' => $report_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ReportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ReportsVector1`.`vector_id`'
					),
				),
			),
			$this->ReportsVector
		);
		$subQuery = ' `UploadsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listImportToCategoriesIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this import's vectors
		if(!$object_vector_ids = $this->ImportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.category_id', 'CategoriesVector.category_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listImportToCategoriesIds('.$object_id.')';
		}
		
		return $this->CategoriesVector->find('list', $options);
	}
	
	public function sqlImportToCategoriesRelated($import_id = false, $admin = false)
	{
	/*
	 * Categories related to a Import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this category
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `CategoriesVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the category_ids from this model that share the same vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['CategoriesVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector2`.`category_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`CategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `CategoriesVector2`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Category`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlImportToCategoriesVectorsRelated($import_id = false, $admin = false)
	{
	/*
	 * Categories related to a Import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this category
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listImportToReportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this import's vectors
		if(!$object_vector_ids = $this->ImportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.report_id', 'ReportsVector.report_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listImportToReportsIds('.$object_id.')';
		}
		
		return $this->ReportsVector->find('list', $options);
	}
	
	public function sqlImportToReportsVectorsRelated($import_id = false, $admin = false)
	{
	/*
	 * Reports related to a Import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this import
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `ReportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlImportToUploadsRelated($import_id = false, $admin = false)
	{
	/*
	 * Uploads related to an Import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this upload
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `UploadsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the upload_ids from this model that share the came vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['UploadsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector2`.`upload_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`UploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `UploadsVector2`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		// get the uploads themselves
		
		$subQuery2 = ' `Upload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlImportToUploadsVectorsRelated($import_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this import
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `UploadsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listUploadToReportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this upload's vectors
		if(!$object_vector_ids = $this->UploadsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.report_id', 'ReportsVector.report_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listUploadToReportsIds('.$object_id.')';
		}
		
		return $this->ReportsVector->find('list', $options);
	}
	
	public function sqlUploadToReportsVectorsRelated($upload_id = false, $admin = false)
	{
	/*
	 * Reports related to a Upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this upload
		$this->UploadsVector->recursive = 0;
		$db = $this->UploadsVector->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		$subQuery = ' `ReportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listUploadToCategoriesIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this upload's vectors
		if(!$object_vector_ids = $this->UploadsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.category_id', 'CategoriesVector.category_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listUploadToCategoriesIds('.$object_id.')';
		}
		
		return $this->CategoriesVector->find('list', $options);
	}
	
	public function sqlUploadToCategoriesVectorsRelated($upload_id = false, $admin = false)
	{
	/*
	 * Categories related to a Upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this category
		$this->UploadsVector->recursive = 0;
		$db = $this->UploadsVector->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlUploadToImportsRelated($upload_id = false, $admin = false)
	{
	/*
	 * Imports related to an Upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this category
		$this->UploadsVector->recursive = 0;
		$db = $this->UploadsVector->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		$subQuery = ' `ImportsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the import_ids from this model that share the same vectors
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['ImportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector2`.`import_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`ImportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `ImportsVector2`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Import`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlUploadToImportsVectorsRelated($upload_id = false, $admin = false)
	{
	/*
	 * Imports related to a Upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this upload
		$this->UploadsVector->recursive = 0;
		$db = $this->UploadsVector->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		$subQuery = ' `ImportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	//////////
	public function sqlDumpToReportsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Reports related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this category
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `ReportsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the report_ids from this model that share the same vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['ReportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`ReportsVector2`.`report_id`'),
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`ReportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `ReportsVector2`.`vector_id`'
					),
				),
			),
			$this->ReportsVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Report`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlDumpToReportsVectorsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Reports related to a Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this dump
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `ReportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listDumpToCategoriesIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this category's vectors
		if(!$object_vector_ids = $this->DumpsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.category_id', 'CategoriesVector.category_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listDumpToCategoriesIds('.$object_id.')';
		}
		
		return $this->CategoriesVector->find('list', $options);
	}
	
	public function sqlDumpToCategoriesRelatedOLD($dump_id = false, $admin = false)
	{
	/*
	 * Categories related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this category
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `CategoriesVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the category_ids from this model that share the came vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['CategoriesVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector2`.`category_id`'),
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`CategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `CategoriesVector2`.`vector_id`'
					),
				),
			),
			$this->CategoriesVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Category`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlDumpToCategoriesVectorsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Categories related to a Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this category
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function listDumpToReportsIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		if(!$object_id) return false;
		
		/////////// this report's vectors
		if(!$object_vector_ids = $this->DumpsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ReportsVector.vector_id' => $object_vector_ids,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ReportsVector.report_id', 'ReportsVector.report_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listDumpToReportsIds('.$object_id.')';
		}
		
		return $this->ReportsVector->find('list', $options);
	}
	
	public function sqlDumpToUploadsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Uploads related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this upload
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `UploadsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the upload_ids from this model that share the came vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['UploadsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector2`.`upload_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`UploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `UploadsVector2`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		// get the uploads themselves
		
		$subQuery2 = ' `Upload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlDumpToUploadsVectorsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Uploads related to a Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this dump
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `UploadsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlDumpToImportsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Imports related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this import
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `ImportsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		// get the import_ids from this model that share the came vectors.
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['ImportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector2`.`import_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`ImportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `ImportsVector2`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		// get the imports themselves
		
		$subQuery2 = ' `Import`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function sqlDumpToImportsVectorsRelated($dump_id = false, $admin = false)
	{
	/*
	 * Imports related to a Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the vector ids from this dump
		$this->DumpsVector->recursive = 0;
		$db = $this->DumpsVector->getDataSource();
		
		$subQuery_conditions = array('DumpsVector1.dump_id' => $dump_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `DumpsVector1`.`vector_id`'
					),
				),
			),
			$this->DumpsVector
		);
		$subQuery = ' `ImportsVector`.`vector_id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	/** Comparison Functions **/
	//////////
	public function compareCategoryReport($category_id = false, $report_id = false, $admin = false)
	{
	/*
	 * Compare a category and a report
	 */
		$data = array(
			'category' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'report' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		$vectors_category_conditions = array(
			'CategoriesVector.category_id' => $category_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_category_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_category = $this->CategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_category_conditions,
		));
		asort($vectors_category);
		
		$vectors_report_conditions = array(
			'ReportsVector.report_id' => $report_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_category_conditions['ReportsVector.active'] = 1;
		}
		
		$vectors_report = $this->ReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_report_conditions,
		));		
		asort($vectors_report);
		
		// find the unique vector_ids
		$vectors_category_unique = array_diff_assoc($vectors_category, $vectors_report);
		$vectors_report_unique = array_diff_assoc($vectors_report, $vectors_category);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_category, $vectors_report);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_category, $vectors_report));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_category = "\n". implode("\n",$vectors_category);

		$string_report = "\n". implode("\n",$vectors_report);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_category, $string_report);
		
		$data = array(
			'category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_category_unique,
			),
			'report' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_report_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareCategoryUpload($category_id = false, $upload_id = false, $admin = false)
	{
	/*
	 * Compare a category and an upload
	 */
		$data = array(
			'category' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'upload' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		$vectors_category_conditions = array(
			'CategoriesVector.category_id' => $category_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_category_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_category = $this->CategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_category_conditions,
		));
		asort($vectors_category);
		
		$vectors_upload_conditions = array(
			'UploadsVector.upload_id' => $upload_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_upload['UploadsVector.active'] = 1;
		}
		
		$vectors_upload = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_upload_conditions,
		));		
		asort($vectors_upload);
		
		// find the unique vector_ids
		$vectors_category_unique = array_diff_assoc($vectors_category, $vectors_upload);
		$vectors_upload_unique = array_diff_assoc($vectors_upload, $vectors_category);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_category, $vectors_upload);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_category, $vectors_upload));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_category = "\n". implode("\n",$vectors_category);

		$string_upload = "\n". implode("\n",$vectors_upload);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_category, $string_upload);
		
		$data = array(
			'category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_category_unique,
			),
			'upload' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_upload_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareCategoryImport($category_id = false, $import_id = false, $admin = false)
	{
	/*
	 * Compare a category and an import
	 */
		$data = array(
			'category' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'import' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_category_conditions = array(
			'CategoriesVector.category_id' => $category_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_category_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_category = $this->CategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_category_conditions,
		));
		asort($vectors_category);
		
		$vectors_import_conditions = array(
			'ImportsVector.import_id' => $import_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_import_conditions['ImportsVector.active'] = 1;
		}
		
		$vectors_import = $this->ImportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_import_conditions,
		));		
		asort($vectors_import);
		
		// find the unique vector_ids
		$vectors_category_unique = array_diff_assoc($vectors_category, $vectors_import);
		$vectors_import_unique = array_diff_assoc($vectors_import, $vectors_category);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_category, $vectors_import);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_category, $vectors_import));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_category = "\n". implode("\n",$vectors_category);

		$string_import = "\n". implode("\n",$vectors_import);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_category, $string_import);
		
		$data = array(
			'category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_category_unique,
			),
			'import' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_import_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareCategoryDump($category_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare a category and an dump
	 */
		$data = array(
			'category' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_category_conditions = array(
			'CategoriesVector.category_id' => $category_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_category_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_category = $this->CategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_category_conditions,
		));
		asort($vectors_category);
		
		$vectors_dump_conditions = array(
			'DumpsVector.dump_id' => $dump_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_dump_conditions['DumpsVector.active'] = 1;
		}
		
		$vectors_dump = $this->DumpsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_dump_conditions,
		));		
		asort($vectors_dump);
		
		// find the unique vector_ids
		$vectors_category_unique = array_diff_assoc($vectors_category, $vectors_dump);
		$vectors_dump_unique = array_diff_assoc($vectors_dump, $vectors_category);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_category, $vectors_dump);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_category, $vectors_dump));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_category = "\n". implode("\n",$vectors_category);

		$string_dump = "\n". implode("\n",$vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_category, $string_dump);
		
		$data = array(
			'category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_category_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	//////////
	public function compareReportUpload($report_id = false, $upload_id = false, $admin = false)
	{
	/*
	 * Compare a report and an upload
	 */
		$data = array(
			'report' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'upload' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_report_conditions = array(
			'ReportsVector.report_id' => $report_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_report_conditions['ReportsVector.active'] = 1;
		}
		
		$vectors_report = $this->ReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_report_conditions,
		));
		asort($vectors_report);
		
		$vectors_upload_conditions = array(
			'UploadsVector.upload_id' => $upload_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_upload_conditions['UploadsVector.active'] = 1;
		}
		
		$vectors_upload = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_upload_conditions,
		));		
		asort($vectors_upload);
		
		// find the unique vector_ids
		$vectors_report_unique = array_diff_assoc($vectors_report, $vectors_upload);
		$vectors_upload_unique = array_diff_assoc($vectors_upload, $vectors_report);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_report, $vectors_upload);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_report, $vectors_upload));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_report = "\n". implode("\n",$vectors_report);

		$string_upload = "\n". implode("\n",$vectors_upload);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_report, $string_upload);
		
		$data = array(
			'report' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_report_unique,
			),
			'upload' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_upload_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareReportDump($report_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare a report and an dump
	 */
		$data = array(
			'report' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_report_conditions = array(
			'ReportsVector.report_id' => $report_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_report_conditions['ReportsVector.active'] = 1;
		}
		
		$vectors_report = $this->ReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_report_conditions,
		));
		asort($vectors_report);
		
		$vectors_dump_conditions = array(
			'DumpsVector.dump_id' => $dump_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_dump_conditions['DumpsVector.active'] = 1;
		}
		
		$vectors_dump = $this->DumpsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_dump_conditions,
		));		
		asort($vectors_dump);
		
		// find the unique vector_ids
		$vectors_report_unique = array_diff_assoc($vectors_report, $vectors_dump);
		$vectors_dump_unique = array_diff_assoc($vectors_dump, $vectors_report);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_report, $vectors_dump);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_report, $vectors_dump));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_report = "\n". implode("\n",$vectors_report);

		$string_dump = "\n". implode("\n",$vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_report, $string_dump);
		
		$data = array(
			'report' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_report_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareReportImport($report_id = false, $import_id = false, $admin = false)
	{
	/*
	 * Compare a report and an import
	 */
		$data = array(
			'report' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'import' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_report_conditions = array(
			'ReportsVector.report_id' => $report_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_report_conditions['ReportsVector.active'] = 1;
		}
		
		$vectors_report = $this->ReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_report_conditions,
		));
		asort($vectors_report);
		
		$vectors_import_conditions = array(
			'ImportsVector.import_id' => $import_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_import_conditions['ImportsVector.active'] = 1;
		}
		
		$vectors_import = $this->ImportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_import_conditions,
		));		
		asort($vectors_import);
		
		// find the unique vector_ids
		$vectors_report_unique = array_diff_assoc($vectors_report, $vectors_import);
		$vectors_import_unique = array_diff_assoc($vectors_import, $vectors_report);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_report, $vectors_import);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_report, $vectors_import));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_report = "\n". implode("\n",$vectors_report);

		$string_import = "\n". implode("\n",$vectors_import);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_report, $string_import);
		
		$data = array(
			'report' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_report_unique,
			),
			'import' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_import_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	//////////
	
	public function compareImportDump($import_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare an import and a dump
	 */
		$data = array(
			'import' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_import_conditions = array(
			'ImportsVector.import_id' => $import_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_import_conditions['ImportsVector.active'] = 1;
		}
		
		$vectors_import = $this->ImportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_import_conditions,
		));
		asort($vectors_import);
		
		$vectors_dump_conditions = array(
			'DumpsVector.dump_id' => $dump_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_dump_conditions['DumpsVector.active'] = 1;
		}
		
		$vectors_dump = $this->DumpsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_dump_conditions,
		));		
		asort($vectors_dump);
		
		// find the unique vector_ids
		$vectors_import_unique = array_diff_assoc($vectors_import, $vectors_dump);
		$vectors_dump_unique = array_diff_assoc($vectors_dump, $vectors_import);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_import, $vectors_dump);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_import, $vectors_dump));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_import = "\n". implode("\n",$vectors_import);

		$string_dump = "\n". implode("\n",$vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_import, $string_dump);
		
		$data = array(
			'import' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_import_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	//////////
	public function compareUploadDump($upload_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare an upload and a dump
	 */
		$data = array(
			'upload' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_upload_conditions = array(
			'UploadsVector.upload_id' => $upload_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_upload_conditions['UploadsVector.active'] = 1;
		}
		
		$vectors_upload = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_upload_conditions,
		));
		asort($vectors_upload);
		
		$vectors_dump_conditions = array(
			'DumpsVector.dump_id' => $dump_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_dump_conditions['DumpsVector.active'] = 1;
		}
		
		$vectors_dump = $this->DumpsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_dump_conditions,
		));		
		asort($vectors_dump);
		
		// find the unique vector_ids
		$vectors_upload_unique = array_diff_assoc($vectors_upload, $vectors_dump);
		$vectors_dump_unique = array_diff_assoc($vectors_dump, $vectors_upload);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_upload, $vectors_dump);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_upload, $vectors_dump));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_upload = "\n". implode("\n",$vectors_upload);

		$string_dump = "\n". implode("\n",$vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_upload, $string_dump);
		
		$data = array(
			'upload' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_upload_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function compareUploadImport($upload_id = false, $import_id = false, $admin = false)
	{
	/*
	 * Compare an upload and a import
	 */
		$data = array(
			'upload' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'import' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		
		$vectors_upload_conditions = array(
			'UploadsVector.upload_id' => $upload_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_upload_conditions['UploadsVector.active'] = 1;
		}
		
		$vectors_upload = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_upload_conditions,
		));
		asort($vectors_upload);
		
		$vectors_import_conditions = array(
			'ImportsVector.import_id' => $import_id,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_import_conditions['ImportsVector.active'] = 1;
		}
		
		$vectors_import = $this->ImportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_import_conditions,
		));		
		asort($vectors_import);
		
		// find the unique vector_ids
		$vectors_upload_unique = array_diff_assoc($vectors_upload, $vectors_import);
		$vectors_import_unique = array_diff_assoc($vectors_import, $vectors_upload);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_upload, $vectors_import);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_upload, $vectors_import));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_upload = "\n". implode("\n",$vectors_upload);

		$string_import = "\n". implode("\n",$vectors_import);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_upload, $string_import);
		
		$data = array(
			'upload' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_upload_unique,
			),
			'import' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_import_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
/** Temporary Functions for instituting changes to the production database **/

	public function updateVectorSources()
	{
		// disable this as it's no longer needed, but leaving here for reference
		return true;
		
		$categories_vectors = $this->CategoriesVector->find('all');
		$this->shellOut('CategoriesVector Total: '. count($categories_vectors));
		$i=0;
		foreach($categories_vectors as $categories_vector)
		{
			if($this->VectorSource->tempCheckAdd($categories_vector['CategoriesVector']['vector_id'], 'manual', 'category', $categories_vector['CategoriesVector']['category_id'], $categories_vector['CategoriesVector']['created']))
			{
			$i++;
			}
		}
		$this->shellOut('CategoriesVector Added: '. $i);
		
		$reports_vectors = $this->ReportsVector->find('all');
		$this->shellOut('ReportsVector Total: '. count($reports_vectors));
		$i=0;
		foreach($reports_vectors as $reports_vector)
		{
			if($this->VectorSource->tempCheckAdd($reports_vector['ReportsVector']['vector_id'], 'manual', 'report', $reports_vector['ReportsVector']['report_id'], $reports_vector['ReportsVector']['created']))
			{
			$i++;
			}
		}
		$this->shellOut('ReportsVector Added: '. $i);
		
		$uploads_vectors = $this->UploadsVector->find('all');
		$this->shellOut('UploadsVector Total: '. count($uploads_vectors));
		$i=0;
		foreach($uploads_vectors as $uploads_vector)
		{
			if($this->VectorSource->tempCheckAdd($uploads_vector['UploadsVector']['vector_id'], 'manual', 'upload', $uploads_vector['UploadsVector']['upload_id'], $uploads_vector['UploadsVector']['created']))
			{
			$i++;
			}
		}
		$this->shellOut('UploadsVector Added: '. $i);
	}
	
	public function stats()
	{
		$stats = array(
			'total' => 0,
			'by_type' => array(),
			
		);
		
		$this->recursive = -1;
		
		$this->shellOut(__('Getting the total count...'));		
		$stats['total'] = $this->find('count');
		$this->shellOut(__('Total: %s', $stats['total']));
		
		$this->shellOut(__('Getting the total count by type...'));
		$types = array();
		$this->shellOut(__('Total: %s', $stats['total']));
		
		return $stats;
	}
	
	public function dashboardOverviewStats()
	{
		$stats = [
			'total' => ['name' => __('Total'), 'value' => $this->find('count')],
			'good' => ['name' => __('Good'), 'value' => $this->find('count', ['conditions' => [$this->alias.'.bad' => false]])],
			'bad' => ['name' => __('Bad'), 'value' => $this->find('count', ['conditions' => [$this->alias.'.bad' => true]])],
			'created_today' => ['name' => __('Created Today'), 'value' => $this->find('count', ['conditions' => [$this->alias.'.created >' => date('Y-m-d 00:00:00') ]])],
			'created_7days' => ['name' => __('Created past 7 days'), 'value' => $this->find('count', ['conditions' => [$this->alias.'.created >' => date('Y-m-d 00:00:00', strtotime('-7 days')) ]])],
		];
		
		return $stats;
	}
	
	public function snapshotDashboardGetStats($snapshotKeyRegex = false, $start = false, $end = false)
	{
		return $this->Snapshot_dashboardStats($snapshotKeyRegex, $start, $end);
	}
	
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return [];
	}
}

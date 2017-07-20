<?php
App::uses('AppModel', 'Model');
/**
 * Hostname Model
 *
 * @property Vector $Vector
 
 dns auto lookup levels, and thier meaning:
 0 - dont lookup
 1 - lookup just this
 2 - lookup this, plus results
 3 - look up this, once
 
 */
class Hostname extends AppModel 
{
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
		'HostnameDnsTrackingUser' => array(
			'className' => 'User',
			'foreignKey' => 'dns_auto_lookup_user_id',
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Nslookup',
		'Utilities.VirusTotal',
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
			],
		],
	);
	
	public $hex_balance = false; // track the hexillion balance 
	
	public $checkAddCache = array();
	
	public function checkAddBlank($vector_id = false, $dns_auto_lookup = false, $source = false, $subsource = false, $subsource2 = false, $whois_auto_lookup = false, $hexillion_auto_lookup = false)
	{
	/*
	 * Adds a record for a vector
	 */
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector id');
			$this->shellOut($this->modelError);
			return false;
		}
		
		if(isset($this->checkAddCache[$vector_id])) return $this->checkAddCache[$vector_id];
		
		$dns_auto_lookup_user_id = $whois_auto_lookup_user_id = $hexillion_auto_lookup_user_id = (AuthComponent::user('id')?AuthComponent::user('id'):0);
		
		$id = false;
		
		$record = $this->find('first', array(
	 		'recursive' => -1,
	 		'conditions' => array('vector_id' => $vector_id),
	 	));
	 	
		if(!$record)
		{
			$this->create();
			$this->data = array(
				'vector_id' => $vector_id,
			);
			if($dns_auto_lookup !== false)
			{
				$this->data['dns_auto_lookup'] = $dns_auto_lookup;
				$this->data['auto_lookup_virustotal'] = $dns_auto_lookup;
				$this->data['dns_auto_lookup_user_id'] = $dns_auto_lookup_user_id;
			}
			if($hexillion_auto_lookup !== false)
			{
				$this->data['hexillion_auto_lookup'] = $hexillion_auto_lookup;
				$this->data['hexillion_auto_lookup_user_id'] = $hexillion_auto_lookup_user_id;
			}
			if($whois_auto_lookup !== false)
			{
				$this->data['whois_auto_lookup'] = $whois_auto_lookup;
				$this->data['whois_auto_lookup_user_id'] = $whois_auto_lookup_user_id;
			}
			if($source !== false)
			{
				$this->data['source'] = $source;
			}
			if($subsource !== false)
			{
				$this->data['subsource'] = $subsource;
			}
			if($subsource2 !== false)
			{
				$this->data['subsource2'] = $subsource2;
			}
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		// overwrite existing settings as needed
		else
		{
			$id = $record[$this->alias]['id'];
			$data = false;
			if($dns_auto_lookup !== false)
			{
				$data['dns_auto_lookup'] = $dns_auto_lookup;
				$data['auto_lookup_virustotal'] = $dns_auto_lookup;
				$data['dns_auto_lookup_user_id'] = $dns_auto_lookup_user_id;
			}
			if($hexillion_auto_lookup !== false)
			{
				$data['hexillion_auto_lookup'] = $hexillion_auto_lookup;
				$data['hexillion_auto_lookup_user_id'] = $hexillion_auto_lookup_user_id;
			}
			if($whois_auto_lookup !== false)
			{
				$data['whois_auto_lookup'] = $whois_auto_lookup;
				$data['whois_auto_lookup_user_id'] = $whois_auto_lookup_user_id;
			}
			if($data)
			{
				$this->id = $record[$this->alias]['id'];
				$this->data = $data;
				if($this->save($this->data))
				{
					$id = $this->id;
				}
			}
		}
		
		$this->checkAddCache[$vector_id] = $id;
		
		return $id;
	}
	
	public function batchUpdateDNS($minutes = 1440, $limit = 100, $local_remote = 0)
	{
	/*
	 * Takes a list of hostnames that haven't been looked up in $minutes, and does a dns lookup
	 * Designed mainly to be ran from the Console (see CronShell)
	 * 1440 minutes = 24 hours
	 */
		$time_start = microtime(true);
		
		$this->final_results = __('Finding %s to update their DNS.', __('Hostnames'));
		$this->shellOut($this->final_results, 'nslookup');
		
		$minutes = '-'. $minutes. ' minutes';
		
		$conditions = array(
			'Vector.bad' => 0,
			'Hostname.dns_auto_lookup >' => 0, // only hostnames that are allowed to be looked up
			'or' => array(
				'Hostname.dns_checked <' => date('Y-m-d H:i:s', strtotime($minutes)),
				'Hostname.dns_checked' => null,
			),
		);
		
		$lookup_type = '';
		
		if($local_remote)
		{
			$lookup_type = false;
			$exclude = false;
			if($local_remote == 1) 
			{
				$lookup_type = __('Local ');
			}
			elseif($local_remote == 2) 
			{
				$lookup_type = __('Remote ');
				$exclude = true;
			}
			
			$this->final_results = __('Filtering for only %s%s', $lookup_type, __('Hostnames'));
			$this->shellOut($this->final_results, 'nslookup');
			
			// get the list of items that are considered local from the app config
			$conditions = $this->mergeConditions($conditions, $this->getInternalHostConditions($exclude));
		}
		
		$hostnames = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('Vector'),
			'fields' => array('Hostname.vector_id', 'Hostname.dns_auto_lookup'),
			'conditions' => $conditions,
			'order' => array(
				'Hostname.dns_checked' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s%s are ready to be looked up at this time.', $lookup_type, __('Hostnames'));
			$this->shellOut($this->final_results, 'nslookup');
			return false;
		}
		
		$this->final_results = __('Found %s %s%s to lookup (minute: %s, limit: %s)', $item_count, $lookup_type, __('Hostnames'), $minutes, $limit);
		$this->shellOut($this->final_results, 'nslookup');
		
		$return = array();
		
		$i=0;
		foreach($hostnames as $vector_id => $dns_auto_lookup)
		{
			$i++;
			$percent = $i/$item_count;
			$percent_friendly = number_format( $percent * 100, 0 ) . '%';
			$this->shellOut(__('(%s of %s - %s) Looking up vector_id: %s ', $i, $item_count, $percent_friendly, $vector_id), 'nslookup');
			
			$return[$vector_id] = $this->Vector->updateDns($vector_id, $dns_auto_lookup, true);
		}
		
		$this->hex_balance = $this->Vector->hex_balance;
		$this->shellOut(__('Hexillion Balance: %s', $this->hex_balance), 'nslookup', 'notice', false);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s %s%s - took %s seconds', count($hostnames), $lookup_type, __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'nslookup');
		
		return $return;
	}
	
	public function updateDNSdates($vector_id = false, $checked = true, $updated = false, $dns_auto_lookup_original = 3, $dnsdbapi = false)
	{
	/*
	 * updates the dates when the dns was checked, or updated
	 * changes the dns auto update from a 3 to a 0
	 */
		
		$data = array();
		
		if(!$vector_id) return false;
		
		if($dnsdbapi)
		{
			$k = 'dnsdbapi';
			if(is_string($dnsdbapi))
			{
				$k = $dnsdbapi;
			}
			if($checked == true) $data['dns_checked_'. $k] = date('Y-m-d H:i:s');
			if($updated == true) $data['dns_updated_'. $k] = date('Y-m-d H:i:s');
		}
		else
		{
			if($checked == true) $data['dns_checked'] = date('Y-m-d H:i:s');
			if($updated == true) $data['dns_updated'] = date('Y-m-d H:i:s');
		}
		
		$data['dns_auto_lookup'] = $dns_auto_lookup_original;
		// for the one-offs
		if($dns_auto_lookup_original == 3) $data['dns_auto_lookup'] = 0;
		
		if($data)
		{
			if($id = $this->checkAddBlank($vector_id))
			{
				$this->id = $id;
				$this->data = $data;
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function updateDnsLookupLevel($vector_id, $dns_auto_lookup = 0)
	{
	/*
	 * this should only be called from the cron scripts
	 * only allow ones to be changed from a 0 to a 1
	 * this is called within a loop of/for the results
	 */

		// make sure a record exists
		if($id = $this->checkAddBlank($vector_id))
		{
			// don't allow auto lookup to be disabled from here/cron
			if($dns_auto_lookup == 0)
			{
				return false;
			}
			
			$this->id = $id;
			$current_dns_auto_lookup = $this->field('dns_auto_lookup');
			if($current_dns_auto_lookup < $dns_auto_lookup)
			{
				$this->data = array('dns_auto_lookup' => $dns_auto_lookup);
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function batchUpdateDNSdbapi($minutes = false, $limit = 100)
	{
	/*
	 * Takes a list of hostnames that haven't been looked up in $minutes, and does a dns lookup
	 * Designed mainly to be ran from the Console (see CronShell)
	 * 1440 minutes = 24 hours
	 */
		$time_start = microtime(true);
		
		if(!$minutes)
		{
			$minutes = (1440 * 30); // 1 month
		}
		
		// check to see if dnsdbapi is disabled before looking up hostnames
		if($this->dnsdbapi_isDisabled())
		{
			$this->final_result_count = false;
			$this->final_results = __('DNSdbapi is currently disabled.');
			$this->shellOut($this->final_results, 'dnsdbapi');
			return false;
		}
		
		$minutes = '-'. $minutes. ' minutes';
		
		$conditions = array(
			'Vector.bad' => 0,
			'Hostname.dns_auto_lookup >' => 0, // only hostnames that are allowed to be looked up
			'or' => array(
				'Hostname.dns_checked_dnsdbapi' => null,
				'Hostname.dns_checked_dnsdbapi <' => date('Y-m-d H:i:s', strtotime($minutes)),
			),
		);
		
		$conditions = $this->mergeConditions($conditions, $this->getInternalHostConditions(true));
		
		$hostnames = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('Vector'),
			'fields' => array('Hostname.vector_id', 'Hostname.dns_auto_lookup'),
			'conditions' => $conditions,
			'order' => array(
				'Hostname.dns_checked_dnsdbapi' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		$this->final_results = __('Found %s %s to lookup (minute: %s, limit: %s)', $item_count, __('Hostnames'), $minutes, $limit);
		$this->shellOut($this->final_results, 'dnsdbapi');
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Hostnames'));
			$this->shellOut($this->final_results, 'dnsdbapi', 'notice');
			return false;
		}
		
		$return = array();
		
		$i=0;
		foreach($hostnames as $vector_id => $dns_auto_lookup)
		{
			$i++;
			$percent = $i/$item_count;
			$percent_friendly = number_format( $percent * 100, 0 ) . '%';
			
			$this->final_results = __('(%s of %s - %s) Looking up vector_id: %s ', $i, $item_count, $percent_friendly, $vector_id);
			$this->shellOut($this->final_results, 'dnsdbapi');
			
			$return[$vector_id] = $this->Vector->updateDnsDbapi($vector_id, $dns_auto_lookup, true);
			if($this->Vector->dnsdbapi_none)
			{
				$this->final_results = __('Keys have hit their rate limits, exiting.');
				$this->shellOut($this->final_results, 'dnsdbapi');
				$i--;
				break;
			}
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s of %s %s - took %s seconds', $i, count($hostnames), __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'dnsdbapi');
		
		return $return;
	}
	
	public function batchUpdateVirusTotal($time_period = 'minute')
	{
		$time_start = microtime(true);
		
		// check to see if virustotal is disabled
		// uses nslookup behavior's aleady existing functionalit to do this
		if($length = $this->Vector->VT_isDisabled())
		{
			$this->final_result_count = false;
			$this->final_results = __('VirusTotal is currently disabled for this %s.', $length);
			$this->shellOut($this->final_results, 'virustotal');
			return false;
		}
		
		$limit = $this->Vector->VT_getLimit($time_period);
		
		$conditions = array(
			'Vector.bad' => 0,
			'Hostname.auto_lookup_virustotal >' => 0, // only hostnames that are allowed to be looked up
			'or' => array(
				'Hostname.dns_checked_virustotal' => null,
				'Hostname.dns_checked_virustotal <' => date('Y-m-d H:i:s', strtotime('-24 hours')),
			),
		);
		
		$conditions = $this->mergeConditions($conditions, $this->getInternalHostConditions(true));
		
		$hostnames = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('Vector'),
			'fields' => array('Hostname.vector_id', 'Hostname.auto_lookup_virustotal'),
			'conditions' => $conditions,
			'order' => array(
				'Hostname.created' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		$this->final_results = __('Found %s %s to lookup (limit: %s)', $item_count, __('Hostnames'), $limit);
		$this->shellOut($this->final_results, 'virustotal');
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Hostnames'));
			$this->shellOut($this->final_results, 'virustotal', 'notice');
			return false;
		}
		
		$return = array();
		
		$i=0;
		foreach($hostnames as $vector_id => $auto_lookup_virustotal)
		{
			$i++;
			$percent = $i/$item_count;
			$percent_friendly = number_format( $percent * 100, 0 ) . '%';
			
			$this->final_results = __('(%s of %s - %s) Looking up vector_id: %s ', $i, $item_count, $percent_friendly, $vector_id);
			$this->shellOut($this->final_results, 'virustotal');
			
			$return[$vector_id] = $this->Vector->updateVirusTotal($vector_id, $auto_lookup_virustotal, true);
			if($this->Vector->VT_isDisabled())
			{
				$this->final_results = __('Keys have hit their rate limits, exiting.');
				$this->shellOut($this->final_results, 'virustotal');
				$i--;
				break;
			}
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s of %s %s - took %s seconds', $i, count($hostnames), __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'virustotal');
		
		return $return;
		
	}
	
	public function updateVirusTotalDates($vector_id = false, $checked = true, $updated = false)
	{
		$data = array();
		
		if(!$vector_id) return false;
		
		if($checked == true) $data['dns_checked_virustotal'] = date('Y-m-d H:i:s');
		if($updated == true) $data['dns_updated_virustotal'] = date('Y-m-d H:i:s');
		
		if(count($data))
		{
			if($id = $this->checkAddBlank($vector_id))
			{
				$this->id = $id;
				$this->data = $data;
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function updateVirusTotalLookupLevel($vector_id, $auto_lookup_virustotal = 0)
	{
	/*
	 * this should only be called from the cron scripts
	 * only allow ones to be changed from a 0 to a 1
	 * this is called within a loop of/for the results
	 */

		// make sure a record exists
		if($id = $this->checkAddBlank($vector_id))
		{
			// don't allow auto lookup to be disabled from here/cron
			if($auto_lookup_virustotal == 0)
			{
				return false;
			}
			
			$this->id = $id;
			$current_auto_lookup_virustotal = $this->field('auto_lookup_virustotal');
			if($current_auto_lookup_virustotal < $auto_lookup_virustotal)
			{
				$this->data = array('auto_lookup_virustotal' => $auto_lookup_virustotal);
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function batchUpdatePassiveTotal($minutes = 1440, $limit = 100)
	{
		$time_start = microtime(true);
		
		// check to see if passivetotal is disabled
		if($this->Vector->NslookupHostname->PT_isDisabled())
		{
			$this->final_result_count = false;
			$this->final_results = __('PassiveTotal is currently disabled.');
			$this->shellOut($this->final_results, 'passivetotal');
			return false;
		}
		
		$minutes = '-'. $minutes. ' minutes';
		
		$conditions = array(
			'Vector.bad' => 0,
			'Hostname.dns_auto_lookup >' => 0, // only hostnames that are allowed to be looked up
			'or' => array(
				'Hostname.dns_checked_passivetotal' => null,
				'Hostname.dns_checked_passivetotal <' => date('Y-m-d H:i:s', strtotime($minutes)),
			),
		);
		
		$conditions = $this->mergeConditions($conditions, $this->getInternalHostConditions(true));
		
		$hostnames = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('Vector'),
			'fields' => array('Hostname.vector_id', 'Hostname.dns_auto_lookup'),
			'conditions' => $conditions,
			'order' => array(
				'Hostname.dns_checked_passivetotal' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		$this->final_results = __('Found %s %s to lookup (limit: %s)', $item_count, __('Hostnames'), $limit);
		$this->shellOut($this->final_results, 'passivetotal');
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Hostnames'));
			$this->shellOut($this->final_results, 'passivetotal', 'notice');
			return false;
		}
		
		$return = array();
		
		$i=0;
		foreach($hostnames as $vector_id => $dns_auto_lookup)
		{
			$i++;
			$percent = $i/$item_count;
			$percent_friendly = number_format( $percent * 100, 0 ) . '%';
			
			$this->final_results = __('(%s of %s - %s) Looking up vector_id: %s ', $i, $item_count, $percent_friendly, $vector_id);
			$this->shellOut($this->final_results, 'passivetotal');
			
			$results = $this->Vector->updatePassiveTotal($vector_id, $dns_auto_lookup, true);
			if($this->Vector->NslookupHostname->PT_isDisabled())
			{
				$this->final_results = __('Passive Total is temporarly disabled.');
				$this->shellOut($this->final_results, 'passivetotal');
				$i--;
				break;
			}
			$return[$vector_id] = $results;
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s of %s %s - took %s seconds', $i, count($hostnames), __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'passivetotal');
		
		return $return;
		
	}
	
	public function updatePassiveTotalDates($vector_id = false, $checked = true, $updated = false)
	{
		$data = array();
		
		if(!$vector_id) return false;
		
		if($checked == true) $data['dns_checked_passivetotal'] = date('Y-m-d H:i:s');
		if($updated == true) $data['dns_updated_passivetotal'] = date('Y-m-d H:i:s');
		
		if(count($data))
		{
			if($id = $this->checkAddBlank($vector_id))
			{
				$this->id = $id;
				$this->data = $data;
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function batchUpdateHexillion($minutes = 1440, $limit = 100)
	{
		$time_start = microtime(true);
		
		// check to see if hexillion is disabled
		// uses nslookup behavior's aleady existing functionalit to do this
		if($this->Vector->NslookupHostname->Hex_isDisabled())
		{
			$this->final_result_count = false;
			$this->final_results = __('Hexillion is currently disabled.');
			$this->shellOut($this->final_results, 'hexillion');
			return false;
		}
		
		$minutes = '-'. $minutes. ' minutes';
		
		$conditions = array(
			'Vector.bad' => 0,
			'Hostname.hexillion_auto_lookup >' => 0, // only hostnames that are allowed to be looked up
			'or' => array(
				'Hostname.dns_checked_hexillion' => null,
				'Hostname.dns_checked_hexillion <' => date('Y-m-d H:i:s', strtotime($minutes)),
			),
		);
		
		$conditions = $this->mergeConditions($conditions, $this->getInternalHostConditions(true));
		
		$hostnames = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('Vector'),
			'fields' => array('Hostname.vector_id', 'Hostname.hexillion_auto_lookup'),
			'conditions' => $conditions,
			'order' => array(
				'Hostname.dns_checked_hexillion' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		$this->final_results = __('Found %s %s to lookup (limit: %s)', $item_count, __('Hostnames'), $limit);
		$this->shellOut($this->final_results, 'hexillion');
		
		if(!$item_count) 
		{
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Hostnames'));
			$this->shellOut($this->final_results, 'hexillion', 'notice');
			return false;
		}
		
		$return = array();
		
		$i=0;
		foreach($hostnames as $vector_id => $hexillion_auto_lookup)
		{
			$i++;
			$percent = $i/$item_count;
			$percent_friendly = number_format( $percent * 100, 0 ) . '%';
			
			$this->final_results = __('(%s of %s - %s) Looking up vector_id: %s ', $i, $item_count, $percent_friendly, $vector_id);
			$this->shellOut($this->final_results, 'hexillion');
			
			$results = $this->Vector->updateHexillion($vector_id, $hexillion_auto_lookup, true);
			if($this->Vector->NslookupHostname->Hex_isDisabled())
			{
				$this->final_results = __('Hexillion is temporarly disabled.');
				$this->shellOut($this->final_results, 'hexillion');
				$i--;
				break;
			}
			$return[$vector_id] = $results;
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s of %s %s - took %s seconds', $i, count($hostnames), __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'hexillion');
		
		return $return;
		
	}
	
	public function updateHexillionDates($vector_id = false, $checked = true, $updated = false)
	{
		$data = array();
		
		if(!$vector_id) return false;
		
		if($checked == true) $data['dns_checked_hexillion'] = date('Y-m-d H:i:s');
		if($updated == true) $data['dns_updated_hexillion'] = date('Y-m-d H:i:s');
		
		if(count($data))
		{
			if($id = $this->checkAddBlank($vector_id))
			{
				$this->id = $id;
				$this->data = $data;
				return $this->save($this->data);
			}
		}
		return false;
	}
	
	public function getInternalHostConditions($exclude = false, $alias = 'Vector', $class = false)
	{
		// get the list of items that are considered local from the app config
		$local_config = Configure::read('AppConfig.Nslookup.internal_hosts');
		$local_config = explode(',', $local_config);
		
		// clean them up. this is after all, user input
		foreach($local_config as $i => $local_item)
		{
			$local_item = trim($local_item);
			if(!$local_item) { unset($local_config[$i]); continue; }
			$local_item = strtolower($local_item);
			$local_config[$i] = $local_item;
		}
		// remove duplicates
		$local_config = array_flip($local_config);
		$local_config = array_flip($local_config);
		
		$searchFields = array(
				$alias. '.vector' => array('direction' => 'left'),
		);
		if($class)
		{
			$searchFields[$alias. '.vector']['class'] = $class;
		}
		
		// build the query conditions with the Search.SearchableBehavior
		$conditions = array(
			'q' => implode("\n", $local_config), 
			'ex' => $exclude,
			'searchFields' => $searchFields,
			'padding' => 15,
		);
		
		return $this->orConditions($conditions);
	}
	
	public function batchUpdateWhois($minutes = 1440, $limit = 100)
	{
	/*
	 * Takes a list of hostnames that haven't been looked up in $minutes, and does a whois lookup
	 * Designed mainly to be ran from the Console (see CronShell)
	 * 1440 minutes = 24 hours
	 */
		$time_start = microtime(true);
		
		$minutes = '-'. $minutes. ' minutes';
		
		$hostnames = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('Hostname.vector_id', 'Hostname.whois_auto_lookup'),
			'conditions' => array(
				'Hostname.whois_auto_lookup >' => 0, // only hostnames that are allowed to be looked up
//				'Hostname.whois_auto_lookup' => 3, // only hostnames that are allowed to be looked up
				'or' => array(
//					'Hostname.whois_checked <' => date('Y-m-d H:i:s', strtotime($minutes)),
					'Hostname.whois_checked <' => date('Y-m-d H:i:s', strtotime('-1 month')),
					'Hostname.whois_checked' => null,
				),
			),
			'order' => array(
				'Hostname.whois_checked' => 'ASC',
			),
			'limit' => $limit,
		));
		
		$this->final_result_count = $item_count = count($hostnames);
		
		$this->final_results = __('Found %s %s to lookup (minute: %s, limit: %s)', $item_count, __('Hostnames'), $minutes, $limit);
		$this->shellOut($this->final_results, 'whois');
		
		if(!$item_count) 
		{
			// changed from notice to info, as this is flooding with emails
			$this->final_results = __('No %s are ready to be looked up at this time.', __('Hostnames'));
			$this->shellOut($this->final_results, 'whois');
			return false;
		}
		
		$return = array();
		
		$i=0;
		$j=0;
		reset($hostnames);
		while (list($vector_id, $whois_auto_lookup) = each($hostnames))
//		foreach($hostnames as $vector_id => $whois_auto_lookup)
		{
			$i++;
			$j++;
			
			$this->final_results = __('Looking up vector_id: %s - (%s/%s of %s)', $vector_id, $i, $j, count($hostnames));
			$this->shellOut($this->final_results, 'whois');
			
			$return[$vector_id] = $this->Vector->updateWhois($vector_id, $whois_auto_lookup, true);
			unset($hostnames[$vector_id]);
			
			// clear out ones that have already bee updated;
			if(is_array($this->Vector->updated_vector_ids) and count($this->Vector->updated_vector_ids) > 1)
			{
				foreach($this->Vector->updated_vector_ids as $updated_vector_id)
				{
					if(isset($hostnames[$updated_vector_id]))
					{
						unset($hostnames[$updated_vector_id]);
						$j = 0;
						reset($hostnames);
					}
				}
			}
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Completed for: %s %s - took %s seconds', count($return), __('Hostnames'), $time);
		$this->shellOut($this->final_results, 'whois');
		
		return $return;
	}
	
	public function updateWhoisDates($vector_id = false, $checked = true, $updated = false, $whois_auto_lookup_original = 0)
	{
	/*
	 * updates the dates when the dns was checked, or updated
	 */
		
		$data = array();
		
		if(!$vector_id) return false;
		
		if($checked == true) $data['whois_checked'] = date('Y-m-d H:i:s');
		if($updated == true) $data['whois_updated'] = date('Y-m-d H:i:s');
		if($whois_auto_lookup_original == 3) $data['whois_auto_lookup'] = 0;
		
		if($data)
		{
			if($id = $this->checkAddBlank($vector_id))
			{
				$this->id = $id;
				$this->data = $data;
				return $this->save($data);
			}
		}
		return false;
	}
	
	public function updateWhoisLookupLevel($vector_id, $whois_auto_lookup = 0)
	{
		if($id = $this->field('id', array('vector_id' => $vector_id)))
		{
			$this->id = $id;
			$this->data = array('whois_auto_lookup' => $whois_auto_lookup);
			return $this->save($this->data);
		}
		return false;
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * Nslookup Model
 *
 * @property Hostname $Hostname
 * @property Ipaddress $Ipaddress
 */
class Nslookup extends AppModel 
{


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $hasMany = array(
		'NslookupLog' => array(
			'className' => 'NslookupLog',
			'foreignKey' => 'nslookup_id',
			'dependent' => true,
		),
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'VectorHostname' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_hostname_id',
		),
		'VectorIpaddress' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_ipaddress_id',
		),
	);
	
	public $actsAs = [
		'Utilities.Nslookup', 
		'Utilities.PassiveTotal', 
		'Utilities.Hexillion', 
		'Cacher.Cache' => [
			'config' => 'slowQueries',
			'clearOnDelete' => false,
			'clearOnSave' => false,
			'gzip' => false,
		],
	];
	
	// switch to tell if the record is new
	public $recordNew = false;
	
	// switch if the record is updated
	public $recordUpdated = false;  // for later use
	
	// switch when adding a vector source from a pre-existing nslookup record
	public $recordCreatedDate = false;
	
	// define the fields that can be searched
	public $searchFields = array(
		'VectorHostname.vector' => array('class' => 'Vector'),
		'VectorIpaddress.vector' => array('class' => 'Vector'),
		'Nslookup.ttl',
		'Nslookup.source',
	);
	
	// used to track the dnsdbapi count
	public $dnsdbapi_stats = array();
	
	// true if all available keys are used
	public $dnsdbapi_none = false;
	
	public $hex_balance = false; // track the hexillion balance 
	
	public $checkAddCache = array();
	
	
	public function updateHostnameDns($vector_hostname_id = false, $dns_auto_lookup = 0, $automatic = false, $dnsdbapi = false)
	{
	/*
	 * Does an nslookup on a hostname and stores the results in the database
	 */
	 	$logScope = 'nslookup';
	 	if($dnsdbapi)
	 	{
	 		$logScope = 'dnsdbapi';
	 	}
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->dnsdbapi_stats = array();
		$this->dnsdbapi_none = false;
	 	
	 	if(!$vector_hostname_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorHostname->id = $vector_hostname_id;
		if(!$vector = $this->VectorHostname->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_hostname_id);
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$dns_auto_lookup_this = $dns_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$dns_auto_lookup_results = 0;
		
		if($dns_auto_lookup == 3)
		{
			$dns_auto_lookup_this = 0;
			$dns_auto_lookup_results = 0;
		}
		elseif($dns_auto_lookup == 2)
		{
			$dns_auto_lookup_results = 1;
		}
		elseif($dns_auto_lookup == 1)
		{
			$dns_auto_lookup_results = 0;
		}
		
		if(!$dns_auto_lookup) $dns_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this hostname it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->NS_getIps($vector, $dnsdbapi, $automatic); // always returns an array, even if empty
		
		if($this->dnsdbapi_none)
		{
			$this->shellOut(__('No keys are available'), $logScope, 'notice');
			return $results;
		}
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'warning');
			return false;
		}
		
		// track the hexillion balance
		$this->hex_balance = $this->NS_GetHexBalance();
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $ipaddress => $details)
			{
				// check/add the ipaddress to the vectors table
				if(!$vector_ipaddress_id = $this->VectorIpaddress->checkAdd($ipaddress, 'ipaddress', $dns_auto_lookup_results)) continue;
				
				// update the $dns_auto_lookup on the hostnames table
				$this->VectorHostname->Ipaddress->updateDnsLookupLevel($vector_ipaddress_id, $dns_auto_lookup_results);
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorHostname->VectorSource->add($vector_ipaddress_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateHostnameDns-'. $vector_hostname_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the hostname record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorHostname->Hostname->updateDNSdates($vector_hostname_id, true, $updated, $dns_auto_lookup_this, $dnsdbapi);
		
		// track this transaction
		// add errors later
		$this->VectorHostname->DnsTransactionLog->addLog($vector_hostname_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function updateIpaddressDns($vector_ipaddress_id = false, $dns_auto_lookup = 0, $automatic = false, $dnsdbapi = false)
	{
	/*
	 * Does an nslookup on a hostname and stores the results in the database
	 */
	 	$logScope = 'nslookup';
	 	if($dnsdbapi)
	 	{
	 		$logScope = 'dnsdbapi';
	 	}
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->dnsdbapi_stats = array();
		$this->dnsdbapi_none = false;
	 	
	 	if(!$vector_ipaddress_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
			$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorIpaddress->id = $vector_ipaddress_id;
		if(!$vector = $this->VectorIpaddress->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_ipaddress_id);
			$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$dns_auto_lookup_this = $dns_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$dns_auto_lookup_results = 0;
		
		if($dns_auto_lookup == 3)
		{
			$dns_auto_lookup_this = 0;
			$dns_auto_lookup_results = 0;
		}
		elseif($dns_auto_lookup == 2)
		{
			$dns_auto_lookup_results = 1;
		}
		elseif($dns_auto_lookup == 1)
		{
			$dns_auto_lookup_results = 0;
		}
		
		if(!$dns_auto_lookup) $dns_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this ipaddress it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->NS_getHostnames($vector, $dnsdbapi, $automatic); // always returns an array, even if empty
		
		if($this->dnsdbapi_none)
		{
			$this->shellOut(__('No keys are available'), $logScope, 'notice');
			return $results;
		}
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'error');
			return false;
		}
		
		// track the hexillion balance
		$this->hex_balance = $this->NS_GetHexBalance();
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $hostname => $details)
			{
				// check/add the hostname to the vectors table
				if(!$vector_hostname_id = $this->VectorHostname->checkAdd($hostname, 'hostname', $dns_auto_lookup_results)) continue;
				
				// update the $dns_auto_lookup on the hostnames table
				$this->VectorHostname->Hostname->updateDnsLookupLevel($vector_hostname_id, $dns_auto_lookup_results);
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorHostname->VectorSource->add($vector_hostname_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateIpaddressDns-'. $vector_ipaddress_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the ipaddress record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorIpaddress->Ipaddress->updateDNSdates($vector_ipaddress_id, true, $updated, $dns_auto_lookup_this, $dnsdbapi);
		
		// track this transaction
		// add errors later
		$this->VectorIpaddress->DnsTransactionLog->addLog($vector_ipaddress_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function updateHostnameVirusTotal($vector_hostname_id = false, $auto_lookup_virustotal = 0, $automatic = false)
	{
	/*
	 * Does an virus total dns lookup on a hostname and stores the results in the database
	 */
	 	$this->modelError = false;
	 	
	 	if(!$vector_hostname_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, 'virustotal', 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorHostname->id = $vector_hostname_id;
		if(!$vector = $this->VectorHostname->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_hostname_id);
	 		$this->shellOut($this->modelError, 'virustotal', 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), 'virustotal');
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$auto_lookup_virustotal_this = $auto_lookup_virustotal;
		
		// what the auto lookup should be set to for the results
		$auto_lookup_virustotal_results = 0;
		
		if($auto_lookup_virustotal == 3)
		{
			$auto_lookup_virustotal_this = 0;
			$auto_lookup_virustotal_results = 0;
		}
		elseif($auto_lookup_virustotal == 2)
		{
			$auto_lookup_virustotal_results = 1;
		}
		elseif($auto_lookup_virustotal == 1)
		{
			$auto_lookup_virustotal_results = 0;
		}
		
		if(!$auto_lookup_virustotal) $auto_lookup_virustotal = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this hostname if we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->VectorIpaddress->VT_getIps($vector, $automatic, $vector_hostname_id); // always returns an array, even if empty
		
		if($this->VectorIpaddress->VT_isDisabled())
		{
			$this->shellOut(__('We hit their limit'), 'virustotal', 'notice');
			//return $results;
		}
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, 'virustotal', 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $ipaddress => $details)
			{
				// check/add the ipaddress to the vectors table
				if(!$vector_ipaddress_id = $this->VectorIpaddress->checkAdd($ipaddress, 'ipaddress', $auto_lookup_virustotal_results, 'auto_lookup_virustotal')) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorHostname->VectorSource->add($vector_ipaddress_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateHostnameVirusTotal-'. $vector_hostname_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the hostname record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorHostname->Hostname->updateVirusTotalDates($vector_hostname_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorHostname->DnsTransactionLog->addLog($vector_hostname_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), 'virustotal');
		
		// return the results back
		return $results;
	}
	
	public function updateIpaddressVirusTotal($vector_ipaddress_id = false, $auto_lookup_virustotal = 0, $automatic = false)
	{
	/*
	 * Does an virus total dns lookup on an ipaddress and stores the results in the database
	 */
	 	$this->modelError = false;
	 	
	 	if(!$vector_ipaddress_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, 'virustotal', 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorIpaddress->id = $vector_ipaddress_id;
		if(!$vector = $this->VectorIpaddress->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_ipaddress_id);
	 		$this->shellOut($this->modelError, 'virustotal', 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), 'virustotal');
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$auto_lookup_virustotal_this = $auto_lookup_virustotal;
		
		// what the auto lookup should be set to for the results
		$auto_lookup_virustotal_results = 0;
		
		if($auto_lookup_virustotal == 3)
		{
			$auto_lookup_virustotal_this = 0;
			$auto_lookup_virustotal_results = 0;
		}
		elseif($auto_lookup_virustotal == 2)
		{
			$auto_lookup_virustotal_results = 1;
		}
		elseif($auto_lookup_virustotal == 1)
		{
			$auto_lookup_virustotal_results = 0;
		}
		
		if(!$auto_lookup_virustotal) $auto_lookup_virustotal = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this ipaddress if we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->VectorHostname->VT_getHostnames($vector, $automatic, $vector_ipaddress_id); // always returns an array, even if empty
		
		if($this->VectorHostname->VT_isDisabled())
		{
			$this->modelError = __('We hit their limit');
			$this->shellOut($this->modelError, 'virustotal', 'notice');
			return $results;
		}
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, 'virustotal', 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $hostname => $details)
			{
				// check/add the hostname to the vectors table
				if(!$vector_hostname_id = $this->VectorHostname->checkAdd($hostname, 'hostname', $auto_lookup_virustotal_results, 'auto_lookup_virustotal')) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorIpaddress->VectorSource->add($vector_hostname_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateIpaddressVirusTotal-'. $vector_ipaddress_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the ipaddress record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorIpaddress->Ipaddress->updateVirusTotalDates($vector_ipaddress_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorIpaddress->DnsTransactionLog->addLog($vector_ipaddress_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), 'virustotal');
		
		// return the results back
		return $results;
	}
	
	public function updateHostnamePassiveTotal($vector_hostname_id = false, $dns_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Does an nslookup on a hostname and stores the results in the database
	 */
	 	$logScope = 'passivetotal';
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->passivetotal_stats = array();
		$this->passivetotal_none = false;
	 	
	 	if(!$vector_hostname_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorHostname->id = $vector_hostname_id;
		if(!$vector = $this->VectorHostname->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_hostname_id);
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$dns_auto_lookup_this = $dns_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$dns_auto_lookup_results = 0;
		
		if($dns_auto_lookup == 3)
		{
			$dns_auto_lookup_this = 0;
			$dns_auto_lookup_results = 0;
		}
		elseif($dns_auto_lookup == 2)
		{
			$dns_auto_lookup_results = 1;
		}
		elseif($dns_auto_lookup == 1)
		{
			$dns_auto_lookup_results = 0;
		}
		
		if(!$dns_auto_lookup) $dns_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this hostname it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->PT_getIps($vector, $automatic); // always returns an array, even if empty
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $ipaddress => $details)
			{
				// check/add the ipaddress to the vectors table
				if(!$vector_ipaddress_id = $this->VectorIpaddress->checkAdd($ipaddress, 'ipaddress', $dns_auto_lookup_results)) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorHostname->VectorSource->add($vector_ipaddress_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateHostnamePassiveTotal-'. $vector_hostname_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the hostname record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorHostname->Hostname->updatePassiveTotalDates($vector_hostname_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorHostname->DnsTransactionLog->addLog($vector_hostname_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function updateIpaddressPassiveTotal($vector_ipaddress_id = false, $dns_auto_lookup = 0, $automatic = false)
	{
	/*
	 * Does an nslookup on a ipaddress and stores the results in the database
	 */
	 	$logScope = 'passivetotal';
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->passivetotal_stats = array();
	 	
	 	if(!$vector_ipaddress_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	$this->VectorIpaddress->id = $vector_ipaddress_id;
		if(!$vector = $this->VectorIpaddress->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_ipaddress_id);
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$dns_auto_lookup_this = $dns_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$dns_auto_lookup_results = 0;
		
		if($dns_auto_lookup == 3)
		{
			$dns_auto_lookup_this = 0;
			$dns_auto_lookup_results = 0;
		}
		elseif($dns_auto_lookup == 2)
		{
			$dns_auto_lookup_results = 1;
		}
		elseif($dns_auto_lookup == 1)
		{
			$dns_auto_lookup_results = 0;
		}
		
		if(!$dns_auto_lookup) $dns_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this ipaddress it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->PT_getHostnames($vector, $automatic); // always returns an array, even if empty
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $hostname => $details)
			{
				// check/add the hostname to the vectors table
				if(!$vector_hostname_id = $this->VectorHostname->checkAdd($hostname, 'hostname', $dns_auto_lookup_results)) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorIpaddress->VectorSource->add($vector_hostname_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateIpaddressPassiveTotal-'. $vector_ipaddress_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the ipaddress record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorIpaddress->Ipaddress->updatePassiveTotalDates($vector_ipaddress_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorIpaddress->DnsTransactionLog->addLog($vector_ipaddress_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function updateHostnameHexillion($vector_hostname_id = false, $hexillion_auto_lookup = 0, $automatic = false)
	{
	 	$logScope = 'hexillion';
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->hexillion_stats = array();
		$this->hexillion_none = false;
	 	
	 	if(!$vector_hostname_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	// check if hexillion is disabled before we even begin queries, as there is no point
	 	if($this->Hex_isDisabled())
	 	{
	 		$this->modelError = __('Hexillion is disabled.');
	 		return false;
	 	}
	 	
	 	$this->VectorHostname->id = $vector_hostname_id;
		if(!$vector = $this->VectorHostname->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_hostname_id);
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$hexillion_auto_lookup_this = $hexillion_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$hexillion_auto_lookup_results = 0;
		
		if($hexillion_auto_lookup == 3)
		{
			$hexillion_auto_lookup_this = 0;
			$hexillion_auto_lookup_results = 0;
		}
		elseif($hexillion_auto_lookup == 2)
		{
			$hexillion_auto_lookup_results = 1;
		}
		elseif($hexillion_auto_lookup == 1)
		{
			$hexillion_auto_lookup_results = 0;
		}
		
		if(!$hexillion_auto_lookup) $hexillion_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this hostname it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->Hex_getIps($vector, $automatic); // always returns an array, even if empty
		
	 	
	 	// check if hexillion is disabled before we even begin queries, as there is no point
	 	if($this->Hex_isDisabled())
	 	{
	 		$this->modelError = __('Hexillion is disabled.');
	 		return false;
	 	}
		
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $ipaddress => $details)
			{
				// check/add the ipaddress to the vectors table
				if(!$vector_ipaddress_id = $this->VectorIpaddress->checkAdd($ipaddress, 'ipaddress', $hexillion_auto_lookup_results, 'hexillion_auto_lookup')) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorHostname->VectorSource->add($vector_ipaddress_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateHostnameHexillion-'. $vector_hostname_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the hostname record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorHostname->Hostname->updateHexillionDates($vector_hostname_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorHostname->DnsTransactionLog->addLog($vector_hostname_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function updateIpaddressHexillion($vector_ipaddress_id = false, $hexillion_auto_lookup = 0, $automatic = false)
	{
	 	$logScope = 'hexillion';
	 	
	 	$this->modelError = false;
	 	
		// make sure they're reset
		$this->hexillion_stats = array();
	 	
	 	if(!$vector_ipaddress_id)
	 	{
	 		$this->modelError = __('Unknown Vector id.');
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
	 	}
	 	
	 	// check if hexillion is disabled before we even begin queries, as there is no point
	 	if($this->Hex_isDisabled())
	 	{
	 		$this->modelError = __('Hexillion is disabled.');
	 		return false;
	 	}
	 	
	 	$this->VectorIpaddress->id = $vector_ipaddress_id;
		if(!$vector = $this->VectorIpaddress->field('vector'))
		{
			$this->modelError = __('Unknown Vector with id: %s', $vector_ipaddress_id);
	 		$this->shellOut($this->modelError, $logScope, 'error');
	 		return false;
		}
		
		$time_start = microtime(true);
		$this->shellOut(__('Lookup starting for: %s', $vector), $logScope);
		
		//// track the dns auto lookup setting
		// this auto lookup setting
		$hexillion_auto_lookup_this = $hexillion_auto_lookup;
		
		// what the auto lookup should be set to for the results
		$hexillion_auto_lookup_results = 0;
		
		if($hexillion_auto_lookup == 3)
		{
			$hexillion_auto_lookup_this = 0;
			$hexillion_auto_lookup_results = 0;
		}
		elseif($hexillion_auto_lookup == 2)
		{
			$hexillion_auto_lookup_results = 1;
		}
		elseif($hexillion_auto_lookup == 1)
		{
			$hexillion_auto_lookup_results = 0;
		}
		
		if(!$hexillion_auto_lookup) $hexillion_auto_lookup = 0;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		// only exempt this ipaddress it we're in the cron
		if($automatic)
		{
			$this->NS_setExemptIps(explode(',', Configure::read('AppConfig.Nslookup.exemption_ips')));
			$this->NS_setExemptHosts(explode(',', Configure::read('AppConfig.Nslookup.exemption_hosts')));
		}
		
		$results = $this->Hex_getHostnames($vector, $automatic); // always returns an array, even if empty
	 	
	 	// check if hexillion is disabled before we even begin queries, as there is no point
	 	if($this->Hex_isDisabled())
	 	{
	 		$this->modelError = __('Hexillion is disabled.');
	 		return false;
	 	}
		
		if(!$results and $this->modelError)
		{
			$this->shellOut($this->modelError, $logScope, 'warning');
			return false;
		}
		
		$this->recordNew = false;
		
		$result_count = 0;
		$transaction_sources = array();
		$error_codes = '';
		
		foreach($results as $source => $items)
		{
			foreach($items as $hostname => $details)
			{
				// check/add the hostname to the vectors table
				if(!$vector_hostname_id = $this->VectorHostname->checkAdd($hostname, 'hostname', $hexillion_auto_lookup_results, 'hexillion_auto_lookup')) continue;
				
				// check/update the record in the nslookup table
				$this->checkAdd($vector_hostname_id, $vector_ipaddress_id, $source, $details);
				
				// add a vector source entry
				$this->VectorIpaddress->VectorSource->add($vector_hostname_id, 'dns', $source, $this->recordCreatedDate, $this->alias. '::updateIpaddressHexillion-'. $vector_ipaddress_id. '-'. $vector);
				
				$result_count++;
			}
			$transaction_sources[] = $source;
		}
		
		// mark the dates in the ipaddress record in the database as checked
		$updated = false;
		if($this->recordNew) $updated = true;
		
		// go through the vectors model as it's alias
		$this->VectorIpaddress->Ipaddress->updateHexillionDates($vector_ipaddress_id, true, $updated);
		
		// track this transaction
		// add errors later
		$this->VectorIpaddress->DnsTransactionLog->addLog($vector_ipaddress_id, $result_count, implode(',', $transaction_sources), $automatic);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->shellOut(__('Lookup completed for: %s - result count: %s - took %s seconds', $vector, $result_count, $time), $logScope);
		
		// return the results back
		return $results;
	}
	
	public function checkAdd($vector_hostname_id = false, $vector_ipaddress_id = false, $source = false, $data = array())
	{
	/*
	 * Checks to see if an entry already exists
	 * if not, add it
	 * if so, update it
	 * add an entry in the log table
	 */
		if(!$vector_hostname_id or !$vector_ipaddress_id)
		{
			$this->modelError = __('Unknown hostname or ipaddress id.');
			$this->shellOut($this->modelError, 'model', 'error');
			return false;
		}
		
		if(!$source)
		{
			$this->modelError = __('Unknown source.');
			$this->shellOut($this->modelError, 'model', 'error');
			return false;
		}
		
		// for later use
		$this->recordUpdated = false; 
		$return = false;
		
		$conditions = array(
			'vector_hostname_id' => $vector_hostname_id,
			'vector_ipaddress_id' => $vector_ipaddress_id,
			'source' => $source,
		);
		$cacKey = md5(serialize($conditions));
		
		if(isset($this->checkAddCache[$cacKey])) return $this->checkAddCache[$cacKey];
		
		$id = false;
		
	 	$record = $this->find('first', array(
	 		'recursive' => -1,
	 		'conditions' => $conditions,
	 	));
	 	
		if(!$record)
		{
			$this->create();
			$this->data = array_merge(array(
				'vector_hostname_id' => $vector_hostname_id,
				'vector_ipaddress_id' => $vector_ipaddress_id,
				'source' => $source,
			), $data);
			$return = $this->save($this->data);
			$this->recordNew = true;
			$this->recordCreatedDate = (isset($data['created'])?$data['created']:date('Y-m-d H:i:s'));
		}
		else
		{
			if(isset($data['first_seen'])) unset($data['first_seen']);
			// update the and last seen
			$this->id = $record[$this->alias]['id'];
			$this->data = $data;
			$return = $this->save($this->data);
			$this->recordCreatedDate = $this->field('created');
		}
		
		if($return)
		{
			$this->checkAddCache[$cacKey] = $this->id;
			
			if(!$this->recordNew)
			{
				if(isset($data['first_seen'])) unset($data['first_seen']);
			}
			
			// record a transaction log for this
			$this->NslookupLog->create();
			$this->NslookupLog->data = array_merge(array(
				'nslookup_id' => $this->id,
				'vector_hostname_id' => $vector_hostname_id,
				'vector_ipaddress_id' => $vector_ipaddress_id,
				'source' => $source,
			), $data);
			
			$this->NslookupLog->save($this->NslookupLog->data);
		}
		
		return $return;
	}
	
	public function fixDomaintoolsHistory()
	{
		$records = $this->find('all', array(
			'conditions' => array(
				'Nslookup.source' => array('domaintools_dns_hist', 'domaintools_dns_history'),
			),
			'order' => array(
				'Nslookup.vector_hostname_id' => 'ASC',
				'Nslookup.vector_ipaddress_id' => 'ASC',
				'Nslookup.created' => 'ASC',
			),
		));
		
		$this->shellOut(__('Found %s total records.', count($records)), 'model');
		
		$duplicates = array();
		foreach($records as $record)
		{
			$key = $record['Nslookup']['vector_hostname_id'].'-'.$record['Nslookup']['vector_ipaddress_id'];
			$duplicates[$key][] = $record;
		}
		
		$this->shellOut(__('Found %s legit records.', count($duplicates)), 'model');
		$this->shellOut(__('Found %s duplicate records.', (count($records) - count($duplicates))), 'model');
		
		$delete_ids = array();
		
		$has_duplicates = 0;
		foreach($duplicates as $duplicate)
		{
			if(count($duplicate) <= 1) continue;
			$has_duplicates++;
			
			// find the first record to make as an original
			$created = time();
			$first = false;
			$first_id = false;
			$first_seen = time();
			$last_seen = false;
			$modified = false;
			
			$data = array();
			
			foreach($duplicate as $record)
			{
				$this_created = strtotime($record['Nslookup']['created']);
				$this_first_seen = strtotime($record['Nslookup']['first_seen']);
				$this_last_seen = strtotime($record['Nslookup']['last_seen']);
				$this_modified = strtotime($record['Nslookup']['modified']);
				if($created > $this_created)
				{
					$created = $this_created;
					$first = $record;
					$first_id = $record['Nslookup']['id'];
				}
				if($first_seen > $this_first_seen)
				{
					$first_seen = $this_first_seen;
				}
				if($last_seen < $this_last_seen)
				{
					$last_seen = $this_last_seen;
				}
				if($modified < $this_modified)
				{
					$modified = $this_modified;
				}
			}
			
			// track the records to delete
			foreach($duplicate as $record)
			{
				$this_id = $record['Nslookup']['id'];
				if($record['Nslookup']['id'] != $first_id)
				{
					$delete_ids[$this_id] = $this_id;
				}
			}
			
			$data = array(
				'first_seen' => date('Y-m-d H:i:s', $first_seen),
				'last_seen' => date('Y-m-d H:i:s',$last_seen),
				'modified' => date('Y-m-d H:i:s',$modified),
			);
			
			// update the record
			$this->id = $first_id;
			$this->data = $data;
			$this->save($this->data);
		}
		
		$this->shellOut(__('Found %s records with duplicates.', $has_duplicates), 'model');
		$this->shellOut(__('Found %s records to delete.', count($delete_ids)), 'model');
		
		$this->deleteAll(array('Nslookup.id' => $delete_ids), false);
		$this->shellOut(__('Deleted duplicate records.'), 'model');
		
		$this->shellOut(__('Deleting duplicate logs.'), 'model');
		$this->NslookupLog->deleteAll(array('NslookupLog.nslookup_id' => $delete_ids), false);
		$this->shellOut(__('Deleted duplicate logs.'), 'model');
	}
	
	public function dashboardOverviewStats()
	{
		$stats = array(
			'total' => array('name' => __('Total'), 'value' => $this->find('count')),
		);
		
		return $stats;
	}
}

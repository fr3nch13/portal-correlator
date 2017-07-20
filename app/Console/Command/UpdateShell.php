<?php

class UpdateShell extends AppShell
{
	// the models to use
	public $uses = array('Vector', 'Hostname', 'Ipaddress', 'Nslookup', 'VectorSource', 'VectorType');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Update Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Update Shell runs all needed jobs to update production\'s database.'));
		
		$parser->addSubcommand('vector_scan', array(
			'help' => __d('cake_console', 'Scans vectors and makes sure they are associated with a hostnames/ipaddresses listing.'),
			'parser' => array(
				'arguments' => array(
//					'minutes' => array('help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'))
				)
			)
		));
		
		$parser->addSubcommand('update_geoip', array(
			'help' => __d('cake_console', 'Adds geoip records for existing ip addresses.'),
		));
		
		$parser->addSubcommand('update_whois', array(
			'help' => __d('cake_console', 'Adds whois records for existing ip addresses and hostnames.'),
		));
		
		$parser->addSubcommand('vector_sources', array(
			'help' => __d('cake_console', 'Fixes the large amount of Vector Sources.'),
		));
		
		$parser->addSubcommand('update_assessments', array(
			'help' => __d('cake_console', 'Scan and assign Assessments.'),
		));
		
		
		
		return $parser;
	}
	
	public function vector_sources()
	{
		return $this->VectorSource->fixSources();
	}
	
	public function update_geoip()
	{
		$vectors = $this->Vector->find('list', array(
			'recursive' => -1,
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => array(
				'Vector.type' => 'ipaddress',
			),
		));
		
		foreach($vectors as $vector_id => $vector)
		{
			$this->Vector->Geoip->lookupVectorId($vector_id, $vector);
		}
	}
	
	public function virustotal_fix()
	{
		$this->Hostname->recursive = -1;
		$this->Hostname->updateAll(
			array('Hostname.auto_lookup_virustotal' => 0),
			array('Hostname.dns_auto_lookup' => 0)
		);
		$this->Hostname->updateAll(
			array('Hostname.auto_lookup_virustotal' => 1),
			array('Hostname.dns_auto_lookup' => 1)
		);
		$this->Hostname->updateAll(
			array('Hostname.auto_lookup_virustotal' => 2),
			array('Hostname.dns_auto_lookup' => 2)
		);
		$this->Hostname->updateAll(
			array('Hostname.auto_lookup_virustotal' => 3),
			array('Hostname.dns_auto_lookup' => 3)
		);
		
		$this->Ipaddress->recursive = -1;
		$this->Ipaddress->updateAll(
			array('Ipaddress.auto_lookup_virustotal' => 0),
			array('Ipaddress.dns_auto_lookup' => 0)
		);
		$this->Ipaddress->updateAll(
			array('Ipaddress.auto_lookup_virustotal' => 1),
			array('Ipaddress.dns_auto_lookup' => 1)
		);
		$this->Ipaddress->updateAll(
			array('Ipaddress.auto_lookup_virustotal' => 2),
			array('Ipaddress.dns_auto_lookup' => 2)
		);
		$this->Ipaddress->updateAll(
			array('Ipaddress.auto_lookup_virustotal' => 3),
			array('Ipaddress.dns_auto_lookup' => 3)
		);
	}
	
	public function update_whois()
	{
		$vectors = $this->Vector->find('list', array(
			'recursive' => -1,
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => array(
				'Vector.type' => array('ipaddress', 'hostname'),
			),
		));
		
		$this->out(__('Found %s hostnames/ipaddresses to create records for.', count($vectors)));
		
		$added = $updated = 0;
		foreach($vectors as $vector_id => $vector)
		{
			$this->Vector->Whois->checkAddBlank($vector_id, 0);
			if($this->Vector->Whois->addUpdate == 1) $added++;
			elseif($this->Vector->Whois->addUpdate == 2) $updated++;
		}
		$this->out(__('Added: %s - Updated: %s', $added, $updated));
	}
	
	public function vector_scan()
	{
	/*
	 * Scans vectors and makes sure they are associated with a hostnames/ipaddresses listing.
	 * 
	 */
		
		// Hostnames
		$vectors = $this->Vector->typeList('hostname', false, true);
		$this->out($this->Vector->shellOut());
		
		// add the new records
		$results = $this->Hostname->checkAddBlank(array_keys($vectors));
		$this->out($this->Hostname->shellOut());
		
		// Ip Addresses
		$vectors = $this->Vector->typeList('ipaddress', false, true);
		$this->out($this->Vector->shellOut());
		
		// add the new records
		$results = $this->Ipaddress->checkAddBlank(array_keys($vectors));
		$this->out($this->Ipaddress->shellOut());
	}
	
	public function vector_source_scan_user()
	{
		// 
		$results = $this->Vector->VectorSource->find('list', array(
			'fields' => array('VectorSource.id', 'VectorSource.source'),
		));
pr($results);
	}
	
	public function fix_domaintools()
	{
		$this->Nslookup->fixDomaintoolsHistory();
	}
	
	public function update_assessments()
	{
		$this->VectorType->updateAssessments();
	}
}
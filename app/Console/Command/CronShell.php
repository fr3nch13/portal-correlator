<?php

class CronShell extends AppShell
{
	// the models to use
	public $uses = ['Main', 'User', 'LoginHistory', 'Vector', 'Hostname', 'Ipaddress', 'Whois', 'ImportManager'];
	
	public $thisParser = false;
	
	public function startup() 
	{
//		$this->clear();
		$this->out('Cron Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$this->thisParser = parent::getOptionParser();
		
		$this->thisParser->description(__d('cake_console', 'The Cron Shell runs all needed cron jobs'));
		
		$this->thisParser->addSubcommand('failed_logins', array(
			'help' => __d('cake_console', 'Emails a list of failed logins to the admins every 5 minutes'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => 10,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_dns_hostnames', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for hostnames based on a time since it was last looked up'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most hostnames to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
					'scope' => array(
						'help' => __d('cake_console', 'The scope of the updates. 0 = both, 1 = local only, 2 = remote only'),
						'short' => 's',
						'default' => 0,
						'choices'
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_dns_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for ip addresses based on a time since it was last looked up'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most ip addresses to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
					'scope' => array(
						'help' => __d('cake_console', 'The scope of the updates. 0 = both, 1 = local only, 2 = remote only'),
						'short' => 's',
						'default' => 0,
						'choices'
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_dnsdbapi_hostnames', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for hostnames based on a time since it was last looked up. Specific to DnsdbApi.'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => (1440 * 30), // 1 month
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most hostnames to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_dnsdbapi_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for ip addresses based on a time since it was last looked up. Specific to DnsdbApi.'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => (1440 * 30), // 1 month
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most ip addresses to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_virustotal', array(
			'help' => __d('cake_console', 'Does the one-off scans against Virus Total'),
			'parser' => array(
			),
		));
		
		$this->thisParser->addSubcommand('update_virustotal_hostnames', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for hostnames based on a time since it was last looked up. Specific to VirusTotal.'),
			'parser' => array(
			),
		));
		
		$this->thisParser->addSubcommand('update_virustotal_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for ip addresses based on a time since it was last looked up. Specific to VirusTotal.'),
			'parser' => array(
			),
		));
		
		$this->thisParser->addSubcommand('update_passivetotal_hostnames', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for hostnames based on a time since it was last looked up. Specific to PassiveTotal.'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from %s minutes ago.', (1440 * 30)),
						'short' => 'm',
						'default' => (1440 * 30), // 1 month
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most hostnames to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_passivetotal_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information for ip addresses based on a time since it was last looked up. Specific to PassiveTotal.'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from %s minutes ago.', (1440 * 30)),
						'short' => 'm',
						'default' => (1440 * 30), // 1 month
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most ip addresses to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_hexillion_hostnames', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information from %s for %s based on a time since it was last looked up. Specific to %s.', __('Hexillion'), __('Hostnames'), __('Hexillion')),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 1 day ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most %s to be looked up with in one run.', __('Hostnames')),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_hexillion_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the DNS lookup information from %s for %s based on a time since it was last looked up. Specific to %s.', __('Hexillion'), __('IP Addresses'), __('Hexillion')),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 1 day ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most %s to be looked up with in one run.', __('IP Addresses')),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_whois_hostnames', array(
			'help' => __d('cake_console', 'Updates the Whois records for hostnames and  ip addresses based on a time since it was last looked up'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 1 day ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most whois records to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_whois_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the Whois records for hostnames and  ip addresses based on a time since it was last looked up'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 1 day ago.'),
						'short' => 'm',
						'default' => 1440, // 1 day
					),
					'limit' => array(
						'help' => __d('cake_console', 'The most whois records to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('update_geoip_ipaddresses', array(
			'help' => __d('cake_console', 'Updates the Geoip records for ip addresses based on a time since it was last looked up'),
			'parser' => array(
				'options' => array(
					'limit' => array(
						'help' => __d('cake_console', 'The most Geoip records to be looked up with in one run.'),
						'short' => 'l',
						'default' => 100,
					),
				),
			),
		));
		
		$this->thisParser->addSubcommand('check_whoiser', array(
			'help' => __d('cake_console', 'Checks the status of submitted searches to Whoiser.'),
			'parser' => array(
			),
		));
		
		$this->thisParser->addSubcommand('update_whoiser', array(
			'help' => __d('cake_console', 'Updated Whois records from Completed Whoiser Searches..'),
			'parser' => array(
			),
		));
		
		$this->thisParser->addSubcommand('update_imports', array(
			'help' => __d('cake_console', 'Runs the automatic Import Managers and adds new possible Imports'),
			'parser' => array(
				'options' => array(
				),
			),
		));
		
		$this->thisParser->addSubcommand('purge_import_logs', array(
			'help' => __d('cake_console', 'Purges the import Manager Logs from the database to an actual log file after 1 year.'),
			'parser' => array(
				'options' => array(
				),
			),
		));
		
		$this->thisParser->addSubcommand('vt_test', array(
			'help' => __d('cake_console', 'testing'),
			'parser' => array(
				'options' => array(
				),
			),
		));
		
		return $this->thisParser;
	}
	
	public function failed_logins()
	{
	/*
	 * Emails a list of failed logins to the admins every 5 minutes
	 * Only sends an email if there was a failed login
	 * Everything is taken care of in the Task
	 */
		$FailedLogins = $this->Tasks->load('Utilities.FailedLogins')->execute($this);
	}
	
	public function send_digest_emails()
	{
	/*
	 * Send the digest emails that have been built up
	 */
		$subcommands = array_keys($this->thisParser->subcommands());
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		foreach($subcommands as $subcommand)
		{
			$Email->set('digest_file', TMP. $subcommand);
			if($Email->executeDigest())
			{
				$this->out(__('Sent digest email for %s - results: %s', $subcommand, $Email->getVariable('digest_results_count')), 1, Shell::QUIET);
			}
			else
			{
				$this->out(__('Failed to send digest email for %s - results: %s', $subcommand, $Email->getVariable('digest_error')), 1, Shell::QUIET);
			}
		}
	}
	
	public function send_digest_backup_emails()
	{
		/// reads the backup logs, and emails them to the admins
		$this->out(__('Sending logs for Rsync Backups as a Digest Email'), 1, Shell::QUIET);
		
		if(!$results = scandir(TMP. 'logs', 0))
		{
			$this->out(__('No files found.'));
			return false;
		}
		
		$body = array();
		$file_count = 0;
		$file_count_www = 0;
		$file_count_mysql = 0;
		$sep = '-------------------------';
		foreach($results as $file)
		{
			if(preg_match('/^cron_backup-(.*).log$/i', $file))
			{
				$file_count++;
				if(preg_match('/www/i', $file))
				{
					$file_count_www++;
				}
				if(preg_match('/mysql/i', $file))
				{
					$file_count_mysql++;
				}
				$body[] = __("File:\n%s", $file);
				$body[] = $sep;
				$body[] = file_get_contents(TMP. 'logs'. DS. $file);
				$body[] = $sep;
				$body[] = '';
				if(is_writable(TMP. 'logs'. DS. $file))
				{
					unlink(TMP. 'logs'. DS. $file);
				}
			}
		}
		
		$subject = __('(total: %s, www: %s, mysql: %s)', $file_count, $file_count_www, $file_count_mysql);
		$this->out(__('Backup Log counts : %s', $subject), 1, Shell::QUIET);
		
		$subject = __('Cron Digest - %s - %s', __('Rsync Backups'), $subject);
		
		$body = implode("    \n", $body);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $body);
		
		// send the email
		if($Email->execute())
		{
			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
		}
		else
		{
			$email_results = __('Email failed to send.');
		}
	}
	
	public function update_dns_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdateDNS($this->params['minutes'], $this->params['limit'], $this->params['scope']);
		
		$final_results = $this->Hostname->final_results;
		$hex_balance = $this->Hostname->hex_balance;
		
		// filter out for the 'No Local'
		if(preg_match('/No\s+Local/i', $final_results))
		{
			return true;
		}
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		$subject = $body = $final_results;
		
		$force_send = false;
		if($hex_balance !== false)
		{
			$body = __('Hexillion balance: %s', $hex_balance). "\n". $body;
			
			if($hex_balance == 0)
			{
				$subject = __('Hexillion Empty! - '). $subject;
				$force_send = true;
			}
		}
		$Email->set('force_send', $force_send);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron DNS - %s', $subject));
		$Email->set('body', $body);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'nslookup');
	}
	
	public function update_dns_ipaddresses()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Ipaddress->batchUpdateDNS($this->params['minutes'], $this->params['limit'], $this->params['scope']);
		
		$final_results = $this->Ipaddress->final_results;
		$hex_balance = $this->Ipaddress->hex_balance;
		
		// filter out for the 'No Local'
		if(preg_match('/No\s+Local/i', $final_results))
		{
			return true;
		}
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		$subject = $body = $final_results;
		
		$force_send = false;
		if($hex_balance !== false)
		{
			$body = __('Hexillion balance: %s', $hex_balance). "\n". $body;
			
			if($hex_balance == 0)
			{
				$subject = __('Hexillion Empty! - '). $subject;
				$force_send = true;
			}
		}
		$Email->set('force_send', $force_send);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron DNS - %s', $subject));
		$Email->set('body', $body);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'nslookup');
	}
	
	public function update_dnsdbapi_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdateDNSdbapi($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Hostname->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron DNSdbapi - ');
		
		$final_results = array();
		
		// get the dnsdb api stats
		if($this->Hostname->Vector->dnsdbapi_none)
		{
			$final_results[] = ' ';
			$final_results[] = __('ALERT: All of the dnsdbapi keys have reached their limits.');
			$final_results[] = ' ';
			$subject .= __('NO KEYS AVAILABLE - ');
		}
		
		$subject .= $final_results[] = $this->Hostname->final_results;
		
		if($this->Hostname->Vector->dnsdbapi_stats)
		{
			foreach($this->Hostname->Vector->dnsdbapi_stats as $key => $dnsdbapi_stats)
			{
				$final_results[] = __('-- Stats for key: %s...', substr($key, 0, 5));
				if(is_array($dnsdbapi_stats))
				{
					foreach($dnsdbapi_stats as $dnsdbapi_stat_key => $dnsdbapi_stat_value)
					{
						$final_results[] = __('-- %s : %s', $dnsdbapi_stat_key, $dnsdbapi_stat_value);
					}
				}
				$final_results[] = '----';
			}
		}
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'dnsdbapi');
	}
	
	public function update_dnsdbapi_ipaddresses()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Ipaddress->batchUpdateDNSdbapi($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Ipaddress->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron DNSdbapi - ');
		
		$final_results = array();
		
		// get the dnsdb api stats
		if($this->Ipaddress->Vector->dnsdbapi_none)
		{
			$final_results[] = ' ';
			$final_results[] = __('ALERT: All of the dnsdbapi keys have reached their limits.');
			$final_results[] = ' ';
			$subject .= __('NO KEYS AVAILABLE - ');
		}
		
		$subject .= $final_results[] = $this->Ipaddress->final_results;
		
		if($this->Ipaddress->Vector->dnsdbapi_stats)
		{
			foreach($this->Ipaddress->Vector->dnsdbapi_stats as $key => $dnsdbapi_stats)
			{
				$final_results[] = __('-- Stats for key: %s...', substr($key, 0, 5));
				if(is_array($dnsdbapi_stats))
				{
					foreach($dnsdbapi_stats as $dnsdbapi_stat_key => $dnsdbapi_stat_value)
					{
						$final_results[] = __('-- %s : %s', $dnsdbapi_stat_key, $dnsdbapi_stat_value);
					}
				}
				$final_results[] = '----';
			}
		}
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'dnsdbapi');
	}
	
	public function update_virustotal()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Vector->batchUpdateNewVirusTotal();
	}
	
	public function update_virustotal_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdateVirusTotal();
		
		// don't send emails since this will run every minute
		return true;
		
		$subject = __('Cron VirusTotal - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Hostname->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'virustotal');
	}
	
	public function update_virustotal_ipaddresses()
	{
		// get a list of ipaddresses that need to be looked up
		$results = $this->Ipaddress->batchUpdateVirusTotal();
		
		// don't send emails since this will run every minute
		return true;
		
		$subject = __('Cron VirusTotal - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Ipaddress->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'virustotal');
	}
	
	public function update_passivetotal_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdatePassiveTotal($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Hostname->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron PassiveTotal - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Hostname->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'passivetotal');
	}
	
	public function update_passivetotal_ipaddresses()
	{
		// get a list of ipaddresses that need to be looked up
		$results = $this->Ipaddress->batchUpdatePassiveTotal($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Ipaddress->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron PassiveTotal - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Ipaddress->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'passivetotal');
	}
	
	public function update_hexillion_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdateHexillion($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Hostname->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron Hexillion - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Hostname->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'hexillion');
	}
	
	public function update_hexillion_ipaddresses()
	{
		// get a list of ipaddresses that need to be looked up
		$results = $this->Ipaddress->batchUpdateHexillion($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Ipaddress->final_result_count)
		{
			return;
		}
		
		$subject = __('Cron Hexillion - ');
		
		$final_results = array();
		
		$subject .= $final_results[] = $this->Ipaddress->final_results;
		
		$final_results = implode("\n", $final_results);
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', $subject);
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'hexillion');
	}
	
	public function update_whois_hostnames()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Hostname->batchUpdateWhois($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Hostname->final_result_count)
		{
			return;
		}
		
		$final_results = $this->Hostname->final_results;
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron Whois - %s', $final_results));
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Hostname->shellOut($email_results, 'whois');
	}
	
	public function update_whois_ipaddresses()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Ipaddress->batchUpdateWhois($this->params['minutes'], $this->params['limit']);
		
		if(!$this->Ipaddress->final_result_count)
		{
			return;
		}
		
		$final_results = $this->Ipaddress->final_results;
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron Whois - %s', $final_results));
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'whois');
	}
	
	public function update_geoip_ipaddresses()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->Ipaddress->batchUpdateGeoip($this->params['limit']);
		
		if(!$this->Ipaddress->final_result_count)
		{
			return;
		}
		
		$final_results = $this->Ipaddress->final_results;
		
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)));
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		///// Make sure this is logged to a digest email
		$Email->set('digest_file', TMP. __FUNCTION__);
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron Geoip - %s', $final_results));
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
// digest now
//			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
			$email_results = __('Email sucessfully logged to digest');
		}
		else
		{
//			$email_results = __('Email failed to send.');
			$email_results = __('Email failed to log to digest.');
		}
		$this->Ipaddress->shellOut($email_results, 'whois');
	}
	
	public function check_whoiser()
	{
		$results = $this->Vector->WhoiserTransaction->checkStatuses();
	}
	
	public function update_whoiser()
	{
		$results = $this->Vector->WhoiserTransaction->getDetails();
	}
	
	public function update_imports()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->ImportManager->cronUpdate();
		$final_results = $this->ImportManager->final_results;
		
/*
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron Update Imports - %s', $final_results));
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
		}
		else
		{
			$email_results = __('Email failed to send.');
		}
		$this->ImportManager->shellOut($email_results, 'imports');
*/
	}
	
	public function purge_import_logs()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->ImportManager->ImportManagerLog->purge();
		$final_results = $this->ImportManager->ImportManagerLog->final_results;
		
/*
		// get the admin users
		$emails = $this->User->adminEmails();
		
		if(!$emails)
		{
			$this->out(__('No admin email addresses found.'));
			return false;
		}
		
		$this->out(__('Found %s admin email addresses.', count($emails)), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		//set the email parts
		$Email->set('to', $emails);
		$Email->set('subject', __('Cron Purge Import Logs - %s', $final_results));
		$Email->set('body', $final_results);
		
		// send the email
		if($Email->execute())
		{
			$email_results = __('Email sucessfully sent to: %s', implode(', ', $emails));
		}
		else
		{
			$email_results = __('Email failed to send.');
		}
		$this->ImportManager->shellOut($email_results, 'imports');
*/
	}
	
	public function update_hhs_cert()
	{
		$this->Main->update_hhs_cert();
	}
}
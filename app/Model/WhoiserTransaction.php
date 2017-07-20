<?php
App::uses('AppModel', 'Model');
/**
 * WhoiserTransaction Model
 *
 */
class WhoiserTransaction extends AppModel 
{
	// add plugins and other behaviors
	public $actsAs = array('Utilities.Whois', 'Utilities.Whoiser');
	
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);
	
	public $compile_states = array(
		0 => 'pending compile',
		1 => 'pending recompile',
		2 => 'compiling',
		3 => 'complete',
		4 => 'imported',
	);
	
	public function submitSearch($vector_id = false, $vector = array(), $user_id = false)
	{
		if(!isset($vector['Vector']['vector']))
		{
			$vector = $this->Vector->read(null, $vector_id);
		}
		
		$vector = $vector['Vector']['vector'];
		
		$results = $this->Whoiser_submitSearch($vector);
		
		if(!isset($results['success']))
		{
			$this->modelError = __('An error occurred when trying to lookup Whois records. (1)');
			return false;
		}
		elseif(!$results['success'])
		{
			$this->modelError = __('An error occurred when trying to lookup Whois records. (2)');
			if(isset($results['msg']))
			{
				$this->modelError = $results['msg'];
			}
			return false;
		}
		elseif(!isset($results['results']['status']))
		{
			$this->modelError = __('An error occurred when trying to lookup Whois records. (3)');
			return false;
		}
		
		$status = (isset($results['results']['status']['status'])?$results['results']['status']['status']:false);
		
		// create a whoiser transaction entry for the cron jobs to use to check/import searches/records
		$transaction_data = array(
			'vector_id' => $vector_id,
			'whoiser_search_id' => $results['results']['status']['search_id'],
			'user_id' => ($user_id?$user_id:0),
			'status' => $status,
		);
		
		if(!$this->checkAdd($vector_id, $transaction_data))
		{
			$this->modelError = __('An error occurred when trying to lookup Whois records. (4)');
			return false;
		}
		
		return __('Whois update request successfully submitted.');
	}
	
	public function checkStatuses()
	{
		$whoiser_transactions = $this->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				'WhoiserTransaction.status <' => 3,
			),
			'order' => array(
				'WhoiserTransaction.last_checked' => 'asc',
				'WhoiserTransaction.created' => 'asc',
			),
		));
		
		$this->shellOut(__('Found %s Whoiser Transactions to check.', count($whoiser_transactions)), 'whois');
		
		foreach($whoiser_transactions as $whoiser_transaction)
		{
			$this->shellOut(__('Checking Whoiser Transaction for the Vector: %s.', $whoiser_transaction['Vector']['vector']), 'whois');
			$results = $this->Whoiser_checkStatus($whoiser_transaction['WhoiserTransaction']['whoiser_search_id']);
			
			$this->id = $whoiser_transaction['WhoiserTransaction']['id'];
			$this->data = array(
				'last_checked' => date('Y-m-d H:i:s'),
			);
			
			if(isset($results['status']))
			{
				if($results['status'] != $whoiser_transaction['WhoiserTransaction']['status'])
				{
					$this->data['status'] = $results['status'];
					$this->data['last_changed'] = date('Y-m-d H:i:s');
					$this->shellOut(__('Whoiser Transaction status changed to "%s" for the Vector: %s.', $results['status'], $whoiser_transaction['Vector']['vector']), 'whois');
				}
			}
			
			$this->save($this->data);
		}
	}
	
	public function getDetails()
	{
		$start = microtime(true);
		$this->shellOut(__('Getting Whois records for complete Whoiser Transactions'), 'whois');
		
		$whoiser_transactions = $this->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				'WhoiserTransaction.status' => 3,
			),
			'order' => array(
				'WhoiserTransaction.last_checked' => 'asc',
				'WhoiserTransaction.created' => 'asc',
			),
		));
		
		$this->shellOut(__('Found %s Whoiser Transactions ready to retrieve records.', count($whoiser_transactions)), 'whois');
		
		foreach($whoiser_transactions as $whoiser_transaction)
		{
			$this->shellOut(__('Getting Whoiser records for the Vector: %s.', $whoiser_transaction['Vector']['vector']), 'whois');
			if(!$results = $this->Whoiser_getDetails($whoiser_transaction['WhoiserTransaction']['whoiser_search_id'])) continue;
			
			if(!isset($results['Search'])) continue;
			
			if(!$results['WhoisRecords'])
			{
				$this->shellOut(__('Found NO Whoiser records for the Vector: %s.', $whoiser_transaction['Vector']['vector']), 'whois');
				// update the transaction to mark as imported
				$this->id = $whoiser_transaction['WhoiserTransaction']['id'];
				$this->data = array(
					'status' => 4,
					'last_changed' => date('Y-m-d H:i:s'),
				);
				$this->save($this->data);
				continue;
			}
			
			$transaction_start = microtime(true);
			$this->shellOut(__('Found %s Whoiser records for the Vector: %s.', count($results['WhoisRecords']), $whoiser_transaction['Vector']['vector']), 'whois');
			
			$saved_records = 0;
			$transaction_sources = array();
			foreach($results['WhoisRecords'] as $whois_record)
			{
				if(!$mapped_record = $this->Whoiser_mapSqlSource($whois_record['WhoisRecord'])) continue;
				
				// give it a source if none is given
				$source = 'whoiser';
				if(isset($mapped_record['source']) and $mapped_record['source']) $source = $mapped_record['source'];
				
				// save the mapped record to the whois table
				if($this->Vector->Whois->updateAllRecords($whoiser_transaction['Vector']['id'], $source, $mapped_record))
				{
					$saved_records++;
					$transaction_sources[$source] = $source;
				}
			}
			
			$this->Vector->WhoisTransactionLog->addLog($whoiser_transaction['Vector']['id'], $saved_records, implode(',', $transaction_sources), false);
			
			// update the transaction to mark as imported
			$this->id = $whoiser_transaction['WhoiserTransaction']['id'];
			$this->data = array(
				'status' => 4,
				'last_changed' => date('Y-m-d H:i:s'),
			);
			
			$this->save($this->data);
				
			$transaction_end = microtime(true);
			$transaction_diff = ($transaction_end - $transaction_start);
			$this->shellOut(__('Saved %s Whoiser records for the Vector: %s. - Took: %s seconds', $saved_records, $whoiser_transaction['Vector']['vector'], $transaction_diff), 'whois');
		}

		$end = microtime(true);
		$diff = ($end - $start);
		return $this->shellOut(__('Started at: %s - Ended at: %s - Took: %s seconds', date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end), $diff), $this->alias);
	}
	
	public function checkAdd($vector_id = false, $details = array())
	{
	/*
	 * Checks to see if an entry already exists
	 * if not, add it
	 * if so, update it
	 * add an entry in the log table
	 */
		if(!$vector_id)
		{
			$this->modelError = __('Unknown vector.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
		if(!$details)
		{
			$this->modelError = __('No details given.');
			$this->shellOut($this->modelError, 'whois', 'error');
			return false;
		}
		
////
		$record = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias. '.vector_id' => $vector_id,
			),
		));
		
		// new
		if(!$record)
		{
			$this->create();
			$this->data['vector_id'] = $vector_id;
			$failed_message = __('Unable to add a new record');
		}
		// existing
		else
		{
			$this->id = $record[$this->alias]['id'];
			$failed_message = __('Unable to update an existing record');
		}
		
		$this->data = $details;
		if(!$return = $this->save($this->data))
		{
			$this->modelError = $failed_message;
			$this->shellOut($this->modelError, 'whois', 'error'); 
			return false;
		}
		
		return $return;
	}
}

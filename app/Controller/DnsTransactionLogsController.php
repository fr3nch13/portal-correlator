<?php
App::uses('AppController', 'Controller');
/**
 * DnsTransactionLogs Controller
 *
 * @property DnsTransactionLog $DnsTransactionLog
 */
class DnsTransactionLogsController extends AppController 
{
	/*
	 * Lists out the transaction log for a vector
	 */
	public function vector($vector_id = false)
	{
		$this->DnsTransactionLog->Vector->id = $vector_id;
		if (!$this->DnsTransactionLog->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'DnsTransactionLog.vector_id' => $vector_id,
		);
		
		$this->paginate['order'] = array('DnsTransactionLog.id' => 'desc');
		$this->paginate['conditions'] = $this->DnsTransactionLog->conditions($conditions, $this->passedArgs); 
		$this->set('dns_transaction_logs', $this->paginate());
	}
	
	public function admin_vector($vector_id = false)
	{
		$this->DnsTransactionLog->Vector->id = $vector_id;
		if (!$this->DnsTransactionLog->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'DnsTransactionLog.vector_id' => $vector_id,
		);
		
		$this->paginate['order'] = array('DnsTransactionLog.id' => 'desc');
		$this->paginate['conditions'] = $this->DnsTransactionLog->conditions($conditions, $this->passedArgs); 
		$this->set('dns_transaction_logs', $this->paginate());
	}
}

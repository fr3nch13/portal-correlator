<?php
App::uses('AppController', 'Controller');
/**
 * WhoisTransactionLogs Controller
 *
 * @property WhoisTransactionLog $WhoisTransactionLog
 */
class WhoisTransactionLogsController extends AppController 
{
	/*
	 * Lists out the transaction log for a vector
	 */
	public function vector($vector_id = false)
	{
		$this->WhoisTransactionLog->Vector->id = $vector_id;
		if (!$this->WhoisTransactionLog->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisTransactionLog.vector_id' => $vector_id,
		);
		
		$this->paginate['order'] = array('WhoisTransactionLog.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisTransactionLog->conditions($conditions, $this->passedArgs); 
		$this->set('whois_transaction_logs', $this->paginate());
	}
	
	public function admin_vector($vector_id = false)
	{
		$this->WhoisTransactionLog->Vector->id = $vector_id;
		if (!$this->WhoisTransactionLog->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid Vector'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisTransactionLog.vector_id' => $vector_id,
		);
		
		$this->paginate['order'] = array('WhoisTransactionLog.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisTransactionLog->conditions($conditions, $this->passedArgs); 
		$this->set('whois_transaction_logs', $this->paginate());
	}
}

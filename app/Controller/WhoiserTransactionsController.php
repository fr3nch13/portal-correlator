<?php
App::uses('AppController', 'Controller');
/**
 * WhoiserTransactions Controller
 *
 * @property WhoiserTransaction $WhoiserTransaction
 */
class WhoiserTransactionsController extends AppController 
{

//
	public function admin_index() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->WhoiserTransaction->recursive = 0;
		
		$this->paginate['order'] = array('WhoiserTransaction.id' => 'desc');
		
		$this->paginate['conditions'] = $this->WhoiserTransaction->conditions($conditions, $this->passedArgs); 
		$this->set('whoiser_transactions', $this->paginate());
		$this->set('compile_states', $this->WhoiserTransaction->compile_states);
		
	}
}
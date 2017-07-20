<?php
App::uses('AppController', 'Controller');

class IpaddressesController extends AppController 
{
	
	public function search_results()
	{
		return $this->index();
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		
		if(!isset($this->paginateModel))
			$this->paginateModel = 'Ipaddress';
		
		$conditions = [
			'Vector.bad' => 0, 
			'Vector.type' => 'ipaddress'
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->Ipaddress->searchFields = [
			'Vector.vector'
		];
		
		$this->paginate['contain'] = ['Vector'];
		$this->paginate['conditions'] = $this->Ipaddress->conditions($conditions, $this->passedArgs); 
		
		$ipaddresses = $this->paginate();
		
		$this->set(compact(['ipaddresses']));
	}
}
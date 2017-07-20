<?php
App::uses('AppController', 'Controller');

class HostnamesController extends AppController 
{
	
	public function search_results()
	{
		return $this->index();
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		
		if(!isset($this->paginateModel))
			$this->paginateModel = 'Hostname';
		
		$conditions = [
			'Vector.bad' => 0, 
			'Vector.type' => 'hostname'
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->Hostname->searchFields = [
			'Vector.vector'
		];
		
		$this->paginate['contain'] = ['Vector'];
		$this->paginate['conditions'] = $this->Hostname->conditions($conditions, $this->passedArgs); 
		
		$hostnames = $this->paginate();
		
		$this->set(compact(['hostnames']));
	}
}
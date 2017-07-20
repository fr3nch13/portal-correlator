<?php
App::uses('AppController', 'Controller');
/**
 * WhoisNameservers Controller
 *
 * @property WhoisNameservers $WhoisNameservers
 *
 */
class WhoisNameserversController extends AppController 
{
	public function whois($whois_id = false) 
	{
	/*
	 * Lists out the nameservers for a whois record
	 */
		$this->WhoisNameserver->Whois->id = $whois_id;
		if (!$this->WhoisNameserver->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisNameserver.whois_id' => $whois_id,
		);
		
		$this->WhoisNameserver->recursive = 0;
		$this->paginate['order'] = array('Nameserver.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisNameserver->conditions($conditions, $this->passedArgs);
		$this->set('whois_nameservers', $this->paginate());
	}
	
	public function nameserver($nameserver_id = false) 
	{
	/*
	 * Lists out the whois records for a nameserver
	 */
		$this->WhoisNameserver->Nameserver->id = $nameserver_id;
		if (!$this->WhoisNameserver->Nameserver->exists()) 
		{
			throw new NotFoundException(__('Invalid Nameserver'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisNameserver.nameserver_id' => $nameserver_id,
		);
		
		$this->WhoisNameserver->recursive = 1;
		$this->paginate['contain'] = array('Whois', 'Whois.Vector');
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisNameserver->conditions($conditions, $this->passedArgs);
		$this->set('whois_nameservers', $this->paginate());
	}

	public function admin_whois($whois_id = false) 
	{
	/*
	 * Lists out the nameservers for a whois record
	 */
		$this->WhoisNameserver->Whois->id = $whois_id;
		if (!$this->WhoisNameserver->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisNameserver.whois_id' => $whois_id,
		);
		
		$this->WhoisNameserver->recursive = 0;
		$this->paginate['order'] = array('Nameserver.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisNameserver->conditions($conditions, $this->passedArgs);
		$this->set('whois_nameservers', $this->paginate());
	}
	
	public function admin_nameserver($nameserver_id = false) 
	{
	/*
	 * Lists out the whois records for a nameserver
	 */
		$this->WhoisNameserver->Nameserver->id = $nameserver_id;
		if (!$this->WhoisNameserver->Nameserver->exists()) 
		{
			throw new NotFoundException(__('Invalid Nameserver'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisNameserver.nameserver_id' => $nameserver_id,
		);
		
		$this->WhoisNameserver->recursive = 1;
		$this->paginate['contain'] = array('Whois', 'Whois.Vector');
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisNameserver->conditions($conditions, $this->passedArgs);
		$this->set('whois_nameservers', $this->paginate());
	}
}

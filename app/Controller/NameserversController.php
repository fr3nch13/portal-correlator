<?php
App::uses('AppController', 'Controller');
/**
 * Nameservers Controller
 *
 * @property Nameservers $Nameservers
 *
 */
class NameserversController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Nameserver->recursive = 0;
		$this->paginate['order'] = array('Nameserver.id' => 'desc');
		$this->paginate['conditions'] = $this->Nameserver->conditions($conditions, $this->passedArgs);
		$this->set('nameservers', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Nameserver->id = $id;
		if (!$this->Nameserver->exists()) 
		{
			throw new NotFoundException(__('Invalid nameserver'));
		}
		
		// get the counts
		$this->Nameserver->getCounts = array(
			'WhoisNameserver' => array(
				'all' => array(
					'conditions' => array(
						'WhoisNameserver.nameserver_id' => $id
					),
				),
			),
		);
		
		$this->Nameserver->recursive = 0;
		$this->set('nameserver', $this->Nameserver->read(null, $id));
	}
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Nameserver->recursive = 0;
		$this->paginate['order'] = array('Nameserver.id' => 'desc');
		$this->paginate['conditions'] = $this->Nameserver->conditions($conditions, $this->passedArgs);
		$this->set('nameservers', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
		$this->Nameserver->id = $id;
		if (!$this->Nameserver->exists()) 
		{
			throw new NotFoundException(__('Invalid nameserver'));
		}
		
		// get the counts
		$this->Nameserver->getCounts = array(
			'WhoisNameserver' => array(
				'all' => array(
					'conditions' => array(
						'WhoisNameserver.nameserver_id' => $id
					),
				),
			),
		);
		
		$this->Nameserver->recursive = 0;
		$this->set('nameserver', $this->Nameserver->read(null, $id));
	}
}

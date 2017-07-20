<?php
App::uses('AppController', 'Controller');

class WhoisController extends AppController 
{
	public function db_block_overview()
	{
		$stats = $this->Whois->dashboardOverviewStats();
		$this->set(compact('stats'));
	}
	
	public function dashboard()
	{
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$this->Whois->recursive = 0;
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->Whois->conditions($conditions, $this->passedArgs);
		$this->set('whois', $this->paginate());
	}
	
	public function api_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$this->Whois->recursive = 0;
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->Whois->conditions($conditions, $this->passedArgs);
		$this->set('whois', $this->paginate());
	}
	
	public function vector($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Whois.vector_id' => $vector_id,
		);
		
		$this->Whois->recursive = 0;
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->Whois->conditions($conditions, $this->passedArgs);
		$this->set('whois', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Whois->id = $id;
		if (!$this->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid whois'));
		}
		
		// get the counts
		$this->Whois->getCounts = array(
			'WhoisNameserver' => array(
				'all' => array(
					'conditions' => array(
						'WhoisNameserver.whois_id' => $id
					),
				),
			),
			'WhoisLog' => array(
				'all' => array(
					'conditions' => array(
						'WhoisLog.whois_id' => $id
					),
				),
			),
		);
		
		$this->Whois->recursive = 0;
		$whois = $this->Whois->read(null, $id);
		$this->set('whois', $whois);
		
		$this->Whois->Vector->recursive = 0;
		$vector = $this->Whois->Vector->read(null, $whois['Whois']['vector_id']);
		$this->set('vector', $vector);
		$this->set('whoiser_compile_states', $this->Whois->Vector->WhoiserTransaction->compile_states);
	}

//
	public function edit($id = null) 
	{
		$this->Whois->id = $id;
		if (!$this->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Whois->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Whois Record has been saved'));
				return $this->redirect(array('action' => 'view', $this->Whois->id));
			}
			else
			{
				$this->Session->setFlash(__('The Whois Record could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->Whois->recursive = 0;
			$this->Whois->contain(array('Vector'));
			$this->request->data = $this->Whois->read(null, $id);
		}
	}
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Whois->recursive = 0;
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->Whois->conditions($conditions, $this->passedArgs);
		$this->set('whois', $this->paginate());
	}
	
	public function admin_vector($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Whois.vector_id' => $vector_id,
		);
		
		$this->Whois->recursive = 0;
		$this->paginate['order'] = array('Whois.id' => 'desc');
		$this->paginate['conditions'] = $this->Whois->conditions($conditions, $this->passedArgs);
		$this->set('whois', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
		$this->Whois->id = $id;
		if (!$this->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid whois'));
		}
		
		// get the counts
		$this->Whois->getCounts = array(
			'WhoisNameserver' => array(
				'all' => array(
					'conditions' => array(
						'WhoisNameserver.whois_id' => $id
					),
				),
			),
			'WhoisLog' => array(
				'all' => array(
					'conditions' => array(
						'WhoisLog.whois_id' => $id
					),
				),
			),
		);
		
		$this->Whois->recursive = 0;
		$whois = $this->Whois->read(null, $id);
		$this->set('whois', $whois);
		
		$this->Whois->Vector->recursive = 0;
		$vector = $this->Whois->Vector->read(null, $whois['Whois']['vector_id']);
		$this->set('vector', $vector);
		$this->set('whoiser_compile_states', $this->Whois->Vector->WhoiserTransaction->compile_states);
	}
}

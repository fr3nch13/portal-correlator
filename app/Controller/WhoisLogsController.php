<?php
App::uses('AppController', 'Controller');
/**
 * Whoiss Controller
 *
 * @property Whois $Whois
 */
class WhoisLogsController extends AppController 
{
	public function whois($whois_id = false)
	{
		$this->WhoisLog->Whois->id = $whois_id;
		if (!$this->WhoisLog->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisLog.whois_id' => $whois_id,
		);
		
		$this->WhoisLog->recursive = 0;
		$this->paginate['contain'] = array('Whois', 'Vector', 'Vector.Hostname', 'Vector.Ipaddress');
		$this->paginate['order'] = array('WhoisLog.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisLog->conditions($conditions, $this->passedArgs); 
		$this->set('whois_logs', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->WhoisLog->id = $id;
		if (!$this->WhoisLog->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois Log'));
		}
		
		$this->WhoisLog->recursive = 0;
		$whois_log = $this->WhoisLog->read(null, $id);
		$this->set('whois_log', $whois_log);
		
		$this->WhoisLog->Whois->Vector->recursive = 0;
		$vector = $this->WhoisLog->Whois->Vector->read(null, $whois_log['WhoisLog']['vector_id']);
		$this->set('vector', $vector);
	}
	
	public function admin_whois($whois_id = false)
	{
		$this->WhoisLog->Whois->id = $whois_id;
		if (!$this->WhoisLog->Whois->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WhoisLog.whois_id' => $whois_id,
		);
		
		$this->WhoisLog->recursive = 0;
		$this->paginate['contain'] = array('Whois', 'Vector', 'Vector.Hostname', 'Vector.Ipaddress');
		$this->paginate['order'] = array('WhoisLog.id' => 'desc');
		$this->paginate['conditions'] = $this->WhoisLog->conditions($conditions, $this->passedArgs); 
		$this->set('whois_logs', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
		$this->WhoisLog->id = $id;
		if (!$this->WhoisLog->exists()) 
		{
			throw new NotFoundException(__('Invalid Whois Log'));
		}
		
		$this->WhoisLog->recursive = 0;
		$whois_log = $this->WhoisLog->read(null, $id);
		$this->set('whois_log', $whois_log);
		
		$this->WhoisLog->Whois->Vector->recursive = 0;
		$vector = $this->WhoisLog->Whois->Vector->read(null, $whois_log['WhoisLog']['vector_id']);
		$this->set('vector', $vector);
	}
}
?>
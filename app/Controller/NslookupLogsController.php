<?php
App::uses('AppController', 'Controller');
/**
 * Nslookups Controller
 *
 * @property Nslookup $Nslookup
 */
class NslookupLogsController extends AppController 
{
	public function nslookup($nslookup_id = false)
	{
		$this->NslookupLog->Nslookup->id = $nslookup_id;
		if (!$this->NslookupLog->Nslookup->exists()) 
		{
			throw new NotFoundException(__('Invalid Nslookup'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'NslookupLog.nslookup_id' => $nslookup_id,
		);
		
		$this->NslookupLog->recursive = 0;
		$this->paginate['contain'] = array('Nslookup', 'NslookupHostname', 'NslookupIpaddress');
		$this->NslookupLog->searchFields[] = 'NslookupHostname.vector';
		$this->NslookupLog->searchFields[] = 'NslookupIpaddress.vector';
		
		$this->paginate['order'] = array('NslookupLog.id' => 'desc');
		$this->paginate['conditions'] = $this->NslookupLog->conditions($conditions, $this->passedArgs); 
		$this->set('nslookup_logs', $this->paginate());
	}
}
?>
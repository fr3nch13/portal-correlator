<?php
App::uses('AppController', 'Controller');
/**
 * ImportManagerLogs Controller
 *
 * @property ImportManagerLogs $ImportManagerLogs
 */
class ImportManagerLogsController extends AppController 
{
	public function admin_import_manager($import_manager_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ImportManagerLog.import_manager_id' => $import_manager_id,
		);
		
		// include just the user information
		$this->paginate['order'] = array('Import.id' => 'asc');
		$this->paginate['conditions'] = $this->ImportManagerLog->conditions($conditions, $this->passedArgs); 
		$this->set('import_manager_logs', $this->paginate());
	}
}
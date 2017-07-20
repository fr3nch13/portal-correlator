<?php
App::uses('AppModel', 'Model');
/**
 * ImportManagerLog Model
 *
 * @property ImportManager $ImportManager
 */
class ImportManagerLog extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ImportManager' => array(
			'className' => 'ImportManager',
			'foreignKey' => 'import_manager_id',
		)
	);
	
	public $tracking = array();
	
	public function add($import_manager_id = false)
	{
		$this->create();
		$this->data = array(
			'import_manager_id' => $import_manager_id,
			'starttime' => date('Y-m-d H:i:s'),
		);
		if($this->save($this->data))
		{
			
			return $this->id;
		}
		return false;
	}
	
	// define the fields that can be searched
	public $searchFields = array(
//		'Import.name',
	);
	
	public function update($id = false, $data = array('num_added' => 0, 'num_duplicate' => 0, 'num_empty' => 0, 'msg' => '', 'success' => 0))
	{
		$defaults = array('num_added' => 0, 'num_duplicate' => 0, 'num_failed' => 0, 'num_empty' => 0, 'msg' => '', 'success' => 0);
		$data = array_merge($defaults, $data);
//		$this->cronOut(__('Data: %s', json_encode($data)), 'imports');
		$this->id = $id;
		
		if(!isset($this->tracking[$id]))
		{
			$this->tracking[$id] = $defaults;
		}
//		$this->cronOut(__('Tracking[%s]: %s', $id, json_encode($this->tracking[$id])), 'imports');
		
		// track the stats
		foreach($data as $k => $v)
		{
			if(is_int($v))
			{
				$data[$k] = $this->tracking[$id][$k] + $v;
			}
			$this->tracking[$id][$k] = $data[$k];
		}
		
		$this->data = $this->tracking[$id];
		$this->data['endtime'] = date('Y-m-d H:i:s');
		
		$this->cronOut(__('Stats: %s', json_encode($this->data)), 'imports');
		
		return $this->save($this->data);
	}
	
	public function purge()
	{
	/*
	 * Purges the import logs from the database into a log file
	 */
		
		$logs = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'ImportManagerLog.created <' => date('Y-m-d H:i:s', strtotime('-1 month')),
			),
			'order' => array(
				'ImportManagerLog.created' => 'ASC',
			),
		));
		
		$this->shellOut(__('Found %s Import Manager Logs to purge', count($logs)), 'imports', 'info');
		
		// configure the cakelog for each import manager
		$configured = array();
		foreach($logs as $log)
		{
			$import_manager_id = $log['ImportManagerLog']['import_manager_id'];
			if(isset($configured[$import_manager_id]))
			{
				continue;
			}
			
			$cakelog_name = 'import_purge_'. $import_manager_id;
			$cakelog_filename = $cakelog_name. '.log';
			
			CakeLog::config($cakelog_name, array(
				'engine' => 'FileLog',
				'types' => array('info', 'error', 'warning', 'debug'),
				'scopes' => array($cakelog_name),
				'file' => $cakelog_filename,
			));
			
			$configured[$import_manager_id] = $import_manager_id;
			$this->shellOut(__('Created CakeLog named %s with the log file: %s', $cakelog_name, $cakelog_filename), 'imports', 'info');
		}
		
		$ids = array();
		foreach($logs as $log)
		{
			// track the id's later for deleting the log from the database
			$id = $log['ImportManagerLog']['id'];
			$ids[$id] = $id;
			
			// write this log to the log file
			$cakelog_name = 'import_purge_'. $log['ImportManagerLog']['import_manager_id'];
			$msg = json_encode($log['ImportManagerLog']);
			$this->shellOut($msg, $cakelog_name, 'info');
		}
		
		$this->deleteAll(array('ImportManagerLog.id' => $ids), false, false);
		
		$this->final_results = __('Purged %s logs.', count($logs));
		$this->shellOut($this->final_results, 'imports', 'info');
		
		return true;
	}
}

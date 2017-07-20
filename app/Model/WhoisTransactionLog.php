<?php
App::uses('AppModel', 'Model');
/**
 * WhoisTransactionLog Model
 *
 * @property Vector $Vector
 */
class WhoisTransactionLog extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		)
	);
	
	// when a whois lookup is requested, record it, and some stats/details as well
	// add errors later
	public function addLog($vector_id = false, $result_count = 0, $sources = '', $automatic = false)
	{
		$this->create();
		$this->data = array(
			'vector_id' => $vector_id,
			'result_count' => $result_count,
			'sources' => $sources,
			'automatic' => $automatic,
		);
		return $this->save($this->data);
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * DumpsDetail Model
 *
 * @property Dump $Dump
 */
class DumpsDetail extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Dump' => array(
			'className' => 'Dump',
			'foreignKey' => 'dump_id',
		)
	);
	
	public function beforeSave($options = array())
	{
		// from the Model Dump::afterSave
		if(!isset($this->data[$this->alias]['allvectors']))
			$this->data[$this->alias]['allvectors'] = '';
		if(!isset($this->data[$this->alias]['newvectors']))
			$this->data[$this->alias]['newvectors'] = '';
		$this->data[$this->alias]['allvectors'] .= "\n". Cache::read('DumpsDetail.allvectors', 'file');
		$this->data[$this->alias]['newvectors'] .= "\n". Cache::read('DumpsDetail.newvectors', 'file');
// don't save the contents of the file in the database
//		$this->data[$this->alias]['dumptext'] .= "\n". Cache::read('DumpsDetail.dumptext', 'file');

		Cache::delete('DumpsDetail.allvectors');
		Cache::delete('DumpsDetail.newvectors');
		Cache::delete('DumpsDetail.dumptext');
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		return parent::afterSave($created, $options);
	}
}

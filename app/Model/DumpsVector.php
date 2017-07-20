<?php
App::uses('AppModel', 'Model');
/**
 * DumpsVector Model
 *
 * @property Dump $Dump
 * @property Vector $Vector
 */
class DumpsVector extends AppModel {

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
		),
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
		'VectorType' => array(
			'className' => 'VectorType',
			'foreignKey' => 'vector_type_id',
		),
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public function listVectorIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		$contain = array('Vector');
		$conditions = array(
			'DumpsVector.dump_id' => $object_id,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Dump';
			
			$conditions['DumpsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('DumpsVector.vector_id', 'DumpsVector.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listVectorIds('.$object_id.')';
		}
		
		return $this->find('list', $options);
	}
}

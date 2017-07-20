<?php
App::uses('AppModel', 'Model');
/**
 * TempUploadsVector Model
 *
 * @property TempUpload $TempUpload
 * @property TempVector $TempVector
 */
class TempUploadsVector extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'temp_upload_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'temp_vector_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'TempUpload' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'temp_upload_id',
		),
		'TempVector' => array(
			'className' => 'TempVector',
			'foreignKey' => 'temp_vector_id',
		),
		'VectorType' => array(
			'className' => 'VectorType',
			'foreignKey' => 'vector_type_id',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'TempVector.temp_vector',
		'TempUpload.filename',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'type', 'multitype');
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public function saveAssociations($temp_upload_id = false, $temp_vector_ids = array(), $vector_xref_data = array())
	{
	/*
	 * Saves associations between a upload and temp_vectors
	 * 
	 */
			// remove the existing records (incase they add a temp_vector that is already associated with this upload)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('TempUploadsVector.id', 'TempUploadsVector.temp_vector_id'),
				'conditions' => array(
					'TempUploadsVector.temp_upload_id' => $temp_upload_id,
				),
			));
			
			// get just the new ones
			$temp_vector_ids = array_diff($temp_vector_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($temp_vector_ids as $temp_vector => $temp_vector_id)
			{
				$data[$temp_vector] = array('temp_upload_id' => $temp_upload_id, 'temp_vector_id' => $temp_vector_id, 'active' => 1);
				if(isset($vector_xref_data[$temp_vector]))
				{
					$data[$temp_vector] = array_merge($vector_xref_data[$temp_vector], $data[$temp_vector]);
				}
			}
			return $this->saveMany($data);
	}
	
	function add($data)
	{
	/*
	 * Save relations with a temp_upload
	 */
		if(isset($data[$this->alias]['temp_vectors']) and isset($data[$this->alias]['temp_upload_id']))
		{
			$_temp_vectors = $data[$this->alias]['temp_vectors'];
			
			if(is_string($data[$this->alias]['temp_vectors']))
			{
				$_temp_vectors = split("\n", trim($data[$this->alias]['temp_vectors']));
			}
			
			$vector_type_id = (isset($data[$this->alias]['vector_type_id'])?$data[$this->alias]['vector_type_id']:0);
			if(!$vector_type_id) $vector_type_id = 0;
			$dns_auto_lookup = (isset($data[$this->alias]['dns_auto_lookup'])?$data[$this->alias]['dns_auto_lookup']:0);
			if(!$dns_auto_lookup) $dns_auto_lookup = 0;
			$hexillion_auto_lookup = (isset($data[$this->alias]['hexillion_auto_lookup'])?$data[$this->alias]['hexillion_auto_lookup']:0);
			if(!$hexillion_auto_lookup) $hexillion_auto_lookup = 0;
			$vt_lookup = (isset($data[$this->alias]['vt_lookup'])?$data[$this->alias]['vt_lookup']:0);
			if(!$vt_lookup) $vt_lookup = 0;
			
			// clean them up and format them for a saveMany()
			$temp_vectors = array();
			$vector_xref_data = array();
			foreach($_temp_vectors as $i => $temp_vector)
			{
				$temp_vector = trim($temp_vector);
				if(!$temp_vector) continue;
				$temp_vector = $this->cleanString($temp_vector);
				$temp_vectors[$temp_vector] = array('temp_vector' => $temp_vector, 'vt_lookup' => $vt_lookup, 'dns_auto_lookup' => $dns_auto_lookup, 'hexillion_auto_lookup' => $hexillion_auto_lookup, 'vector_type_id' => $vector_type_id);
				$vector_xref_data[$temp_vector]['vector_type_id'] = $vector_type_id; 
			}
			
			// save only the new temp_vectors
			$this->TempVector->saveMany($temp_vectors, true);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['temp_upload_id'], $this->TempVector->saveManyIds, $vector_xref_data);
		}
		return true;
	}
	
	function assignVectorType($data)
	{
		if(!isset($data['TempUploadsVector']['temp_upload_id']))
		{
			return false;
		}
		if(!isset($data['TempUploadsVector']['vector_type_id']))
		{
			return false;
		}
		if(!$data['TempUploadsVector']['vector_type_id'])
		{
			$data['TempUploadsVector']['vector_type_id'] = 0;
		}
		
		$conditions = array(
			'TempUploadsVector.temp_upload_id' => $data['TempUploadsVector']['temp_upload_id'],
		);
		
		if(isset($data['TempUploadsVector']['only_unassigned']) and $data['TempUploadsVector']['only_unassigned'])
		{
			$conditions['TempUploadsVector.vector_type_id <'] = 1;
		}
		
		return $this->updateAll(
			array('TempUploadsVector.vector_type_id' => $data['TempUploadsVector']['vector_type_id']),
			$conditions
		);
	}
}

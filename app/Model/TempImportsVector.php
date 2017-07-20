<?php
App::uses('AppModel', 'Model');
/**
 * TempImportsVector Model
 *
 * @property TempImport $TempImport
 * @property TempVector $TempVector
 */
class TempImportsVector extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'import_id' => array(
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
		'Import' => array(
			'className' => 'Import',
			'foreignKey' => 'import_id',
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
		'Import.filename',
		'Import.name',
		'Import.sha1',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'type', 'multitype');
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public function saveAssociations($import_id = false, $temp_vector_ids = array(), $vector_xref_data = array())
	{
	/*
	 * Saves associations between a upload and temp_vectors
	 * 
	 */
			// remove the existing records (incase they add a temp_vector that is already associated with this upload)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('TempImportsVector.id', 'TempImportsVector.temp_vector_id'),
				'conditions' => array(
					'TempImportsVector.import_id' => $import_id,
				),
			));
			
			// get just the new ones
			$temp_vector_ids = array_diff($temp_vector_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($temp_vector_ids as $temp_vector => $temp_vector_id)
			{
				$data[$temp_vector] = array('import_id' => $import_id, 'temp_vector_id' => $temp_vector_id, 'active' => 1);
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
	 * Save relations with a import
	 */
	 /*
			$data = array(
				'TempImportsVector' => array(
					'temp_vectors' => $vectors,
					'import_id' => $this->id,
					'vector_settings' => $import_manager['ImportManager']['csv_fields'],
					'source' => 'import',
					'subsource' => $filename,
				),
			);
	 */
		if(isset($data[$this->alias]['temp_vectors']) and isset($data[$this->alias]['import_id']))
		{
			$_temp_vectors = $data[$this->alias]['temp_vectors'];
			
			if(is_string($data[$this->alias]['temp_vectors']))
			{
				$_temp_vectors = split("\n", trim($data[$this->alias]['temp_vectors']));
			}
			
			// clean them up and format them for a saveMany()
			$temp_vectors = array();
			$vector_xref_data = array();
			foreach($_temp_vectors as $i => $temp_vector)
			{
				$column = false;
				if(is_array($temp_vector))
				{
					$column = $temp_vector['column'];
					$temp_vector = $temp_vector['vector'];
				}
				
				$temp_vector = trim($temp_vector);
				if(!$temp_vector) continue;
				$temp_vector = $this->cleanString($temp_vector);
				
				$temp_vectors[$temp_vector] = array(
					'temp_vector' => $temp_vector, 
					'source' => $data[$this->alias]['source'], 
					'subsource' => $data[$this->alias]['subsource'],
					'subsource2' => $column,
					'dns_auto_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_dns'])?$data[$this->alias]['vector_settings'][$column]['setting_dns']:0),
					'vt_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_vt'])?$data[$this->alias]['vector_settings'][$column]['setting_vt']:0),
					'whois_auto_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_whois'])?$data[$this->alias]['vector_settings'][$column]['setting_whois']:0),
					'vector_type_id' => (isset($data[$this->alias]['vector_settings'][$column]['setting_vector_type'])?$data[$this->alias]['vector_settings'][$column]['setting_vector_type']:0),
				);
				
				$vector_xref_data[$temp_vector]['vector_type_id'] = $vector_type_id;
				
				if(isset($data[$this->alias]['vector_settings'][$column]['setting_vector_type']))
				{
					$vector_xref_data[$temp_vector]['vector_type_id'] = $data[$this->alias]['vector_settings'][$column]['setting_vector_type'];
				}
			}
			
			// save only the new temp_vectors
			$this->TempVector->saveMany($temp_vectors);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['import_id'], $this->TempVector->saveManyIds, $vector_xref_data);
		}
		return true;
	}
	
	function assignVectorType($data)
	{
		if(!isset($data['TempImportsVector']['import_id']))
		{
			return false;
		}
		if(!isset($data['TempImportsVector']['vector_type_id']))
		{
			return false;
		}
		if(!$data['TempImportsVector']['vector_type_id'])
		{
			$data['TempImportsVector']['vector_type_id'] = 0;
		}
		
		$conditions = array(
			'TempImportsVector.import_id' => $data['TempImportsVector']['import_id'],
		);
		
		if(isset($data['TempImportsVector']['only_unassigned']) and $data['TempImportsVector']['only_unassigned'])
		{
			$conditions['TempImportsVector.vector_type_id <'] = 1;
		}
		
		return $this->updateAll(
			array('TempImportsVector.vector_type_id' => $data['TempImportsVector']['vector_type_id']),
			$conditions
		);
	}
}

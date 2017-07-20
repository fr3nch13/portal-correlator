<?php
App::uses('AppModel', 'Model');
/**
 * ImportsVector Model
 *
 * @property Import $Import
 * @property Vector $Vector
 */
class ImportsVector extends AppModel {
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
		'vector_id' => array(
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
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
		'VectorType' => array(
			'className' => 'VectorType',
			'foreignKey' => 'vector_type_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		// a 'fake association' used to allow this to show up, and to allow sorting
		'Geoip' => array(
			'className' => 'Geoip',
			'joinTable' => 'vectors',
			'foreignKey' => 'vector_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Vector.vector',
		'Import.filename',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'active', 'inactive', 'type', 'multitype');
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public function delete($id = null, $cascade = true)
	{
		// remove the faux habtm with geoip so the record can be deleted
		$this->unbindModel(array('hasAndBelongsToMany' => array('Geoip')), false);
		return parent::delete($id, $cascade);
	}
	
	public function deleteAll($conditions, $cascade = true, $callbacks = false)
	{
		// remove the faux habtm with geoip so the record can be deleted
		$this->unbindModel(array('hasAndBelongsToMany' => array('Geoip')), false);
		return parent::deleteAll($conditions, $cascade, $callbacks);
	}
	
	public function saveAssociations($import_id = false, $vector_ids = array(), $vector_xref_data = array())
	{
	/*
	 * Saves associations between a import and vectors
	 * 
	 */
			// remove the existing records (incase they add a vector that is already associated with this import)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('ImportsVector.id', 'ImportsVector.vector_id'),
				'conditions' => array(
					'ImportsVector.import_id' => $import_id,
				),
			));
			
			// get just the new ones
			$vector_ids = array_diff($vector_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($vector_ids as $vector => $vector_id)
			{
				$data[$vector] = array('import_id' => $import_id, 'vector_id' => $vector_id, 'active' => 1);
				if(isset($vector_xref_data[$vector]))
				{
					$data[$vector] = array_merge($vector_xref_data[$vector], $data[$vector]);
				}
			}
			return $this->saveMany($data);
	}
	
	public function add($data)
	{
	/*
	 * Save relations with a import
	 */
		if(isset($data[$this->alias]['vectors']) and isset($data[$this->alias]['import_id']))
		{
			$_vectors = $data[$this->alias]['vectors'];
			
			if(is_string($data[$this->alias]['vectors']))
			{
				$_vectors = split("\n", trim($data[$this->alias]['vectors']));
			}
			
			// clean them up and format them for a saveMany()
			$vectors = array();
			$vector_xref_data = array();
			foreach($_vectors as $i => $vector)
			{
				$column = false;
				if(is_array($vector))
				{
					$column = $vector['column'];
					$vector = $vector['vector'];
				}
				
				$vector = trim($vector);
				if(!$vector) continue;
				$vector = $this->cleanString($vector);
				
				$vectors[$vector] = array(
					'vector' => $vector, 
					'source' => $data[$this->alias]['source'], 
					'subsource' => $data[$this->alias]['subsource'],
					'subsource2' => $column,
					'dns_auto_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_dns'])?$data[$this->alias]['vector_settings'][$column]['setting_dns']:0),
					'hexillion_auto_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_dns'])?$data[$this->alias]['vector_settings'][$column]['setting_dns']:0),
					'vt_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_vt'])?$data[$this->alias]['vector_settings'][$column]['setting_vt']:0),
					'whois_auto_lookup' => (isset($data[$this->alias]['vector_settings'][$column]['setting_whois'])?$data[$this->alias]['vector_settings'][$column]['setting_whois']:0),
					'vector_type_id' => (isset($data[$this->alias]['vector_settings'][$column]['setting_vector_type'])?$data[$this->alias]['vector_settings'][$column]['setting_vector_type']:0),
				);
				
				$vector_xref_data[$vector]['vector_type_id'] = $vector_type_id;
				
				if(isset($data[$this->alias]['vector_settings'][$column]['setting_vector_type']))
				{
					$vector_xref_data[$vector]['vector_type_id'] = $data[$this->alias]['vector_settings'][$column]['setting_vector_type'];
				}
			}
			
			// save only the new vectors
			$this->Vector->saveMany($vectors, true);
			
			// save the sources for them
			$this->Vector->VectorSource->addBatch($this->Vector->saveManyIds, $data[$this->alias]['source'], $data[$this->alias]['subsource'], false, $data[$this->alias]['import_id']);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['import_id'], $this->Vector->saveManyIds, $vector_xref_data);
		}
		return true;
	}
	
	public function assignVectorType($data)
	{
		if(!isset($data['ImportsVector']['import_id']))
		{
			return false;
		}
		if(!isset($data['ImportsVector']['vector_type_id']))
		{
			return false;
		}
		if(!$data['ImportsVector']['vector_type_id'])
		{
			$data['ImportsVector']['vector_type_id'] = 0;
		}
		
		$conditions = array(
			'ImportsVector.import_id' => $data['ImportsVector']['import_id'],
		);
		
		if(isset($data['ImportsVector']['only_unassigned']) and $data['ImportsVector']['only_unassigned'])
		{
			$conditions['ImportsVector.vector_type_id <'] = 1;
		}
		
		return $this->updateAll(
			array('ImportsVector.vector_type_id' => $data['ImportsVector']['vector_type_id']),
			$conditions
		);
	}
	
	public function assignDnsTracking($data)
	{
		if(!isset($data[$this->alias]['import_id']))
		{
			return false;
		}
		if(!isset($data[$this->alias]['dns_auto_lookup']))
		{
			return false;
		}
		
		// find the hostnames/ipaddresses
		$vectors = $this->find('list', array(
			'recursive' => 0,
			'conditions' => array(
				$this->alias.'.import_id' => $data[$this->alias]['import_id'],
				'Vector.type' => array('hostname', 'ipaddress'),
			),
			'fields' => array('Vector.id', 'Vector.type'),
		));
		
		$vector_hostname_ids = $vector_ipaddress_ids = array();
		foreach($vectors as $vector_id => $vector_type)
		{
			if($vector_type == 'hostname') $vector_hostname_ids[] = $vector_id;
			if($vector_type == 'ipaddress') $vector_ipaddress_ids[] = $vector_id;
		}
		
		$this->Vector->Hostname->updateAll(
			array('Hostname.dns_auto_lookup' => $data[$this->alias]['dns_auto_lookup']),
			array('Hostname.vector_id' => $vector_hostname_ids)
		);
		
		$this->Vector->Ipaddress->updateAll(
			array('Ipaddress.dns_auto_lookup' => $data[$this->alias]['dns_auto_lookup']),
			array('Ipaddress.vector_id' => $vector_ipaddress_ids)
		);
		return true;
	}
	
	public function assignHexillionTracking($data)
	{
		if(!isset($data[$this->alias]['import_id']))
		{
			return false;
		}
		if(!isset($data[$this->alias]['hexillion_auto_lookup']))
		{
			return false;
		}
		
		// find the hostnames/ipaddresses
		$vectors = $this->find('list', array(
			'recursive' => 0,
			'conditions' => array(
				$this->alias.'.import_id' => $data[$this->alias]['import_id'],
				'Vector.type' => array('hostname', 'ipaddress'),
			),
			'fields' => array('Vector.id', 'Vector.type'),
		));
		
		$vector_hostname_ids = $vector_ipaddress_ids = array();
		foreach($vectors as $vector_id => $vector_type)
		{
			if($vector_type == 'hostname') $vector_hostname_ids[] = $vector_id;
			if($vector_type == 'ipaddress') $vector_ipaddress_ids[] = $vector_id;
		}
		
		$this->Vector->Hostname->updateAll(
			array('Hostname.hexillion_auto_lookup' => $data[$this->alias]['hexillion_auto_lookup']),
			array('Hostname.vector_id' => $vector_hostname_ids)
		);
		
		$this->Vector->Ipaddress->updateAll(
			array('Ipaddress.hexillion_auto_lookup' => $data[$this->alias]['hexillion_auto_lookup']),
			array('Ipaddress.vector_id' => $vector_ipaddress_ids)
		);
		return true;
	}
	
	public function assignWhoisTracking($data)
	{
		if(!isset($data[$this->alias]['import_id']))
		{
			return false;
		}
		if(!isset($data[$this->alias]['whois_auto_lookup']))
		{
			return false;
		}
		
		// find the hostnames/ipaddresses
		$vectors = $this->find('list', array(
			'recursive' => 0,
			'conditions' => array(
				$this->alias.'import_id' => $data[$this->alias]['import_id'],
				'Vector.type' => array('hostname', 'ipaddress'),
			),
			'fields' => array('Vector.id', 'Vector.type'),
		));
		
		$vector_hostname_ids = $vector_ipaddress_ids = array();
		foreach($vectors as $vector_id => $vector_type)
		{
			if($vector_type == 'hostname') $vector_hostname_ids[] = $vector_id;
			if($vector_type == 'ipaddress') $vector_ipaddress_ids[] = $vector_id;
		}
		
		$this->Vector->Hostname->updateAll(
			array('Hostname.whois_auto_lookup' => $data[$this->alias]['whois_auto_lookup']),
			array('Hostname.vector_id' => $vector_hostname_ids)
		);
		
		$this->Vector->Ipaddress->updateAll(
			array('Ipaddress.whois_auto_lookup' => $data[$this->alias]['whois_auto_lookup']),
			array('Ipaddress.vector_id' => $vector_ipaddress_ids)
		);
		return true;
	}
	
	public function reviewed($import_id = false, $data = false)
	{
	/*
	 * Saves reviewed vectors for a reviewed import
	 */
		
		if(!$import_id) return false;
		if(!$data) return false;
		
		// build the list of vectors to be saved
		$vectors = array();
		$vector_xref_data = array();
		$vector_added_dates = array();
		foreach($data as $item)
		{
			$vector = $item['temp_vector'];
			$temp_id = $item['TempImportsVector']['id'];
			
			//track the importsVector data
			$vector_xref_data[$vector] = $item['TempImportsVector'];
			
			// track the vector added dates for the vector_sources table
			if(isset($vector_xref_data[$vector]['created']))
			{
				$vector_added_dates[$vector] = $vector_xref_data[$vector]['created'];
			}
			
			// track the vectors
			$vectors[$vector] = array_merge($item, array(
				'vector' => $vector,
				'reviewed' => date('Y-m-d H:i:s'),
			));
			
			// remove some of the items in the arrays
			unset(
				$vectors[$vector]['id'],
				$vectors[$vector]['temp_vector'],
				$vectors[$vector]['TempImportsVector'],
				$vector_xref_data[$vector]['id'],
				$vector_xref_data[$vector]['temp_vector_id']
			);
		}
		
		$vector_ids = $this->Vector->reviewed($vectors);
		if(!$vector_ids) return false;
		
		// save the sources for them
		$this->Vector->VectorSource->addBatch($vector_ids, 'manual', 'import', $vector_added_dates, $import_id);
		
		// save the associations of vectors to this import
		$return = $this->saveAssociations($import_id, $vector_ids, $vector_xref_data);
		
		$this->Import->TempImportsVector->deleteAll(array('TempImportsVector.import_id' => $import_id), false);
		
		return $return;
	}
	
	public function vectorsForHighlight($import_id = false)
	{
		return $this->find('list', array(
			'recursive' => 0,
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => array('ImportsVector.import_id' => $import_id, 'Vector.bad' => 0),
			'order' => array('LENGTH(Vector.vector) DESC'),
		));
	}
	
	public function listVectorIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.import_id' => $object_id,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Import';
			
			$conditions['ImportsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listVectorIds('.$object_id.')';
		}
		
		return $this->find('list', $options);
	}
	
	public function sqlImportsVectorRelatedOLD($import_id = false, $admin = false)
	{
	/*
	 * Import vectors related to another import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this import
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this
		);
		$subQuery = ' `ImportsVector`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * UploadsVector Model
 *
 * @property Upload $Upload
 * @property Vector $Vector
 */
class UploadsVector extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'upload_id' => array(
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
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'upload_id',
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
		'Upload.filename',
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
	
	public function saveAssociations($upload_id = false, $vector_ids = array(), $vector_xref_data = array())
	{
	/*
	 * Saves associations between a upload and vectors
	 * 
	 */
			// remove the existing records (incase they add a vector that is already associated with this upload)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('UploadsVector.id', 'UploadsVector.vector_id'),
				'conditions' => array(
					'UploadsVector.upload_id' => $upload_id,
				),
			));
			
			// get just the new ones
			$vector_ids = array_diff($vector_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($vector_ids as $vector => $vector_id)
			{
				$data[$vector] = array('upload_id' => $upload_id, 'vector_id' => $vector_id, 'active' => 1);
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
	 * Save relations with a upload
	 */
		if(isset($data[$this->alias]['vectors']) and isset($data[$this->alias]['upload_id']))
		{
			$_vectors = $data[$this->alias]['vectors'];
			
			if(is_string($data[$this->alias]['vectors']))
			{
				$_vectors = split("\n", trim($data[$this->alias]['vectors']));
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
			$vectors = array();
			$vector_xref_data = array();
			foreach($_vectors as $i => $vector)
			{
				$vector = trim($vector);
				if(!$vector) continue;
				$vector = $this->cleanString($vector);
				$vectors[$vector] = array('vector' => $vector, 'vt_lookup' => $vt_lookup, 'dns_auto_lookup' => $dns_auto_lookup, 'hexillion_auto_lookup' => $hexillion_auto_lookup, 'vector_type_id' => $vector_type_id);
				$vector_xref_data[$vector]['vector_type_id'] = $vector_type_id; 
			}
			
			// save only the new vectors
			$this->Vector->saveMany($vectors, true);
			
			// save the sources for them
			$this->Vector->VectorSource->addBatch($this->Vector->saveManyIds, 'manual', 'upload', false, $data[$this->alias]['upload_id']);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['upload_id'], $this->Vector->saveManyIds, $vector_xref_data);
		}
		return true;
	}
	
	public function assignVectorType($data)
	{
		if(!isset($data['UploadsVector']['upload_id']))
		{
			return false;
		}
		if(!isset($data['UploadsVector']['vector_type_id']))
		{
			return false;
		}
		if(!$data['UploadsVector']['vector_type_id'])
		{
			$data['UploadsVector']['vector_type_id'] = 0;
		}
		
		$conditions = array(
			'UploadsVector.upload_id' => $data['UploadsVector']['upload_id'],
		);
		
		if(isset($data['UploadsVector']['only_unassigned']) and $data['UploadsVector']['only_unassigned'])
		{
			$conditions['UploadsVector.vector_type_id <'] = 1;
		}
		
		return $this->updateAll(
			array('UploadsVector.vector_type_id' => $data['UploadsVector']['vector_type_id']),
			$conditions
		);
	}
	
	public function assignDnsTracking($data)
	{
		if(!isset($data[$this->alias]['upload_id']))
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
				$this->alias.'.upload_id' => $data[$this->alias]['upload_id'],
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
		if(!isset($data[$this->alias]['upload_id']))
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
				$this->alias.'.upload_id' => $data[$this->alias]['upload_id'],
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
		if(!isset($data[$this->alias]['upload_id']))
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
				$this->alias.'upload_id' => $data[$this->alias]['upload_id'],
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
	
	public function reviewed($upload_id = false, $data = false)
	{
	/*
	 * Saves reviewed vectors for a reviewed upload
	 */
		
		if(!$upload_id) return false;
		if(!$data) return false;
		
		// build the list of vectors to be saved
		$vectors = array();
		$vector_xref_data = array();
		$vector_added_dates = array();
		foreach($data as $item)
		{
			$vector = $item['temp_vector'];
			
			//track the reportsVector data
			$vector_xref_data[$vector] = $item['TempUploadsVector'];
			
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
				$vectors[$vector]['TempUploadsVector'],
				$vector_xref_data[$vector]['id'],
				$vector_xref_data[$vector]['temp_upload_id'],
				$vector_xref_data[$vector]['temp_vector_id']
			);
		}
		
		$vector_ids = $this->Vector->reviewed($vectors);
		if(!$vector_ids) return false;
		
		// save the sources for them
		$this->Vector->VectorSource->addBatch($vector_ids, 'manual', 'upload', $vector_added_dates, $upload_id);
		
		// save the associations of vectors to this upload
		$return = $this->saveAssociations($upload_id, $vector_ids, $vector_xref_data);
		
		return $return;
	}
	
	public function listVectorIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		$contain = array('Vector');
		$conditions = array(
			'UploadsVector.upload_id' => $object_id,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['UploadsVector.active'] = 1;
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('UploadsVector.vector_id', 'UploadsVector.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listVectorIds('.$object_id.')';
		}
		
		return $this->find('list', $options);
	}
	
	public function sqlUploadsVectorRelatedOLD($upload_id = false, $admin = false)
	{
	/*
	 * Upload vectors related to another upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this upload
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this
		);
		$subQuery = ' `UploadsVector`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
}

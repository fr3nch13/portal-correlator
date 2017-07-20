<?php
App::uses('AppModel', 'Model');
/**
 * CategoriesVector Model
 *
 * @property Category $Category
 * @property Vector $Vector
 */
class CategoriesVector extends AppModel 
{

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
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
	
	public $actsAs = [
		'Cacher.Cache' => [
			'config' => 'slowQueries',
			'clearOnDelete' => false,
			'clearOnSave' => false,
			'gzip' => false,
		],
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
			],
		],
	];
	
	// define the fields that can be searched
	public $searchFields = array(
		'Vector.vector',
		'Category.name',
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
	
	public function saveAssociations($category_id = false, $vector_ids = array(), $vector_xref_data = array())
	{
	/*
	 * Saves associations between a category and vectors
	 * 
	 */
			if(!$vector_ids) $vector_ids = array();
			
			// remove the existing records (incase they add a vector that is already associated with this category)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('CategoriesVector.id', 'CategoriesVector.vector_id'),
				'conditions' => array(
					'CategoriesVector.category_id' => $category_id,
				),
			));
			
			if(!$existing) $existing = array();
			
			// get just the new ones
			$vector_ids = array_diff($vector_ids, $existing);
			
			// build the proper save array
			$data = array();
			
			foreach($vector_ids as $vector => $vector_id)
			{
				$data[$vector] = array('category_id' => $category_id, 'vector_id' => $vector_id, 'active' => 1);
				if(isset($vector_xref_data[$vector]))
				{
					$data[$vector] = array_merge($vector_xref_data[$vector], $data[$vector]);
				}
			}
			
			if(!empty($data))
			{
				return $this->saveMany($data);
			}
			return true;
	}
	
	public function add($data)
	{
	/*
	 * Save relations with a category
	 */
		if(isset($data[$this->alias]['vectors']) and isset($data[$this->alias]['category_id']))
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
				$vector = $this->cleanString($vector);
				if(!$vector) continue;
				$vectors[$vector] = array('vector' => $vector, 'vt_lookup' => $vt_lookup, 'dns_auto_lookup' => $dns_auto_lookup, 'hexillion_auto_lookup' => $hexillion_auto_lookup, 'vector_type_id' => $vector_type_id);
				$vector_xref_data[$vector]['vector_type_id'] = $vector_type_id; 
			}
			
			// save only the new vectors
			$this->Vector->saveMany($vectors, true);
			
			// save the sources for them
			$this->Vector->VectorSource->addBatch($this->Vector->saveManyIds, 'manual', 'category', false, $data[$this->alias]['category_id']);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['category_id'], $this->Vector->saveManyIds, $vector_xref_data);
		}
		return true;
	}
	
	public function reviewed($category_id = false, $data = false)
	{
	/*
	 * Saves reviewed vectors for a reviewed category
	 */
		
		if(!$category_id) return false;
		if(!$data) return false;
		
		// build the list of vectors to be saved
		$vectors = array();
		$vector_xref_data = array();
		$vector_added_dates = array();
		foreach($data as $item)
		{
			$vector = $item['temp_vector'];
			
			//track the reportsVector data
			$vector_xref_data[$vector] = $item['TempCategoriesVector'];
			
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
				$vectors[$vector]['TempCategoriesVector'],
				$vector_xref_data[$vector]['id'],
				$vector_xref_data[$vector]['temp_category_id'],
				$vector_xref_data[$vector]['temp_vector_id']
			);
		}
		
		$vector_ids = $this->Vector->reviewed($vectors);
		if(!$vector_ids) return false;
		
		// save the sources for them
		$this->Vector->VectorSource->addBatch($vector_ids, 'manual', 'category', $vector_added_dates, $category_id);
		
		// save the associations of vectors to this category
		return $this->saveAssociations($category_id, $vector_ids, $vector_xref_data);
	}
	
	public function assignVectorType($data)
	{
		if(!isset($data['CategoriesVector']['category_id']))
		{
			return false;
		}
		if(!isset($data['CategoriesVector']['vector_type_id']))
		{
			return false;
		}
		if(!$data['CategoriesVector']['vector_type_id'])
		{
			$data['CategoriesVector']['vector_type_id'] = 0;
		}
		
		$conditions = array(
			'CategoriesVector.category_id' => $data['CategoriesVector']['category_id'],
		);
		
		if(isset($data['CategoriesVector']['only_unassigned']) and $data['CategoriesVector']['only_unassigned'])
		{
			$conditions['CategoriesVector.vector_type_id <'] = 1;
		}
		
		return $this->updateAll(
			array('CategoriesVector.vector_type_id' => $data['CategoriesVector']['vector_type_id']),
			$conditions
		);
	}
	
	public function assignDnsTracking($data)
	{
		if(!isset($data[$this->alias]['category_id']))
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
				$this->alias.'.category_id' => $data[$this->alias]['category_id'],
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
		if(!isset($data[$this->alias]['category_id']))
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
				$this->alias.'.category_id' => $data[$this->alias]['category_id'],
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
		if(!isset($data[$this->alias]['category_id']))
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
				$this->alias.'.category_id' => $data[$this->alias]['category_id'],
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
	
	public function listVectorIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		$contain = array('Vector');
		$conditions = array(
			'CategoriesVector.category_id' => $object_id,
			'Vector.bad' => 0,
		);
		
		if(!$admin and $org_group_id and $user_id)
		{
			$contain[] = 'Category';
			
			$conditions['CategoriesVector.active'] = 1;
			$conditions['OR'] = array(
				'Category.public' => 2,
				array(
					'Category.public' => 1,
					'Category.org_group_id' => $org_group_id,
				),
				array(
					'Category.public' => 0,
					'Category.user_id' => $user_id,
				),
			);
		}
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listVectorIds('.$object_id.')';
		}
		
		return $this->find('list', $options);
	}
	
	public function listVectorIds2($object_id = false)
	{
		$conditions = array(
			$this->alias.'.category_id' => $object_id,
		);
		
		$options = array(
			'conditions' => $conditions,
			'fields' => array($this->alias.'.vector_id', $this->alias.'.vector_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listVectorIds('.(is_array($object_id)?implode(',',$object_id):$object_id).')';
		}
		
		return $this->find('list', $options);
	}
	
	public function sqlCategoriesVectorRelated($category_id = false, $admin = false)
	{
	/*
	 * Category related to another category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this
		);
		$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function sqlCategoriesVector($category_id = false, $admin = false)
	{
	/*
	 * Category related to another category
	 * Builds the complex query for the conditions
	 */
		if(!$category_id) return false;
		
		// get the vector ids from this category
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery_conditions = array('CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this
		);
		//$subQuery = ' `CategoriesVector`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
/*** Methods for extracting stats for reporting ***/
	
	public function stats_vectorsByType()
	{
		// get the vector ids from this category
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery_conditions = array(
			'CategoriesVector1.active' => 1,
			'Category1.created > ' => '2013-01-01 00:00:00', 
			'Vector1.bad' => 0,
		);
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`CategoriesVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'joins'		 => array(
					array(
						'alias' => '`Category1`',
						'table' => 'categories',
						'type' => 'LEFT',
						'conditions' => '`Category1`.`id` = `CategoriesVector1`.`category_id`'
					),
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					),
				),
			),
			$this
		);
		$subQuery = ' `Vector`.`id` IN (' . $subQuery . ') ';
		
		$subQueryExpression = $db->expression($subQuery);
		$vectors = $this->Vector->find('list', array(
			'recursive' => -1,
			'fields' => array('Vector.id', 'Vector.type'),
			'conditions' => array(
				$subQueryExpression,
			),
 		));
		
		$types = array('all' => count($vectors));
		foreach($vectors as $vector_type)
		{
			if(!$vector_type) $vector_type = 'unassigned';
			if(!isset($types[$vector_type])) $types[$vector_type] = 0;
			$types[$vector_type] = ($types[$vector_type] + 1);
		}
		return $types;
	}
	
	public function snapshotDashboardGetStats($snapshotKeyRegex = false, $start = false, $end = false)
	{
		return $this->Snapshot_dashboardStats($snapshotKeyRegex, $start, $end);
	}
	
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return [];
	}
}

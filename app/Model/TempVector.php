<?php
App::uses('AppModel', 'Model');
/**
 * TempVector Model
 *
 */
class TempVector extends AppModel 
{

	public $displayField = 'temp_vector';
	
	public $validate = array(
		'temp_vector' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
	);
	
	public $hasAndBelongsToMany = array(
		'TempCategory' => array(
			'className' => 'TempCategory',
			'joinTable' => 'temp_categories_vectors',
			'foreignKey' => 'temp_category_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempCategoriesVector',
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'joinTable' => 'temp_reports_vectors',
			'foreignKey' => 'temp_report_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempReportsVector',
		),
		'TempUpload' => array(
			'className' => 'TempUpload',
			'joinTable' => 'temp_uploads_vectors',
			'foreignKey' => 'temp_upload_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempUploadsVector',
		)
	);
	
	public $belongsTo = array(
		'VectorType' => array(
			'className' => 'VectorType',
			'foreignKey' => 'vector_type_id',
		),
		'VectorTypeUser' => array(
			'className' => 'User',
			'foreignKey' => 'user_vtype_id',
		),
	);

	
	public $actsAs = array('Tags.Taggable');
	
	// define the fields that can be searched
	public $searchFields = array(
		'TempVector.temp_vector',
		'TempVector.type',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('bad');
	
	public $saveManyIds = array();
	
	
	public function afterFind($results = array(), $primary = false)
	{
		foreach ($results as $key => $val) 
		{
			if (array_key_exists('TempVector', $val) and array_key_exists('type', $val['TempVector']) and !$val['TempVector']['type'])
			{
				$results[$key]['TempVector']['type'] = $this->EX_discoverType($val['TempVector']['temp_vector']);
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function beforeSave($options = array())
	{
		if(isset($this->data['TempVector']['temp_vector']) and !isset($this->data['TempVector']['type']))
		{
			$this->data['TempVector']['type'] = $this->EX_discoverType($this->data['TempVector']['temp_vector']);
		}
		
		// mimic the bad state of the vector group it's in, ONLY if this vector is a new vector
		if(!isset($this->data[$this->alias]['id']) and isset($this->data[$this->alias]['vector_type_id']) and !isset($this->data[$this->alias]['bad']))
		{
			// get the vector type's benign state
			$this->data[$this->alias]['bad'] = (int)$this->VectorType->field('bad', array('id' => $this->data[$this->alias]['vector_type_id']));
		}
		
		return parent::beforeSave($options);
	}
	
	public function checkAdd($vector = false, $extra = array())
	{
		if(!$vector) return false;
		
		$vector = trim($vector);
		if(!$vector) return false;
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.temp_vector' => $vector)))
		{
			return $id;
		}
		
		// not an existing one, create it
		$this->create();
		$this->data = array_merge(array('temp_vector' => $vector), $extra);
		if($this->save($this->data))
		{
			return $this->id;
		}
		return false;
	}
	
	public function saveMany($data = array(), $from_xref = false)
	{
	/*
	 * Filter out the temp_vectors that already exist based on the temp_vector column
	 */
	 	$return = false;
	 	
	 	// reset the ids array
	 	$this->saveManyIds = array();
	 	
	 	if($data)
	 	{
	 		// find the existing temp_vectors
	 		$temp_vectors = array_keys($data);
	 		$existing = $this->find('all', array(
	 			'recursive' => -1,
				'conditions' => array('TempVector.temp_vector' => $temp_vectors),
			));
			
			// some do exist, filter them out
			if($existing)
			{	
				// update the existing ones from the current data set
				foreach($existing as $item)
				{
					$temp_vector = $item['TempVector']['temp_vector'];
					if(isset($data[$temp_vector]))
					{
						$data[$temp_vector]['id'] = $item['TempVector']['id'];
						// don't allow overwriting of the global vector type for existing ones, when added to an object like a report, etc
						if($from_xref)
						{
							if(isset($item['TempVector']['vector_type_id'])) unset($data[$temp_vector]['vector_type_id']);
						}
					}
					$this->saveManyIds[$temp_vector] = $item['TempVector']['id'];
				}
			}
			
			// add the new ones, and update the old ones
			if($data)
			{
				$return = parent::saveMany($data);
				
				// get the ids of the new records
				if($return)
				{
					$new = $this->find('list', array(
						'recursive' => -1,
						'fields' => array('TempVector.temp_vector', 'TempVector.id'),
						'conditions' => array('TempVector.temp_vector' => array_keys($data)),
					));
					if($new)
					{
						foreach($new as $temp_vector => $temp_vector_id)
						{
							$this->saveManyIds[$temp_vector] = $temp_vector_id;
						}
					}
				}
			}
	 	}
		return $return;
	}
	
	/** Correlations SQL Functions **/
//
	public function sqlTempCategoryToTempReportsRelated($temp_category_id = false, $admin = false)
	{
	/*
	 * TempReports related to a TempCategory
	 * Builds the complex query for the conditions
	 */
		if(!$temp_category_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->TempCategoriesVector->recursive = 0;
		$db = $this->TempCategoriesVector->getDataSource();
		
		$subQuery_conditions = array('TempCategoriesVector1.temp_category_id' => $temp_category_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['TempCategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`TempCategoriesVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->TempCategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`TempCategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `TempCategoriesVector1`.`temp_vector_id`'
					),
				),
			),
			$this->TempCategoriesVector
		);
		$subQuery = ' `TempReportsVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_report_ids from this model that share the same temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempReportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempReportsVector2`.`temp_report_id`'),
				'table'		 => $db->fullTableName($this->TempReportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempReportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempReportsVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempReportsVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempReport`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlTempReportToTempCategoriesRelated($temp_report_id = false, $admin = false)
	{
	/*
	 * TempCategories related to a TempReport
	 * Builds the complex query for the conditions
	 */
		if(!$temp_report_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->TempReportsVector->recursive = 0;
		$db = $this->TempReportsVector->getDataSource();
		
		$subQuery_conditions = array('TempReportsVector1.temp_report_id' => $temp_report_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['TempReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`TempReportsVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->TempReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`TempReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `TempReportsVector1`.`temp_vector_id`'
					),
				),
			),
			$this->TempReportsVector
		);
		$subQuery = ' `TempCategoriesVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_category_ids from this model that share the same temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempCategoriesVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempCategoriesVector2`.`temp_category_id`'),
				'table'		 => $db->fullTableName($this->TempCategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempCategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempCategoriesVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempCategoriesVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempCategory`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlTempUploadToTempReportsRelated($temp_upload_id = false, $admin = false)
	{
	/*
	 * TempReports related to an TempUpload
	 * Builds the complex query for the conditions
	 */
		if(!$temp_upload_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->TempUploadsVector->recursive = 0;
		$db = $this->TempUploadsVector->getDataSource();
		
		$subQuery_conditions = array('TempUploadsVector1.temp_upload_id' => $temp_upload_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['TempUploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`TempUploadsVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->TempUploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`TempUploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `TempUploadsVector1`.`temp_vector_id`'
					),
				),
			),
			$this->TempUploadsVector
		);
		$subQuery = ' `TempReportsVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_report_ids from this model that share the same temp_vectors
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempReportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempReportsVector2`.`temp_report_id`'),
				'table'		 => $db->fullTableName($this->TempReportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempReportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempReportsVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempReportsVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempReport`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlTempUploadToTempCategoriesRelated($temp_upload_id = false, $admin = false)
	{
	/*
	 * TempCategories related to an TempUpload
	 * Builds the complex query for the conditions
	 */
		if(!$temp_upload_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->TempUploadsVector->recursive = 0;
		$db = $this->TempUploadsVector->getDataSource();
		
		$subQuery_conditions = array('TempUploadsVector1.temp_upload_id' => $temp_upload_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['TempUploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`TempUploadsVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->TempUploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`TempUploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `TempUploadsVector1`.`temp_vector_id`'
					),
				),
			),
			$this->TempUploadsVector
		);
		$subQuery = ' `TempCategoriesVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_category_ids from this model that share the came temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempCategoriesVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempCategoriesVector2`.`temp_category_id`'),
				'table'		 => $db->fullTableName($this->TempCategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempCategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempCategoriesVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempCategoriesVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempCategory`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlDumpToTempReportsRelated($dump_id = false, $admin = false)
	{
	/*
	 * TempReports related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->DumpsTempVector->recursive = 0;
		$db = $this->DumpsTempVector->getDataSource();
		
		$subQuery_conditions = array('DumpsTempVector1.dump_id' => $dump_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsTempVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsTempVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsTempVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsTempVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `DumpsTempVector1`.`temp_vector_id`'
					),
				),
			),
			$this->DumpsTempVector
		);
		$subQuery = ' `TempReportsVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_report_ids from this model that share the same temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempReportsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempReportsVector2`.`temp_report_id`'),
				'table'		 => $db->fullTableName($this->TempReportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempReportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempReportsVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempReportsVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempReport`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlDumpToTempCategoriesRelated($dump_id = false, $admin = false)
	{
	/*
	 * TempCategories related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the temp_vector ids from this temp_category
		$this->DumpsTempVector->recursive = 0;
		$db = $this->DumpsTempVector->getDataSource();
		
		$subQuery_conditions = array('DumpsTempVector1.dump_id' => $dump_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsTempVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsTempVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsTempVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsTempVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `DumpsTempVector1`.`temp_vector_id`'
					),
				),
			),
			$this->DumpsTempVector
		);
		$subQuery = ' `TempCategoriesVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_category_ids from this model that share the came temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempCategoriesVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempCategoriesVector2`.`temp_category_id`'),
				'table'		 => $db->fullTableName($this->TempCategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempCategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempCategoriesVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempCategoriesVector
		);
		// get the temp_categories themselves
		
		$subQuery2 = ' `TempCategory`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
//
	public function sqlDumpToTempUploadsRelated($dump_id = false, $admin = false)
	{
	/*
	 * TempUploads related to an Dump
	 * Builds the complex query for the conditions
	 */
		if(!$dump_id) return false;
		
		// get the temp_vector ids from this temp_upload
		$this->DumpsTempVector->recursive = 0;
		$db = $this->DumpsTempVector->getDataSource();
		
		$subQuery_conditions = array('DumpsTempVector1.dump_id' => $dump_id, 'TempVector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['DumpsTempVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`DumpsTempVector1`.`temp_vector_id`'),
				'table'		 => $db->fullTableName($this->DumpsTempVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`DumpsTempVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector1`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector1`.`id` = `DumpsTempVector1`.`temp_vector_id`'
					),
				),
			),
			$this->DumpsTempVector
		);
		$subQuery = ' `TempUploadsVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		// get the temp_upload_ids from this model that share the came temp_vectors.
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempUploadsVector2.active'] = 1;
		}
		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`TempUploadsVector2`.`temp_upload_id`'),
				'table'		 => $db->fullTableName($this->TempUploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`TempUploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`TempVector2`',
						'table' => 'temp_vectors',
						'type' => 'LEFT',
						'conditions' => '`TempVector2`.`id` = `TempUploadsVector2`.`temp_vector_id`'
					),
				),
			),
			$this->TempUploadsVector
		);
		// get the temp_uploads themselves
		
		$subQuery2 = ' `TempUpload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	/** Comparison Functions **/
	
//
	public function compareTempCategoryTempReport($temp_category_id = false, $temp_report_id = false, $admin = false)
	{
	/*
	 * Compare a temp_category and a temp_report
	 */
		$data = array(
			'temp_category' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'temp_report' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		$temp_vectors_temp_category_conditions = array(
			'TempCategoriesVector.temp_category_id' => $temp_category_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_category_conditions['TempCategoriesVector.active'] = 1;
		}
		
		$temp_vectors_temp_category = $this->TempCategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_category_conditions,
		));
		asort($temp_vectors_temp_category);
		
		$temp_vectors_temp_report_conditions = array(
			'TempReportsVector.temp_report_id' => $temp_report_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_category_conditions['TempReportsVector.active'] = 1;
		}
		
		$temp_vectors_temp_report = $this->TempReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_report_conditions,
		));		
		asort($temp_vectors_temp_report);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_category_unique = array_diff_assoc($temp_vectors_temp_category, $temp_vectors_temp_report);
		$temp_vectors_temp_report_unique = array_diff_assoc($temp_vectors_temp_report, $temp_vectors_temp_category);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_category, $temp_vectors_temp_report);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_category, $temp_vectors_temp_report));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_category = "\n". implode("\n",$temp_vectors_temp_category);

		$string_temp_report = "\n". implode("\n",$temp_vectors_temp_report);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_category, $string_temp_report);
		
		$data = array(
			'temp_category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_category_unique,
			),
			'temp_report' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_report_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
	
//
	public function compareTempCategoryTempUpload($temp_category_id = false, $temp_upload_id = false, $admin = false)
	{
	/*
	 * Compare a temp_category and an temp_upload
	 */
		$data = array(
			'temp_category' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'temp_upload' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		$temp_vectors_temp_category_conditions = array(
			'TempCategoriesVector.temp_category_id' => $temp_category_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_category_conditions['TempCategoriesVector.active'] = 1;
		}
		
		$temp_vectors_temp_category = $this->TempCategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_category_conditions,
		));
		asort($temp_vectors_temp_category);
		
		$temp_vectors_temp_upload_conditions = array(
			'TempUploadsVector.temp_upload_id' => $temp_upload_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_upload['TempUploadsVector.active'] = 1;
		}
		
		$temp_vectors_temp_upload = $this->TempUploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_upload_conditions,
		));		
		asort($temp_vectors_temp_upload);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_category_unique = array_diff_assoc($temp_vectors_temp_category, $temp_vectors_temp_upload);
		$temp_vectors_temp_upload_unique = array_diff_assoc($temp_vectors_temp_upload, $temp_vectors_temp_category);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_category, $temp_vectors_temp_upload);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_category, $temp_vectors_temp_upload));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_category = "\n". implode("\n",$temp_vectors_temp_category);

		$string_temp_upload = "\n". implode("\n",$temp_vectors_temp_upload);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_category, $string_temp_upload);
		
		$data = array(
			'temp_category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_category_unique,
			),
			'temp_upload' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_upload_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
	
//
	public function compareTempReportTempUpload($temp_report_id = false, $temp_upload_id = false, $admin = false)
	{
	/*
	 * Compare a temp_report and an temp_upload
	 */
		$data = array(
			'temp_report' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'temp_upload' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		
		$temp_vectors_temp_report_conditions = array(
			'TempReportsVector.temp_report_id' => $temp_report_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_report_conditions['TempReportsVector.active'] = 1;
		}
		
		$temp_vectors_temp_report = $this->TempReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_report_conditions,
		));
		asort($temp_vectors_temp_report);
		
		$temp_vectors_temp_upload_conditions = array(
			'TempUploadsVector.temp_upload_id' => $temp_upload_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_upload_conditions['TempUploadsVector.active'] = 1;
		}
		
		$temp_vectors_temp_upload = $this->TempUploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_upload_conditions,
		));		
		asort($temp_vectors_temp_upload);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_report_unique = array_diff_assoc($temp_vectors_temp_report, $temp_vectors_temp_upload);
		$temp_vectors_temp_upload_unique = array_diff_assoc($temp_vectors_temp_upload, $temp_vectors_temp_report);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_report, $temp_vectors_temp_upload);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_report, $temp_vectors_temp_upload));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_report = "\n". implode("\n",$temp_vectors_temp_report);

		$string_temp_upload = "\n". implode("\n",$temp_vectors_temp_upload);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_report, $string_temp_upload);
		
		$data = array(
			'temp_report' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_report_unique,
			),
			'temp_upload' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_upload_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
	
//
	public function compareTempCategoryDump($temp_category_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare a temp_category and an dump
	 */
		$data = array(
			'temp_category' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		
		$temp_vectors_temp_category_conditions = array(
			'TempCategoriesVector.temp_category_id' => $temp_category_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_category_conditions['TempCategoriesVector.active'] = 1;
		}
		
		$temp_vectors_temp_category = $this->TempCategoriesVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_category_conditions,
		));
		asort($temp_vectors_temp_category);
		
		$temp_vectors_dump_conditions = array(
			'DumpsTempVector.dump_id' => $dump_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_dump_conditions['DumpsTempVector.active'] = 1;
		}
		
		$temp_vectors_dump = $this->DumpsTempVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_dump_conditions,
		));		
		asort($temp_vectors_dump);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_category_unique = array_diff_assoc($temp_vectors_temp_category, $temp_vectors_dump);
		$temp_vectors_dump_unique = array_diff_assoc($temp_vectors_dump, $temp_vectors_temp_category);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_category, $temp_vectors_dump);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_category, $temp_vectors_dump));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_category = "\n". implode("\n",$temp_vectors_temp_category);

		$string_dump = "\n". implode("\n",$temp_vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_category, $string_dump);
		
		$data = array(
			'temp_category' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_category_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
	
//
	public function compareTempReportDump($temp_report_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare a temp_report and an dump
	 */
		$data = array(
			'temp_report' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		
		$temp_vectors_temp_report_conditions = array(
			'TempReportsVector.temp_report_id' => $temp_report_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_report_conditions['TempReportsVector.active'] = 1;
		}
		
		$temp_vectors_temp_report = $this->TempReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_report_conditions,
		));
		asort($temp_vectors_temp_report);
		
		$temp_vectors_dump_conditions = array(
			'DumpsTempVector.dump_id' => $dump_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_dump_conditions['DumpsTempVector.active'] = 1;
		}
		
		$temp_vectors_dump = $this->DumpsTempVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_dump_conditions,
		));		
		asort($temp_vectors_dump);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_report_unique = array_diff_assoc($temp_vectors_temp_report, $temp_vectors_dump);
		$temp_vectors_dump_unique = array_diff_assoc($temp_vectors_dump, $temp_vectors_temp_report);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_report, $temp_vectors_dump);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_report, $temp_vectors_dump));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_report = "\n". implode("\n",$temp_vectors_temp_report);

		$string_dump = "\n". implode("\n",$temp_vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_report, $string_dump);
		
		$data = array(
			'temp_report' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_report_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
	
//
	public function compareTempUploadDump($temp_upload_id = false, $dump_id = false, $admin = false)
	{
	/*
	 * Compare an temp_upload and a dump
	 */
		$data = array(
			'temp_upload' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'dump' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_categories
		
		$temp_vectors_temp_upload_conditions = array(
			'TempUploadsVector.temp_upload_id' => $temp_upload_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_temp_upload_conditions['TempUploadsVector.active'] = 1;
		}
		
		$temp_vectors_temp_upload = $this->TempUploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_temp_upload_conditions,
		));
		asort($temp_vectors_temp_upload);
		
		$temp_vectors_dump_conditions = array(
			'DumpsTempVector.dump_id' => $dump_id,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_dump_conditions['DumpsTempVector.active'] = 1;
		}
		
		$temp_vectors_dump = $this->DumpsTempVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.temp_vector', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_dump_conditions,
		));		
		asort($temp_vectors_dump);
		
		// find the unique temp_vector_ids
		$temp_vectors_temp_upload_unique = array_diff_assoc($temp_vectors_temp_upload, $temp_vectors_dump);
		$temp_vectors_dump_unique = array_diff_assoc($temp_vectors_dump, $temp_vectors_temp_upload);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_temp_upload, $temp_vectors_dump);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_temp_upload, $temp_vectors_dump));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_temp_upload = "\n". implode("\n",$temp_vectors_temp_upload);

		$string_dump = "\n". implode("\n",$temp_vectors_dump);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_temp_upload, $string_dump);
		
		$data = array(
			'temp_upload' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_temp_upload_unique,
			),
			'dump' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_dump_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
}

<?php
App::uses('Model', 'Model');

App::uses('AuthComponent', 'Controller/Component');

class AppModel extends Model 
{
	public $actsAs = array(
		'Containable', 
		'Utilities.Common', 
		'Utilities.Extractor', 
		'Utilities.Foapi', 
		'Utilities.Rules', 
		'Utilities.Shell', 
		'Search.Searchable', 
		'Ssdeep.Ssdeep',
		'OAuthClient.OAuthClient' => array(
			'redirectUrl' => array('plugin' => false, 'controller' => 'users', 'action' => 'login', 'admin' => false)
		),
		'Cacher.Cache' => array(
			'config' => 'slowQueries',
			'clearOnDelete' => false,
			'clearOnSave' => false,
			'gzip' => false,
		),
		
		// used for avatar management
		'Upload.Upload' => array(
			'photo' => array(
				'deleteOnUpdate' => true,
				'thumbnailSizes' => array(
					'big' => '200x200',
					'medium' => '120x120',
					'thumb' => '80x80',
					'small' => '40x40',
					'tiny' => '16x16',
				),
			),
		),
    );
    
    public $exists = array();
	
	public $hex_balance = false; // track the balance for hexillion
	
	public function stats()
	{
	/*
	 * Default placeholder if no stats function is available for a Model
	 */
		return array();
	}
	
/////////////////////
	
	public function readGlobalObject()
	{
		$db =& $this->getDataSource();
		$fields = $db->describe($this->useTable);
		$out = array();
		foreach($fields as $name => $attr)
		{
			$value = false;
			if($attr['type'] == 'integer') $value = 0;
			if($attr['type'] == 'datetime') $value = 0;
			if($attr['type'] == 'string')
			{
				if($name == 'name') $value = __('Global');
			}
			
			$out[$this->alias][$name] = $value;
		}
		return $out;
	}
	
/////////////////////
//
	public function sqlSignatureIds($subQuery_conditions = array())
	{
	/*
	 * Signature Ids related to a Object
	 * Mainly for CategioriesSignatures and ReportsSignatures
	 * Builds the complex query for the conditions
	 */
		
		// get the signature ids from this category
		$this->recursive = 0;
		$db = $this->getDataSource();
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`'.$this->alias.'`.`signature_id`'),
				'table'		 => $db->fullTableName($this),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`'.$this->alias.'`',
				'joins'		 => array(
					array(
						'alias' => '`Signature`',
						'table' => 'signatures',
						'type' => 'LEFT',
						'conditions' => '`Signature`.`id` = `'.$this->alias.'`.`signature_id`'
					),
				),
			),
			$this
		);
		$subQuery = ' `Signature`.`id` IN (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		return $subQueryExpression;
	}
	
	public function guessNames($modelName = false)
	{
		$model_name = Inflector::underscore($modelName);
		$model_names = explode('_', $model_name);
		
		foreach($model_names as $i => $name)
		{
			$name = Inflector::camelize(Inflector::singularize($name));
			$model_names[$i] = $name;
		}
		
		$controller = strtolower(Inflector::pluralize($model_names[0]));
		$idKey = Inflector::singularize($controller). '_id';
		
		return array(
			'thisName' => $modelName,
			'parent1Name' => $model_names[0],
			'parent2Name' => $model_names[1],
			'controller' => $controller,
			'idKey' => $idKey,
		);
	}
	
	public function assignVtTracking($data)
	{
		if(!isset($data[$this->alias]['id']))
		{
			$this->modelError = __('Unknown id.');
			return false;
		}
		
		if(!isset($data[$this->alias]['vt_lookup']))
		{
			$this->modelError = __('Unknown %s setting.', __('VirusTotal'));
			return false;
		}
		
		$vt_lookup = $data[$this->alias]['vt_lookup'];
		
		$names = $this->guessNames($this->alias);
		extract($names);
		
		// find the hostnames/ipaddresses/hashes
		$vectors = $this->find('list', array(
			'recursive' => 0,
			'conditions' => array(
				'Vector.bad' => 0,
				'Vector.type' => array_merge(array_keys($this->EX_listTypes('hash')), array('hostname', 'ipaddress')),
				$thisName.'.'.$idKey => $data[$thisName]['id'],
			),
			'fields' => array('Vector.id', 'Vector.type'),
		));
		
		if(!$vectors)
		{
			$this->modelError = __('None of the selected %s match the criteria.', Inflector::pluralize($parent2Name));
			return false;
		}
		
		// make sure this vector has a vector details entry
		$cnt=0;
		foreach($vectors as $vector_id => $vector_type)
		{
			if($this->{$parent2Name}->VectorDetail->checkAddUpdate($vector_id, array(
				'vt_lookup' => $vt_lookup,
			))) { $cnt++; }
		}
		
		$this->modelResults = $cnt;
		return true;
	}
	
	/** Correlations SQL Functions **/
	
	public function sqlVirusTotalAllIds($vector_id = false, $alias = false, $aliasKey = false)
	{
	/*
	 * Builds the sql query that will return all vector ids
	 * that are related to this vector through the vt tables
	 */
		if(!$vector_id) return false;
		
		$vectorAlias = false;
		$foreignKey = false;
		
		if($this->alias != 'Vector')
		{
			$vectorAlias = false;
			$foreignKey = false;
			if(isset($this->belongsTo))
			{
				foreach($this->belongsTo as $modelAlias => $modelAliasSettings)
				{
					if(isset($modelAliasSettings['className']) and $modelAliasSettings['className'] == 'Vector')
					{
						$vectorAlias = $modelAlias;
						$foreignKey = $modelAliasSettings['foreignKey'];
						break;
					}
				}
			}
			
			$this->VtDetectedUrlLookup = $this->Vector->VtDetectedUrlLookup;
			$this->VtDetectedUrlUrl = $this->Vector->VtDetectedUrlUrl;
			$this->VtRelatedSampleLookup = $this->Vector->VtRelatedSampleLookup;
			$this->VtRelatedSampleSample = $this->Vector->VtRelatedSampleSample;
			$this->VtNtRecordLookup = $this->Vector->VtNtRecordLookup;
			$this->VtNtRecordLookup = $this->Vector->VtNtRecordLookup;
			$this->VtNtRecordSrc = $this->Vector->VtNtRecordSrc;
			$this->VtNtRecordDst = $this->Vector->VtNtRecordDst;
			
			if(!$aliasKey) $aliasKey = $foreignKey;
		}
		
		if(!$alias) $alias = $this->alias;
		if(!$aliasKey) $aliasKey = $this->primaryKey;
		
		$db = $this->getDataSource();
		
		$subQuery_conditions = array('VtDetectedUrlLookup1.vector_lookup_id' => $vector_id);
		$subQuery_VtDetectedUrlLookup1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtDetectedUrlLookup1`.`vector_url_id`'),
				'table'		 => $db->fullTableName($this->VtDetectedUrlLookup),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtDetectedUrlLookup1`',
			),
			$this->VtDetectedUrlLookup
		);
		
		$subQuery_conditions = array('VtDetectedUrlUrl1.vector_url_id' => $vector_id);
		$subQuery_VtDetectedUrlUrl1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtDetectedUrlUrl1`.`vector_lookup_id`'),
				'table'		 => $db->fullTableName($this->VtDetectedUrlUrl),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtDetectedUrlUrl1`',
			),
			$this->VtDetectedUrlUrl
		);
		
		$subQuery_conditions = array('VtRelatedSampleLookup1.vector_lookup_id' => $vector_id);
		$subQuery_VtRelatedSampleLookup1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtRelatedSampleLookup1`.`vector_sample_id`'),
				'table'		 => $db->fullTableName($this->VtRelatedSampleLookup),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtRelatedSampleLookup1`',
			),
			$this->VtRelatedSampleLookup
		);
		
		$subQuery_conditions = array('VtRelatedSampleSample1.vector_sample_id' => $vector_id);
		$subQuery_VtRelatedSampleSample1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtRelatedSampleSample1`.`vector_lookup_id`'),
				'table'		 => $db->fullTableName($this->VtRelatedSampleSample),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtRelatedSampleSample1`',
			),
			$this->VtRelatedSampleSample
		);

		$subQuery_conditions = array('VtNtRecordLookup1.vector_lookup_id' => $vector_id);
		$subQuery_VtNtRecordLookup1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordLookup1`.`vector_src_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordLookup),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordLookup1`',
			),
			$this->VtNtRecordLookup
		);
		
		$subQuery_conditions = array('VtNtRecordLookup2.vector_lookup_id' => $vector_id);
		$subQuery_VtNtRecordLookup2 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordLookup2`.`vector_dst_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordLookup),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordLookup2`',
			),
			$this->VtNtRecordLookup
		);
		
		$subQuery_conditions = array('VtNtRecordSrc1.vector_src_id' => $vector_id);
		$subQuery_VtNtRecordSrc1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordSrc1`.`vector_lookup_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordSrc),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordSrc1`',
			),
			$this->VtNtRecordSrc
		);
		
		$subQuery_conditions = array('VtNtRecordSrc2.vector_src_id' => $vector_id);
		$subQuery_VtNtRecordSrc2 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordSrc2`.`vector_dst_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordSrc),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordSrc2`',
			),
			$this->VtNtRecordSrc
		);
		
		$subQuery_conditions = array('VtNtRecordDst1.vector_dst_id' => $vector_id);
		$subQuery_VtNtRecordDst1 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordDst1`.`vector_lookup_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordDst),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordDst1`',
			),
			$this->VtNtRecordDst
		);
		
		$subQuery_conditions = array('VtNtRecordDst2.vector_dst_id' => $vector_id);
		$subQuery_VtNtRecordDst2 = $db->buildStatement(
			array(
				'fields'	 => array('`VtNtRecordDst2`.`vector_src_id`'),
				'table'		 => $db->fullTableName($this->VtNtRecordDst),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`VtNtRecordDst2`',
			),
			$this->VtNtRecordDst
		);
		
		$subQuery_VtDetectedUrlLookup1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtDetectedUrlLookup1 . ') ';
		$subQuery_VtDetectedUrlUrl1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtDetectedUrlUrl1 . ') ';
		$subQuery_VtRelatedSampleLookup1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtRelatedSampleLookup1 . ') ';
		$subQuery_VtRelatedSampleSample1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtRelatedSampleSample1 . ') ';
		$subQuery_VtNtRecordLookup1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordLookup1 . ') ';
		$subQuery_VtNtRecordLookup2 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordLookup2 . ') ';
		$subQuery_VtNtRecordSrc1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordSrc1 . ') ';
		$subQuery_VtNtRecordSrc2 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordSrc2 . ') ';
		$subQuery_VtNtRecordDst1 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordDst1 . ') ';
		$subQuery_VtNtRecordDst2 = ' `'.$alias.'`.`'.$aliasKey.'` IN (' . $subQuery_VtNtRecordDst2 . ') ';
		
		$subQueryExpression = array(
				$db->expression($subQuery_VtDetectedUrlLookup1),
				$db->expression($subQuery_VtDetectedUrlUrl1),
				$db->expression($subQuery_VtRelatedSampleLookup1),
				$db->expression($subQuery_VtRelatedSampleSample1),
				$db->expression($subQuery_VtNtRecordLookup1),
				$db->expression($subQuery_VtNtRecordLookup2),
				$db->expression($subQuery_VtNtRecordSrc1),
				$db->expression($subQuery_VtNtRecordSrc2),
				$db->expression($subQuery_VtNtRecordDst1),
				$db->expression($subQuery_VtNtRecordDst2),
		);
		
		return $subQueryExpression;
	}
	
	public function exists($id = null)
	{
		if($id === null) {
			$id = $this->getID();
		}
		if(isset($this->exists[$id]))
		{
			return $this->exists[$id];
		}
		
		$results = parent::exists();
		$this->exists[$id] = $results;
		return $results;
	}
}

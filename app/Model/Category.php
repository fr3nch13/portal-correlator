<?php
App::uses('AppVectorParent', 'Model');

class Category extends AppVectorParent 
{
	public $useTable = 'categories';

	public $displayField = 'name';
	
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
		'user_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'public' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
	];
	
	public $hasOne = [
		'CategoriesDetail' => [
			'className' => 'CategoriesDetail',
			'foreignKey' => 'category_id',
			'dependent' => true,
		]
	];
	
	public $hasMany = [
		'Upload' => [
			'className' => 'Upload',
			'foreignKey' => 'category_id',
			'dependent' => true,
		],
		'TempUpload' => [
			'className' => 'TempUpload',
			'foreignKey' => 'category_id',
			'dependent' => true,
		],
		'CategoriesEditor' => [
			'className' => 'CategoriesEditor',
			'foreignKey' => 'category_id',
			'dependent' => true,
		],
	];
	
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
		'CategoryType' => [
			'className' => 'CategoryType',
			'foreignKey' => 'category_type_id',
			'plugin_snapshot' => true,
		],
		'OrgGroup' => [
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		],
		'AdAccount' => [
			'className' => 'AdAccount',
			'foreignKey' => 'ad_account_id',
		],
		'Sac' => [
			'className' => 'Sac',
			'foreignKey' => 'sac_id',
		],
		'AssessmentCustRisk' => [
			'className' => 'AssessmentCustRisk',
			'foreignKey' => 'assessment_cust_risk_id',
			'plugin_snapshot' => true,
		],
		'AssessmentNihRisk' => [
			'className' => 'AssessmentNihRisk',
			'foreignKey' => 'assessment_nih_risk_id',
			'plugin_snapshot' => true,
		],
		'AssessmentOffice' => [
			'className' => 'AssessmentOffice',
			'foreignKey' => 'assessment_office_id',
			'plugin_snapshot' => true,
		],
		'AssessmentOrganization' => [
			'className' => 'AssessmentOrganization',
			'foreignKey' => 'assessment_organization_id',
			'plugin_snapshot' => true,
		],
	];
	
	public $hasAndBelongsToMany = [
		'CombinedView' => [
			'className' => 'CombinedView',
			'joinTable' => 'combined_view_categories',
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'combined_view_id',
			'unique' => 'keepExisting',
			'with' => 'CombinedViewCategory',
		],
		'Vector' => [
			'className' => 'Vector',
			'joinTable' => 'categories_vectors',
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesVector',
		],
		'Signature' => [
			'className' => 'Signature',
			'joinTable' => 'categories_signature',
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'signature_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesSignature',
		],
	];
	
	public $actsAs = [
		'Correlation',
		'Tags.Taggable',
		'Cacher.Cache' => [
			'config' => 'slowQueries',
			'clearOnDelete' => false,
			'clearOnSave' => false,
			'gzip' => false,
		],
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
				'created' => [],
				'modified' => [],
			],
		],
	];
	
	public $searchFields = [
		'User.name',
		'OrgGroup.name',
		'Category.name',
		'Category.mysource',
		'Category.victim_ip',
		'Category.victim_mac',
		'Category.victim_asset_tag',
		'CategoriesDetail.desc',
		'CategoryType.name',
		'AssessmentCustRisk.name',
		'AssessmentNihRisk.name',
		'AdAccount.username',
		'Sac.shortname',
		'Vector.vector',
	];
	
	public $toggleFields = ['public'];
	
	public function dashboardOverviewStats()
	{
		$conditions = $this->conditionsAvailable();
		
		$categories = $this->find('all', ['conditions' => $conditions]);
		
		$stats = [
			'total' => ['name' => __('Total'), 'value' => count($categories)],
		];
		
		return $stats;
	}
	
	public function compare($category_id_1 = false, $category_id_2 = false, $admin = false)
	{
	/*
	 * Compare 2 categories based on their vectors
	 */
		$data = [
			'category_1' => [
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => [],
			],
			'category_2' => [
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => [],
			],
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => [],
		];
		
		// get all of the good and active vectors for each of the categories
		$vectors_1_conditions = [
			'CategoriesVector.category_id' => $category_id_1,
			'Vector.bad' => 0,
		];
		if(!$admin)
		{
			$vectors_1_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_1 = $this->CategoriesVector->find('list', [
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => ['Vector.id', 'Vector.vector'],
			'conditions' => $vectors_1_conditions,
		]);
		asort($vectors_1);
		
		$vectors_2_conditions = [
			'CategoriesVector.category_id' => $category_id_2,
			'Vector.bad' => 0,
		];
		if(!$admin)
		{
			$vectors_2_conditions['CategoriesVector.active'] = 1;
		}
		
		$vectors_2 = $this->CategoriesVector->find('list', [
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => ['Vector.id', 'Vector.vector'],
			'conditions' => $vectors_2_conditions,
		]);
		asort($vectors_2);
		
		// find the unique vector_ids
		$vectors_1_unique = array_diff_assoc($vectors_1, $vectors_2);
		$vectors_2_unique = array_diff_assoc($vectors_2, $vectors_1);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_1, $vectors_2);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_1, $vectors_2));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		// build the strings for the ssdeep comparisons
		$string_1 = "\n". implode("\n",$vectors_1);

		$string_2 = "\n". implode("\n",$vectors_2);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_1, $string_2);
		
		$data = [
			'category_1' => [
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_1_unique,
			],
			'category_2' => [
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_vectors' => $vectors_2_unique,
			],
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		];
		return $data;
	}
	
	public function listCategoryRelatedIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		/////////// this category's vectors
		if(!$object_vector_ids = $this->CategoriesVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = ['Vector'];
		$conditions = [
			'CategoriesVector.vector_id' => $object_vector_ids,
			'CategoriesVector.category_id !=' => $object_id,
			'Vector.bad' => 0,
		];
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		$options = [
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => ['CategoriesVector.category_id', 'CategoriesVector.category_id'],
		];
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listCategoryRelated('.$object_id.')';
		}
		
		return $this->CategoriesVector->find('list', $options);
	}
	
	public function sqlCategoryRelatedOLD($category_id = false, $admin = false)
	{
		if(!$category_id) return false;
		
		$this->CategoriesVector->recursive = 0;
		$db = $this->CategoriesVector->getDataSource();
		
		$subQuery_conditions = ['CategoriesVector1.category_id' => $category_id, 'Vector1.bad' => 0];
		if(!$admin)
		{
			$subQuery_conditions['CategoriesVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			[
				'fields'	 => ['`CategoriesVector1`.`vector_id`'],
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`CategoriesVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => [
					[
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `CategoriesVector1`.`vector_id`'
					],
				],
			],
			$this->CategoriesVector
		);
		$subQuery = ' `CategoriesVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQuery2_conditions = ['Vector2.bad' => 0, $subQuery];
		if(!$admin)
		{
			$subQuery2_conditions['CategoriesVector2.active'] = 1;
		}
		
		// get the category_ids from this model that share the same vectors,
		$subQuery2 = $db->buildStatement(
			[
				'fields'	 => ['`CategoriesVector2`.`category_id`'],
				'table'		 => $db->fullTableName($this->CategoriesVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`CategoriesVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => [
					[
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `CategoriesVector2`.`vector_id`'
					],
				],
			],
			$this->CategoriesVector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Category`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
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
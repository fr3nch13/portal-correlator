<?php
App::uses('AppVectorParent', 'Model');

class Report extends AppVectorParent 
{
	public $useTable = 'reports';

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
		'ReportsDetail' => [
			'className' => 'ReportsDetail',
			'foreignKey' => 'report_id',
			'dependent' => true,
		]
	];
	
	public $hasMany = [
		'Upload' => [
			'className' => 'Upload',
			'foreignKey' => 'report_id',
			'dependent' => true,
		],
		'TempUpload' => [
			'className' => 'TempUpload',
			'foreignKey' => 'report_id',
			'dependent' => true,
		],
		'ReportsEditor' => [
			'className' => 'ReportsEditor',
			'foreignKey' => 'report_id',
			'dependent' => true,
		],
	];
	
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
		'ReportType' => [
			'className' => 'ReportType',
			'foreignKey' => 'report_type_id',
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
			'joinTable' => 'combined_view_reports',
			'foreignKey' => 'report_id',
			'associationForeignKey' => 'combined_view_id',
			'unique' => 'keepExisting',
			'with' => 'CombinedViewReport',
		],
		'Vector' => [
			'className' => 'Vector',
			'joinTable' => 'reports_vectors',
			'foreignKey' => 'report_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsVector',
		],
		'Signature' => [
			'className' => 'Signature',
			'joinTable' => 'reports_signature',
			'foreignKey' => 'report_id',
			'associationForeignKey' => 'signature_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsSignature',
		],
	];
	
	public $actsAs = [
		'Correlation',
		'Tags.Taggable',
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
		'Report.name',
		'Report.mysource',
		'Report.victim_ip',
		'Report.victim_mac',
		'Report.victim_asset_tag',
		'ReportsDetail.desc',
		'ReportType.name',
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
		
		$reports = $this->find('all', ['conditions' => $conditions]);
		
		$stats = [
			'total' => ['name' => __('Total'), 'value' => count($reports)],
		];
		
		return $stats;
	}
	
	public function compare($report_id_1 = false, $report_id_2 = false, $admin = false)
	{
		$data = [
			'report_1' => [
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => [],
			],
			'report_2' => [
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => [],
			],
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => [],
		];
		
		// get all of the good and active vectors for each of the reports
		$vectors_1_conditions = [
			'ReportsVector.report_id' => $report_id_1,
			'Vector.bad' => 0,
		];
		if(!$admin)
		{
			$vectors_1_conditions['ReportsVector.active'] = 1;
		}
		$vectors_1 = $this->ReportsVector->find('list', [
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => ['Vector.id', 'Vector.vector'],
			'conditions' => $vectors_1_conditions,
		]);
		asort($vectors_1);
		
		$vectors_2_conditions = [
			'ReportsVector.report_id' => $report_id_2,
			'Vector.bad' => 0,
		];
		if(!$admin)
		{
			$vectors_2_conditions['ReportsVector.active'] = 1;
		}
		
		$vectors_2 = $this->ReportsVector->find('list', [
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
		$string_1 = implode("\n",$vectors_1);

		$string_2 = implode("\n",$vectors_2);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_1, $string_2);
		
		$data = [
			'report_1' => [
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_vectors' => $vectors_1_unique,
			],
			'report_2' => [
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
	
	public function listReportRelatedIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		/////////// this report's vectors
		if(!$object_vector_ids = $this->ReportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = ['Vector'];
		$conditions = [
			'ReportsVector.vector_id' => $object_vector_ids,
			'ReportsVector.report_id !=' => $object_id,
			'Vector.bad' => 0,
		];
		
		if(!$admin and $org_group_id and $user_id)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		$options = [
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => ['ReportsVector.report_id', 'ReportsVector.report_id'],
		];
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listReportRelated('.$object_id.')';
		}
		
		return $this->ReportsVector->find('list', $options);
	}
	
	public function sqlReportRelated($report_id = false, $admin = false)
	{
	/*
	 * Report related to another report
	 * Builds the complex query for the conditions
	 */
		if(!$report_id) return false;
		
		// get the vector ids from this report
		$this->ReportsVector->recursive = 0;
		$db = $this->ReportsVector->getDataSource();
		
		$subQuery_conditions = ['ReportsVector1.report_id' => $report_id, 'Vector1.bad' => 0];
		if(!$admin)
		{
			$subQuery_conditions['ReportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			[
				'fields'	 => ['`ReportsVector1`.`vector_id`'],
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ReportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => [
					[
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ReportsVector1`.`vector_id`'
					],
				],
			],
			$this->ReportsVector
		);
		$subQuery = ' `ReportsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQuery2_conditions = ['Vector2.bad' => 0, $subQuery];
		if(!$admin)
		{
			$subQuery2_conditions['ReportsVector2.active'] = 1;
		}
		
		// get the report_ids from this model that share the came vectors,
		$subQuery2 = $db->buildStatement(
			[
				'fields'	 => ['`ReportsVector2`.`report_id`'],
				'table'		 => $db->fullTableName($this->ReportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`ReportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => [
					[
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `ReportsVector2`.`vector_id`'
					],
				],
			],
			$this->ReportsVector
		);
		// get the reports themselves
		
		$subQuery2 = ' `Report`.`id` IN (' . $subQuery2 . ') ';
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
<?php
App::uses('AppVectorParent', 'Model');

class TempReport extends AppVectorParent 
{
	public $useTable = 'temp_reports';

	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'public' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
    	'zipfile' => array(
    	    'rule'    => array('RuleZipMimeType'),
    	    'message' => 'Invalid file type, must be a zip file.'
    	),
	);
	
	public $hasOne = array(
		'TempReportsDetail' => array(
			'className' => 'TempReportsDetail',
			'foreignKey' => 'temp_report_id',
			'dependent' => true,
		)
	);
	
	public $hasMany = array(
		'TempUpload' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'temp_report_id',
			'dependent' => true,
		),
		'TempReportsEditor' => array(
			'className' => 'TempReportsEditor',
			'foreignKey' => 'temp_report_id',
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ReportType' => array(
			'className' => 'ReportType',
			'foreignKey' => 'report_type_id',
		),
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
		'AdAccount' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'ad_account_id',
		),
		'Sac' => array(
			'className' => 'Sac',
			'foreignKey' => 'sac_id',
		),
		'AssessmentCustRisk' => array(
			'className' => 'AssessmentCustRisk',
			'foreignKey' => 'assessment_cust_risk_id',
		),
		'AssessmentNihRisk' => array(
			'className' => 'AssessmentNihRisk',
			'foreignKey' => 'assessment_nih_risk_id',
		),
		'AssessmentOffice' => array(
			'className' => 'AssessmentOffice',
			'foreignKey' => 'assessment_office_id',
		),
		'AssessmentOrganization' => array(
			'className' => 'AssessmentOrganization',
			'foreignKey' => 'assessment_organization_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'TempVector' => array(
			'className' => 'TempVector',
			'joinTable' => 'temp_reports_vectors',
			'foreignKey' => 'temp_report_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempReportsVector',
		),
		'Signature' => array(
			'className' => 'Signature',
			'joinTable' => 'reports_signatures',
			'foreignKey' => 'temp_report_id',
			'associationForeignKey' => 'signature_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsSignature',
		),
	);
	
	public $actsAs = array('Tags.Taggable');
	
	// define the fields that can be searched
	public $searchFields = array(
		'TempReport.name',
		'TempReport.mysource',
		'ReportType.name',
		'AssessmentCustRisk.name',
		'AssessmentNihRisk.name',
		'AdAccount.username',
		'Sac.shortname',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('public');
	
	public $Report = false;
	
	public function batchSave($data = false)
	{
	/*
	 * Used to unzip and save multiple objects from a zip file
	 */
		// validate the data, and make sure it's a zip file
		if(!isset($data[$this->alias]['zipfile']))
		{
				$this->validationErrors['zipfile'] = __r('Please upload a zip file.');
				return false;
		}
		
		$this->set($data);
		if(!$this->validates()) return false;
		
		$zip_details = $this->data[$this->alias]['zipfile'];
		
		$files = $this->processZipFile($zip_details['tmp_name'], true);

		unset($data[$this->alias]['zipfile']);
		
		$save_data_each = array();
		$save_data = array();
		
		// save each one individually
		foreach($files as $file)
		{
			$save_data_each = $data;
			
			$file_name = array_pop(explode(DS, $file));
			
			// set the name of the report to the file name
			$save_data_each[$this->alias]['name'] = $file_name;
			
			// fill out the tempdetails array;
			$save_data_each['TempReportsDetail'] = array(
				'desc' => 'batch added from the zip file: '. $zip_details['name'],
			);
			
			// fill out the 'TempUpload' array
			$save_data_each['TempUpload']['file'] = array(
				'name' => $file_name,
				'type' => mime_content_type($file),
				'tmp_name' => $file,
				'error' => 0,
				'size' => filesize($file),
			);
			
			$save_data[] = $save_data_each;
		}
		
		if($return = $this->saveMany($save_data, array('deep' => true)))
		{
			// remove the zip directory
			$this->removeZipDir();
		}
		
		return $return;
	}
	
	public function reviewed($id = false)
	{
		$this->recursive = 1;
		$temp_report = $this->read(null, $id);
		if(!$temp_report) return false;
		
		// save the report and it's details first
		if(!$this->Report)
		{
			App::import('Model', 'Report');
			$this->Report = new Report();
		}
		
		// build the save array
		$data = array();
		$data['Report'] = $temp_report['TempReport'];
		$data['ReportsDetail'] = $temp_report['TempReportsDetail'];
		
		// remove all of the ids
		unset(
			$data['Report']['id'],
			$data['ReportsDetail']['id'],
			$data['ReportsDetail']['temp_report_id']
		);
		
		// add the reviewed date
		$data['Report']['reviewed'] = date('Y-m-d H:i:s');
		
		$this->Report->create();
		$this->Report->data = $data;
		
		// save the new report
		if(!$this->Report->saveAssociated($this->Report->data)) return false;
		
		// save the editors/contributors
		if(isset($temp_report['TempReportsEditor']) and count($temp_report['TempReportsEditor']))
		{
			if(!$this->Report->ReportsEditor->reviewed($this->Report->id, $temp_report['TempReportsEditor']))
			{
				$this->reviewError .= "\n". __(' The Editors/Contributors weren\'t transfered correctly.');
				return false;
			}
		}
		
		// save the vectors and associations
		if(isset($temp_report['TempVector']) and count($temp_report['TempVector']))
		{
			if(!$this->Report->ReportsVector->reviewed($this->Report->id, $temp_report['TempVector']))
			{
				$this->reviewError .= "\n". __(' The Vectors weren\'t transfered correctly.');
				return false;
			}
		}
		
		// mark the uploads as reviewed
		
		if($temp_report['TempUpload'])
		{
			$upload_ids = Set::extract('/TempUpload/id', $temp_report);
			$this->TempUpload->reviewed($upload_ids, 0, $this->Report->id);
		}
		
		// mark the uploads as reviewed
		if(isset($temp_report['Signature']) and $temp_report['Signature'])
		{
			$this->ReportsSignature->reviewed($id, $this->Report->id);
		}
		
		// remove them from the temp tables
		$this->id = $id;
		$this->delete();
		
		return $this->Report->id;
	}
	
// - not used
	public function sqlTempReportRelated($temp_report_id = false, $admin = false)
	{
	/*
	 * TempReport related to another temp_report
	 * Builds the complex query for the conditions
	 */
		if(!$temp_report_id) return false;
		
		// get the temp_vector ids from this temp_report
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
		$subQuery = ' `TempReportsVector2`.`temp_vector_id` IN (' . $subQuery . ') ';
		
		$subQuery2_conditions = array('TempVector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['TempReportsVector2.active'] = 1;
		}
		
		// get the temp_report_ids from this model that share the came temp_vectors,
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
		// get the temp_reports themselves
		
		$subQuery2 = ' `TempReport`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
// - not used
	public function compare($temp_report_id_1 = false, $temp_report_id_2 = false, $admin = false)
	{
	/*
	 * Compare 2 temp_reports based on their temp_vectors
	 */
		$data = array(
			'temp_report_1' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'temp_report_2' => array(
				'string' => false,
				'hash' => false,
				'unique_temp_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_temp_vector_ids' => array(),
		);
		
		// get all of the good and active temp_vectors for each of the temp_reports
		$temp_vectors_1_conditions = array(
			'TempReportsVector.temp_report_id' => $temp_report_id_1,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_1_conditions['TempReportsVector.active'] = 1;
		}
		$temp_vectors_1 = $this->TempReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.id', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_1_conditions,
		));
		asort($temp_vectors_1);
		
		$temp_vectors_2_conditions = array(
			'TempReportsVector.temp_report_id' => $temp_report_id_2,
			'TempVector.bad' => 0,
		);
		if(!$admin)
		{
			$temp_vectors_2_conditions['TempReportsVector.active'] = 1;
		}
		
		$temp_vectors_2 = $this->TempReportsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'TempVector',
			'fields' => array('TempVector.id', 'TempVector.temp_vector'),
			'conditions' => $temp_vectors_2_conditions,
		));		
		asort($temp_vectors_2);
		
		// find the unique temp_vector_ids
		$temp_vectors_1_unique = array_diff_assoc($temp_vectors_1, $temp_vectors_2);
		$temp_vectors_2_unique = array_diff_assoc($temp_vectors_2, $temp_vectors_1);
		
		// find the similar temp_vector_ids
		$temp_vectors_similar = array_intersect_assoc($temp_vectors_1, $temp_vectors_2);

		// find out the percent of similar temp_vectors
		$temp_vector_count_similar = count($temp_vectors_similar);
		$temp_vector_count_total = count(array_merge($temp_vectors_1, $temp_vectors_2));
		$similar_percent = round(($temp_vector_count_similar / $temp_vector_count_total) * 100, 2);
		
		
		
		// build the strings for the ssdeep comparisons
		$string_1 = implode("\n",$temp_vectors_1);

		$string_2 = implode("\n",$temp_vectors_2);
		
		$ssdeep_info = $this->ssdeep_compareStrings($string_1, $string_2);
		
		$data = array(
			'temp_report_1' => array(
				'string' => $ssdeep_info['string_1']['string'],
				'hash' => $ssdeep_info['string_1']['hash'],
				'unique_temp_vectors' => $temp_vectors_1_unique,
			),
			'temp_report_2' => array(
				'string' => $ssdeep_info['string_2']['string'],
				'hash' => $ssdeep_info['string_2']['hash'],
				'unique_temp_vectors' => $temp_vectors_2_unique,
			),
			'ssdeep_percent' => $ssdeep_info['percent'],
			'similar_percent' => $similar_percent,
			'similar_temp_vectors' => $temp_vectors_similar,
		);
		return $data;
	}
}
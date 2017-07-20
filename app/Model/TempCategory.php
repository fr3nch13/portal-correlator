<?php
App::uses('AppVectorParent', 'Model');

class TempCategory extends AppVectorParent 
{
	public $useTable = 'temp_categories';

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
		'org_group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $hasOne = array(
		'TempCategoriesDetail' => array(
			'className' => 'TempCategoriesDetail',
			'foreignKey' => 'temp_category_id',
			'dependent' => true,
		)
	);
	
	public $hasMany = array(
		'TempUpload' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'temp_category_id',
			'dependent' => true,
		),
		'TempCategoriesEditor' => array(
			'className' => 'TempCategoriesEditor',
			'foreignKey' => 'temp_category_id',
			'dependent' => true,
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'CategoryType' => array(
			'className' => 'CategoryType',
			'foreignKey' => 'category_type_id',
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
			'joinTable' => 'temp_categories_vectors',
			'foreignKey' => 'temp_category_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempCategoriesVector',
		),
		'Signature' => array(
			'className' => 'Signature',
			'joinTable' => 'categories_signatures',
			'foreignKey' => 'temp_category_id',
			'associationForeignKey' => 'signature_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesSignature',
		),
	);
	
	public $actsAs = array('Tags.Taggable'); 
	
	// define the fields that can be searched
	public $searchFields = array(
		'TempCategory.name',
		'TempCategory.mysource',
		'CategoryType.name',
		'AssessmentCustRisk.name',
		'AssessmentNihRisk.name',
		'AdAccount.username',
		'Sac.shortname',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('public');
	
	public $Category = false;
	
	public function reviewed($id = false)
	{
		$this->recursive = 1;
		$temp_category = $this->read(null, $id);
		
		if(!$temp_category) return false;
		
		// save the category and it's details first
		if(!$this->Category)
		{
			App::import('Model', 'Category');
			$this->Category = new Category();
		}
		
		// build the save array
		$data = array();
		$data['Category'] = $temp_category[$this->alias];
		$data['CategoriesDetail'] = $temp_category['TempCategoriesDetail'];
		
		// remove all of the ids
		unset(
			$data['Category']['id'],
			$data['CategoriesDetail']['id'],
			$data['CategoriesDetail']['temp_category_id']
		);
		
		// add the reviewed date
		$data['Category']['reviewed'] = date('Y-m-d H:i:s');
		
		$this->Category->create();
		$this->Category->data = $data;
		
		// save the new category
		if(!$this->Category->saveAssociated($this->Category->data)) return false;
		
		// save the editors/contributors
		if(isset($temp_category['TempCategoriesEditor']) and count($temp_category['TempCategoriesEditor']))
		{
			if(!$this->Category->CategoriesEditor->reviewed($this->Category->id, $temp_category['TempCategoriesEditor']))
			{
				$this->reviewError .= "\n". __(' The Editors/Contributors weren\'t transfered correctly.');
				return false;
			}
		}
		
		// save the vectors and associations
		if(isset($temp_category['TempVector']) and count($temp_category['TempVector']))
		{
			if(!$this->Category->CategoriesVector->reviewed($this->Category->id, $temp_category['TempVector']))
			{
				$this->reviewError .= "\n". __(' The Vectors weren\'t transfered correctly.');
				return false;
			}
		}	
		
		// mark the uploads as reviewed
		if(isset($temp_category['TempUpload']) and $temp_category['TempUpload'])
		{
			$upload_ids = Set::extract('/TempUpload/id', $temp_category);
			$this->TempUpload->reviewed($upload_ids, $this->Category->id);
		}
		
		// mark the uploads as reviewed
		if(isset($temp_category['Signature']) and $temp_category['Signature'])
		{
			$this->CategoriesSignature->reviewed($id, $this->Category->id);
		}
		
		// remove them from the temp tables
		$this->id = $id;
		if(!$this->delete($id, true))
		{
			$this->reviewError = __(' Unable to delete the %s.', __('Temp Category'));
			return false;
		}
		
		return $this->Category->id;
	}
}
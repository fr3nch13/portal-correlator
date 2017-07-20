<?php
App::uses('AppModel', 'Model');
/**
 * OrgGroup Model
 *
 * @property User $User
 */
class OrgGroup extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'TempUpload' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
	);
	
	// when an org group isn't assigned
	public $defaultOrgGroupName = 'Global';
	
	public function afterFind($results = array(), $primary = false)
	{
		if(empty($results))
		{
			$results[0] = $this->readGlobalObject();
		}
		if(isset($results[0]))
		{
			foreach($results as $i => $result)
			{
				$org_group_id = (isset($result[$this->alias]['id'])?$result[$this->alias]['id']:false);
				if(!$org_group_id)
				{
					$results[$i][$this->alias]['id'] = 0;
					$results[$i][$this->alias]['name'] = $this->defaultOrgGroupName;
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function beforeDelete($cascade = true)
	{
		// set the org_group_id for the has many to 0
		foreach($this->hasMany as $model => $info)
		{
			$this->{$model}->updateAll(
				array($model. '.'. $info['foreignKey'] => 0),
				array($model. '.'. $info['foreignKey'] => $this->id)
			);
		}
		return parent::beforeDelete($cascade = true);
	}
	
	public function read($fields = null, $id = null)
	{
		if(!$id)
		{
			return $this->Common_readGlobalObject();
		}
		return parent::read($fields, $id);
	}
}

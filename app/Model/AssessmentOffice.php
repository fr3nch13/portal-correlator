<?php
App::uses('AppModel', 'Model');

class AssessmentOffice extends AppModel 
{
	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'slug' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'assessment_office_id',
			'dependent' => false,
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'assessment_office_id',
			'dependent' => false,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'assessment_office_id',
			'dependent' => false,
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'assessment_office_id',
			'dependent' => false,
		),
	);
	
	public $actsAs = [
		'Snapshot.Stat' => [
			'entities' => [
				'all' => [],
			],
		],
	];
	
	// define the fields that can be searched
	public $searchFields = array(
		'AssessmentOffice.name',
	);
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]['name']) and !isset($this->data[$this->alias]['slug']))
		{
			$this->data[$this->alias]['slug'] = $this->slugify($this->data[$this->alias]['name']);
		}
		return parent::beforeSave($options);
	}
	
	public function checkAdd($name = false, $extra = array())
	{
		if(!$name) return false;
		
		$name = trim($name);
		if(!$name) return false;
		
		$slug = $this->slugify(strtolower($name));
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.slug' => $slug)))
		{
			return $id;
		}
		
		// not an existing one, create it
		$this->create();
		$this->data = array_merge(array('name' => $name, 'slug' => $slug), $extra);
		if($this->save($this->data))
		{
			return $this->id;
		}
		return false;
	}
}

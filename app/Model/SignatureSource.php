<?php
App::uses('AppModel', 'Model');
/**
 * SignatureSource Model
 *
 * @property CategoriesSignature $CategoriesSignature
 * @property ReportsSignature $ReportsSignature
 * @property Signature $Signature
 */
class SignatureSource extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CategoriesSignature' => array(
			'className' => 'CategoriesSignature',
			'foreignKey' => 'signature_source_id',
			'dependent' => false,
		),
		'ReportsSignature' => array(
			'className' => 'ReportsSignature',
			'foreignKey' => 'signature_source_id',
			'dependent' => false,
		),
		'Signature' => array(
			'className' => 'Signature',
			'foreignKey' => 'signature_source_id',
			'dependent' => false,
		),
		'YaraSignature' => array(
			'className' => 'YaraSignature',
			'foreignKey' => 'signature_source_id',
			'dependent' => false,
		),
		'SnortSignature' => array(
			'className' => 'SnortSignature',
			'foreignKey' => 'signature_source_id',
			'dependent' => false,
		)
	);
	
	public $addCache = array();
	
	public function beforeSave($options = array())
	{
		if(isset($this->data[$this->alias]['name']))
		{
			$this->data[$this->alias]['name'] = trim($this->data[$this->alias]['name']);
			
			if(!isset($this->data[$this->alias]['slug']))
			{
				$this->data[$this->alias]['slug'] = $this->makeSlug($this->data[$this->alias]['name']);
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function add($name = false)
	{
		$slug = $this->makeSlug($name);
		
		if(!$slug) return false;
		
		if(isset($this->addCache[$slug]))
		{
			return $this->addCache[$slug];
		}
		
		if(!$id = $this->field('id', array($this->alias.'.slug' => $slug)))
		{
			$this->create();
			$this->data[$this->alias] = array(
				'name' => $name,
				'slug' => $slug
			);
			if($this->save($this->data))
			{
				$id = $this->id;
				$this->addCache[$slug] = $id;
			}
		}
		
		return $id;
	}
	
	public function makeSlug($name = false)
	{
		$name = trim($name);
		$name = strtolower($name);
		return Inflector::slug($name);
	}
}

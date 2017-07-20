<?php
App::uses('AppModel', 'Model');
/**
 * CategoryType Model
 *
 * @property Category $Category
 * @property TempCategory $TempCategory
 */
class CategoryType extends AppModel 
{

	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_type_id',
			'dependent' => false,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'category_type_id',
			'dependent' => false,
		)
	);
	
	public $belongsTo = array(
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'CategoryType.name',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	// fields that are boolean and marks the record as the default
	// e.g. only one record can be 1, the rest are 0
	public $defaultFields = array('holder');
	
	public function delete($id = null, $cascade = false)
	{
		if(!$id) return false;
		
		// update all categories to have their category_type_ids set to 0
		$this->Category->updateAll(
			array('Category.category_type_id' => $id),
			array('Category.category_type_id' => 0)
		);
		$this->TempCategory->updateAll(
			array('TempCategory.category_type_id' => $id),
			array('TempCategory.category_type_id' => 0)
		);
		return parent::delete($id, $cascade);
	}
	
	public function add($name = null)
	{
		if(!$name) return false;
		
		// return the id if it already exists
		if($id = $this->field('id', array('name' => $name)))
		{
			return $id;
		}
		$this->create();
		if($this->save(array('name' => $name)))
		{
			return $this->id;
		}
		return false;
	}
}

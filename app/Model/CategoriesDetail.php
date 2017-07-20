<?php
App::uses('AppModel', 'Model');
/**
 * CategoriesDetail Model
 *
 * @property Category $Category
 */
class CategoriesDetail extends AppModel 
{

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
		)
	);
}

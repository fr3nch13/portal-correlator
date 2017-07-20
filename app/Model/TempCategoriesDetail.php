<?php
App::uses('AppModel', 'Model');
/**
 * TempCategoriesDetail Model
 *
 * @property TempCategory $TempCategory
 */
class TempCategoriesDetail extends AppModel 
{

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'temp_category_id',
		)
	);
}

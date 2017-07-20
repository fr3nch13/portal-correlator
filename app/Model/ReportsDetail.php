<?php
App::uses('AppModel', 'Model');
/**
 * ReportsDetail Model
 *
 * @property Report $Report
 */
class ReportsDetail extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
			),
		),
		'desc' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a Description',
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'report_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

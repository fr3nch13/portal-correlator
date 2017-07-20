<?php
App::uses('AppModel', 'Model');
/**
 * TempReportsDetail Model
 *
 * @property TempReport $TempReport
 */
class TempReportsDetail extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'temp_report_id' => array(
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
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'temp_report_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

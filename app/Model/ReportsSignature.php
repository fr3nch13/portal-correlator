<?php
App::uses('AppModel', 'Model');
/**
 * ReportsSignature Model
 *
 * @property Report $Report
 * @property TempReport $TempReport
 * @property Signature $Signature
 * @property SignatureSource $SignatureSource
 */
class ReportsSignature extends AppModel 
{
	public $validate = array(
		'report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'temp_report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'signature_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);
	
	public $belongsTo = array(
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'report_id',
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'temp_report_id',
		),
		'Signature' => array(
			'className' => 'Signature',
			'foreignKey' => 'signature_id',
		),
		'SignatureSource' => array(
			'className' => 'SignatureSource',
			'foreignKey' => 'signature_source_id',
		)
	);
	
	public $toggleFields = array('active');
	
	public function add($data = false, $report_id_field = 'report_id')
	{
		if(!isset($data[$report_id_field]))
		{
			$this->modelError = __('Unknown %s', __('Report'));
			return false;
		}
		
		if(!isset($data['signature_id']))
		{
			$this->modelError = __('Unknown %s', __('Signature'));
			return false;
		}
		
		// check to see if it exists first
		$conditions = array(
			$this->alias.'.'.$report_id_field => $data[$report_id_field],
			$this->alias.'.signature_id' => $data['signature_id'],
		);
		
		$id = false;
		
		if(!$id = $this->field('id', $conditions))
		{
			$this->create();
			$this->data[$this->alias] = $data;
			if($this->save($this->data))
			{
				$id = $this->id;
			}
			else
			{
				$this->modelError = __('Unable to save the %s to the %s', __('Signature'), __('Report'));
				return false;
			}
		}
		return $id;
	}
	
	public function reviewed($temp_report_id, $report_id)
	{
		return $this->updateAll(
			array(
				$this->alias.'.report_id' => $report_id,
				$this->alias.'.temp_report_id' => 0,
			),
			array(
				$this->alias.'.temp_report_id' => $temp_report_id,
			)
		);
	}
}

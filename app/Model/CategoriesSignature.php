<?php
App::uses('AppModel', 'Model');
/**
 * CategoriesSignature Model
 *
 * @property Category $Category
 * @property TempCategory $TempCategory
 * @property Signature $Signature
 * @property SignatureSource $SignatureSource
 */
class CategoriesSignature extends AppModel 
{
	public $validate = array(
		'category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'temp_category_id' => array(
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
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'temp_category_id',
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
	
	public function add($data = false, $category_id_field = 'category_id')
	{
		if(!isset($data[$category_id_field]))
		{
			$this->modelError = __('Unknown %s', __('Category'));
			return false;
		}
		
		if(!isset($data['signature_id']))
		{
			$this->modelError = __('Unknown %s', __('Signature'));
			return false;
		}
		
		// check to see if it exists first
		$conditions = array(
			$this->alias.'.'.$category_id_field => $data[$category_id_field],
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
				$this->modelError = __('Unable to save the %s to the %s', __('Signature'), __('Category'));
				return false;
			}
		}
		return $id;
	}
	
	public function reviewed($temp_category_id, $category_id)
	{
		return $this->updateAll(
			array(
				$this->alias.'.category_id' => $category_id,
				$this->alias.'.temp_category_id' => 0,
			),
			array(
				$this->alias.'.temp_category_id' => $temp_category_id,
			)
		);
	}
}

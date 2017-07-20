<?php
App::uses('AppModel', 'Model');
/**
 * VtDetectedUrl Model
 *
 * @property VectorLookup $VectorLookup
 * @property VectorSrc $VectorSrc
 * @property VectorDst $VectorDst
 */
class VtDetectedUrl extends AppModel 
{
	public $displayField = 'vector_lookup_id';
	
	public $validate = array(
		'vector_lookup_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'vector_url_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'VectorLookup' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_lookup_id',
		),
		'VectorUrl' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_url_id',
		),
	);
	
	public function checkAdd(
		$vector_lookup_id = false, 
		$vector_url_id = false,
		$data = array())
	{
		if(!$vector_lookup_id) return false;
		if(!$vector_url_id) return false;
		
		$id = false;
		
		if(!$id = $this->field('id', array(
			'vector_lookup_id' => $vector_lookup_id,
			'vector_url_id' => $vector_url_id,
		)))
		{
			$this->create();
			$data['vector_lookup_id'] = $vector_lookup_id;
			$data['vector_url_id'] = $vector_url_id;
			$data['first_seen'] = date('Y-m-d H:i:s');
		}
		else
		{
			$this->id = $id;
		}
			
		$data['last_seen'] = date('Y-m-d H:i:s');
		$this->data = $data;
		
		if($this->save($this->data))
		{
			$id = $this->id;
		}
		return $id;
	}
}

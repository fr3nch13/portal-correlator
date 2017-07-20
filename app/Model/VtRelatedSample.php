<?php
App::uses('AppModel', 'Model');
/**
 * VtRelatedSample Model
 *
 * @property VectorLookup $VectorLookup
 * @property VectorSrc $VectorSrc
 * @property VectorDst $VectorDst
 */
class VtRelatedSample extends AppModel 
{
	public $displayField = 'vector_lookup_id';
	
	public $validate = array(
		'vector_lookup_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'vector_sample_id' => array(
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
		'VectorSample' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_sample_id',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'VectorLookup.vector',
		'VectorSample.vector',
		'VtRelatedSample.type',
	);
	
	public function checkAdd(
		$vector_lookup_id = false, 
		$vector_sample_id = false,
		$data = array())
	{
		if(!$vector_lookup_id) return false;
		if(!$vector_sample_id) return false;
		
		$id = false;
		
		if(!$id = $this->field('id', array(
			'vector_lookup_id' => $vector_lookup_id,
			'vector_sample_id' => $vector_sample_id,
		)))
		{
			$this->create();
			$data['vector_lookup_id'] = $vector_lookup_id;
			$data['vector_sample_id'] = $vector_sample_id;
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

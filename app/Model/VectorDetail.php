<?php
App::uses('AppModel', 'Model');
/**
 * VectorDetail Model
 *
 * @property Vector $Vector
 */
class VectorDetail extends AppModel 
{
	public $validate = array(
		'vector_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'vt_lookup' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);
	
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		)
	);
	
	public $checkAddCache = array();
	
	public function checkAddUpdate($vector_id = false, $data = array())
	{
		if(!$vector_id) return false;
		
		if(isset($this->checkAddCache[$vector_id])) return $this->checkAddCache[$vector_id];
		
		$id = false;
		
		if(!$id = $this->field('id', array('vector_id' => $vector_id)))
		{
			$this->create();
		}
		else
		{
			$this->id = $id;
		}
		
		// only update if there is something to update, and if it already exists
		if($id)
		{
			if(!empty($data))
			{
				$data['vector_id'] = $vector_id;
			}
		}
		else
		{
			$data['vector_id'] = $vector_id;
		}
		
		if(!empty($data))
		{
			$this->data = $data;
			
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		
		$this->checkAddCache[$vector_id] = $id;
		
		return $id;
	}
}

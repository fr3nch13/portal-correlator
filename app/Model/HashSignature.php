<?php
App::uses('AppModel', 'Model');
/**
 * HashSignature Model
 *
 * @property Vector $Vector
 */
class HashSignature extends AppModel 
{
	public $validate = array(
		'vector_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		)
	);
	
	public function checkAdd($vector_id = false, $data = array())
	{
pr($data);
		if(!$vector_id) return false;
		
		if(!$id = $this->field('id', array('vector_id' => $vector_id)))
		{
			$this->create();
			
			$data['vector_id'] = $vector_id;
			$this->data = $data;
			
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		return $id;
	}
	
	public function getInternalConditions($exclude = false)
	{
		// get the list of items that are considered local from the app config
		if(!$local_config = Configure::read('AppConfig.Nslookup.internal_hashes'))
		{
			return array();
		}
		
		$local_config = explode(',', $local_config);
		
		// clean them up. this is after all, user input
		foreach($local_config as $i => $local_item)
		{
			$local_item = trim($local_item);
			if(!$local_item) { unset($local_config[$i]); continue; }
			$local_item = strtolower($local_item);
			$local_config[$i] = $local_item;
		}
		// remove duplicates
		$local_config = array_flip($local_config);
		$local_config = array_flip($local_config);
		
		// build the query conditions with the Search.SearchableBehavior
		$conditions = array(
			'q' => implode("\n", $local_config), 
			'ex' => $exclude,
			'searchFields' => array(
				'Vector.vector' => array('direction' => 'left'),
			),
			'padding' => 20,
		);
		
		return $this->orConditions($conditions);
	}
}

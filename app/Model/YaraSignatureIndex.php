<?php
App::uses('AppModel', 'Model');
/**
 * YaraSignatureIndex Model
 *
 * @property YaraSignature $YaraSignature
 */
class YaraSignatureIndex extends AppModel 
{
	public $useTable = 'yara_signature_index';
	
	public $validate = array(
		'yara_signature_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'YaraSignature' => array(
			'className' => 'YaraSignature',
			'associationForeignKey' => 'yara_signature_id',
		)
	);
	
	public $allowed_types = array('meta', 'string', 'condition');
	
	public function checkAdd($yara_signature_id = false, $type = false, $key = false, $value = false)
	{
		if(!in_array($type, $this->allowed_types))
		{
			$this->modelError = __('unknown type');
			return false;
		}
		// conditions don't have a key according to yara, we'll make one
		if($type == 'condition')
		{
			if(!$key) $key = Inflector::slug($value);
		}
		
		$id = $this->field('id', array($this->alias.'.yara_signature_id' => $yara_signature_id, $this->alias.'.type' => $type, $this->alias.'.key' => $key));
		
		if(!$id)
		{
			$this->create();
			$this->data = array(
				$this->alias => array(
					'yara_signature_id' => $yara_signature_id,
					'type' => $type,
					'key' => $key,
					'value' => $value,
				),
			);
			if(!$this->save($this->data))
			{
				$this->modelError = __('Unable to save this setting');
				return false;
			}
			$id = $this->id;
		}
		return $id;
	}
	
	public function update($yara_signature_id = false, $type = false, $options = array())
	{
		$existing = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.yara_signature_id' => $yara_signature_id,
				$this->alias.'.type' => $type,
				
			),
		));
		
		$remove_ids = array();
		$updates = array();
		
		foreach($existing as $i => $_existing)
		{
			$option_id = $_existing[$this->alias]['id'];
			// update existing options
			$key = $_existing[$this->alias]['key'];
			$value = $_existing[$this->alias]['value'];
			if(isset($options[$key]))
			{
				// update
				if($value != $options[$key])
				{
					$updates[] = array(
						$this->alias => array(
							'id' => $option_id,
							'value' => $options[$key],
						),
					);
				}
				unset($options[$key]);
			}
			// mark the removed ones to delete
			elseif(!isset($options[$key]))
			{
				$remove_ids[$option_id] = $option_id;
			}
		}
		
		// options should only contain the new ones now
		foreach($options as $key => $value)
		{
			$updates[] = array(
				$this->alias => array(
					'yara_signature_id' => $yara_signature_id,
					'type' => $type,
					'key' => $key,
					'value' => $value,
				),
			);
		}
		
		$this->saveMany($updates);
		$this->deleteAll(array($this->alias.'.id' => $remove_ids), false);
	}
}

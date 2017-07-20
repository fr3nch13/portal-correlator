<?php
App::uses('AppModel', 'Model');
/**
 * SnortSignatureIndex Model
 *
 * @property SnortSignature $SnortSignature
 */
class SnortSignatureIndex extends AppModel 
{
	public $useTable = 'snort_signature_index';
	
	public $validate = array(
		'snort_signature_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'SnortSignature' => array(
			'className' => 'SnortSignature',
			'associationForeignKey' => 'snort_signature_id',
		)
	);
	
	public function checkAdd($snort_signature_id = false, $key = false, $value = false)
	{
		$id = $this->field('id', array($this->alias.'.snort_signature_id' => $snort_signature_id, $this->alias.'.key' => $key));
		
		if(!$id)
		{
			$this->create();
			$this->data = array(
				$this->alias => array(
					'snort_signature_id' => $snort_signature_id,
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
		$this->id = $id;
		return $id;
	}
	
	public function update($snort_signature_id = false, $options = array())
	{
		$existing = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.snort_signature_id' => $snort_signature_id
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
					'snort_signature_id' => $snort_signature_id,
					'key' => $key,
					'value' => $value,
				),
			);
		}
		
		$this->saveMany($updates);
		$this->deleteAll(array($this->alias.'.id' => $remove_ids), false);
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * Ipaddress Model
 *
 * @property Vector $Vector
 */
class Geoip extends AppModel 
{

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_id',
		),
	);
	
	// add plugins and other behaviors
	public $actsAs = array('Maxmind.Geoip', 'Utilities.Extractor');
	
	public function lookupVector($vector = '', $force_update = false)
	{
		if(!$vector)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
		
		if(!$vector_id = $this->Vector->field('id', array('vector' => $vector)))
		{
			$this->modelError = __('Unknown Vector ID');
			return false;
		}
		
		return $this->lookupVectorId($vector_id, $vector, $force_update);
	}
	
	public function lookupVectorId($vector_id = false, $vector = false, $force_update = false)
	{
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector Id');
			return false;
		}
		
		if(!$vector)
		{
			$this->Vector->id = $vector_id;
			if(!$vector = $this->Vector->field('vector'))
			{
				$this->modelError = __('Unknown Vector');
				return false;
			}
		}
		
		return $this->checkAdd($vector_id, $vector, $force_update);
	}
	
	public function checkAdd($vector_id = false, $vector = false, $force_update = false)
	{
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector id');
			return false;
		}
		
		$record = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'Geoip.vector_id' => $vector_id,
			),
		));
		
		// possible redundant
		if(!$vector)
		{
			$this->Vector->id = $vector_id;
			if(!$vector = $this->Vector->field('vector'))
			{
				$this->modelError = __('Unknown Vector');
				return false;
			}
		}
		
		if($this->EX_discoverType($vector) !== 'ipaddress')
		{
			$this->modelError = __('Not an IP Address');
			return false;
		}
		
		if(!$results = $this->GeoipAll($vector))
		{
			// modelError gets set in the behavior
			return false;
		}
		
		$isChecked = true;
		$isUpdated = false;
		
		// new
		if(!$record)
		{
			$this->create();
			$this->data = $results;
			$this->data['vector_id'] = $vector_id;
			$this->save($this->data);
			$geoip_id = $this->id;
		}
		// existing, update if older then 3 months (age of the database)
		else
		{
			$geoip_id = $record[$this->alias]['id'];
			$this->id = $geoip_id;
			
			$update_data = false;
			
			// check modified date
			if(isset($record[$this->alias]['modified']))
			{
				$age_ago = strtotime('-3 months');
				$modified = strtotime($record[$this->alias]['modified']);
				
				// update the record
				if($modified < $age_ago)
				{
					$update_data = $results;
				}
			}
			
			if($force_update)
			{
				$update_data = $results;
			}
			
			if($update_data)
			{
				$this->data = $update_data;
				$this->data['modified'] = date('Y-m-d H:i:s');
				
				if($this->save($this->data))
				{
					$isUpdated = true;
				}
			}
			if($isChecked or $isUpdated)
			{
				$ipData = [];
				if($isChecked)
					$ipData['geoip_checked'] = "'".date('Y-m-d H:i:s')."'";
				if($isUpdated)
					$ipData['geoip_updated'] = "'".date('Y-m-d H:i:s')."'";
				$this->Vector->Ipaddress->updateAll($ipData, ['Ipaddress.vector_id' => $vector_id]);
			}
		}
		return $this->read(null);
	}
	
	public function checkAddBlank($vector_id = false, $dns_auto_lookup = false)
	{
	/*
	 * Adds a record for a vector
	 */
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector id');
			$this->shellOut($this->modelError);
			return false;
		}
		
		if(!$id = $this->field('id', array('vector_id' => $vector_id)))
		{
			$this->create();
			$this->data = array(
				'vector_id' => $vector_id,
			);
			if($dns_auto_lookup !== false)
			{
				$this->data['dns_auto_lookup'] = $dns_auto_lookup;
			}
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		elseif($dns_auto_lookup !== false)
		{
			$this->id = $id;
			$this->data = array(
				'dns_auto_lookup' => $dns_auto_lookup,
			);
			$this->save($this->data);
		}
		return $id;
	}
}
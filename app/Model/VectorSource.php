<?php
App::uses('AppModel', 'Model');
/**
 * Hostname Model
 *
 * @property Vector $Vector
 */
class VectorSource extends AppModel 
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
		)
	);
	
	public $actsAs = array(
		'Utilities.Shell',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'VectorSource.source_type',
		'VectorSource.source',
		'VectorSource.sub_source',
	);
	
	public $soutceTypes = array(
		'manual' => 'Manual',
		'dns' => 'DNS',
		'feed' => 'feed',
	);
	
	public $existings = array();
	
	public function afterFind($results = array(), $primary = false)
	{
		foreach ($results as $key => $val) 
		{
			if(isset($val[$this->alias]['source']) and $val[$this->alias]['source'] == 'upload')
			{
				$results[$key][$this->alias]['source'] = 'file';
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function fixSources()
	{
		$this->shell_nolog = true;
		$this->shell_input = 2;
		$start = time();
		$keep_going = true;
		$last_id = 0;
		$total_count = 0;
		$total = 0;
		$last_time_diff = 0;
		
		/// keeping an accurate count
		if($last_id > 0)
		{
			$total_count = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'VectorSource.id <' => $last_id,
				),
				'order' => array('VectorSource.id' => 'ASC')
			));
		}
		
		while($keep_going)
		{
			if(!$vector_source = $this->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'VectorSource.id >' => $last_id,
				),
				'order' => array('VectorSource.id' => 'ASC')
			)))
			{
				$keep_going = false;
				continue;
			}
			$last_id = $vector_source['VectorSource']['id'];
			$count = $vector_source['VectorSource']['count'];
			
			$updated = 0;
			
			$dupe_conditions = array(
					'VectorSource.id !=' => $vector_source['VectorSource']['id'],
					'VectorSource.vector_id' => $vector_source['VectorSource']['vector_id'],
					'VectorSource.source_type' => $vector_source['VectorSource']['source_type'],
					'VectorSource.source' => $vector_source['VectorSource']['source'],
					'VectorSource.sub_source' => $vector_source['VectorSource']['sub_source'],
					'VectorSource.created BETWEEN ? and ?' => array(
						date('Y-m-d H:i:00', strtotime($vector_source['VectorSource']['created'])),
						date('Y-m-d H:i:59', strtotime($vector_source['VectorSource']['created'])),
					),
				);
			
			$dupes = $this->find('list', array(
				'recursive' => -1,
				'conditions' => $dupe_conditions,
				'fields' => array('VectorSource.id', 'VectorSource.id'),
			));
			
			$dupe_count = count($dupes);
			
			
			// update the first record if needed
			
			if($vector_source['VectorSource']['count'] == 0 or count($dupes))
			{
				$count = ($vector_source['VectorSource']['count']?$vector_source['VectorSource']['count']:1) + $dupe_count;
				$this->id = $last_id;
				$this->data = array(
					'VectorSource' => array(
						'count' => $count,
					),
				);
				
				if($this->save($this->data))
				{
					$updated = 1;
					// delete the duplicates
					if(count($dupes))
					{
						if($this->deleteAll(array('VectorSource.id' => $dupes), false, false))
						{
							$updated = 2;
						}
					}
				}
			}
			
			if(!$total or $dupe_count)
			{
				// update count from the database every ten times
				if (!$total or $total_count % 10000 == 1) 
				{
					$total = $this->find('count');
				}
				else
				{
					$total = ($total - $dupe_count);
				}
			}
			
			$end = time();
			$time_diff = $end - $start;
			$total_count++;
			
			$percent = round((($total_count / $total) * 100), 3);
			
			if($last_time_diff != $time_diff or ($dupe_count and $updated))
			{
				$this->shellOut(__("%s\tIDs: %s,%s\t(%s\t%s\t%s)\t(%s-%s) - %s - time: %s", 
					$percent,
					$last_id, 
					$vector_source['VectorSource']['vector_id'],  
					$dupe_count, 
					$count, 
					$updated,
					$total_count, 
					$total, 
					date('Y.m.d.H', strtotime($vector_source['VectorSource']['created'])),
					$time_diff)
				);
			}
			
			$last_time_diff = $time_diff;
		}
	}
	
	public function add($vector_id = false, $source_type = '', $source = '', $vector_added_date = false, $sub_source = '')
	{
	/*
	 * Adds an entry for the vector when it is added to anything
	 */
		if(!$vector_id)
		{
			$this->modelError = __('Unknown Vector');
			return false;
		}
		
		if(!$vector_added_date) $vector_added_date = date('Y-m-d H:i:s');
		
		$first = false;
		if(!$id = $this->field('id', array('vector_id' => $vector_id)))
		{
			$first = true;
		}
		
		$existing_conditions = array(
			'VectorSource.vector_id' => $vector_id,
			'VectorSource.source_type' => $source_type,
			'VectorSource.source' => $source,
			'VectorSource.sub_source' => $sub_source,
			'VectorSource.created BETWEEN ? and ?' => array(
				date('Y-m-d H:i:00', strtotime($vector_added_date)),
				date('Y-m-d H:i:59', strtotime($vector_added_date)),
			),
		);
		
		$existing_key = md5(serialize($existing_conditions));
		
		$count = 0;
		$vector_source_id = 0;
		if(!isset($this->existings[$existing_key]))
		{
			$this->existings[$existing_key] = 0;
			
			// see if this source instance exists
			$existing = $this->find('list', array(
				'conditions' => $existing_conditions,
				'fields' => array('VectorSource.id', 'VectorSource.count'),
			));
			
			foreach($existing as $vector_source_id => $vector_source_count)
			{
				if($vector_source_count == 0)
					$vector_source_count = 1;
				
				$count = $count + $vector_source_count;
			}
			$this->existings[$existing_key] = $count;
		}
		
		if($this->existings[$existing_key] == 0)
		{
			$this->create();
			$count++;
		}
		else
		{
			$this->id = $vector_source_id;
			$count++;
		}
		
		$this->data = array(
			'VectorSource' => array(
				'vector_id' => $vector_id,
				'source_type' => $source_type,
				'source' => $source,
				'sub_source' => $sub_source,
				'created' => $vector_added_date,
				'first' => $first,
				'last' => true,
				'count' => $count,
			),
		);
		
		if($this->save($this->data))
		{
			if($this->id)
			{
				$this->updateAll(
					array('VectorSource.last' => false),
					array(
						'VectorSource.vector_id' => $vector_id,
						'VectorSource.last' => true,
						'VectorSource.id !=' => $this->id,
					)
				);
			}
			return $this->id;
		}
		return false;
	}
	
	public function addBatch($vector_ids = array(), $source_type = '', $source = '', $vector_added_dates = array(), $sub_source = '')
	{
	/*
	 * Used when initially adding a set of vectors to something, and needing to save all of them
	 */
		if(empty($vector_ids))
		{
			$this->modelError = __('Unknown Vectors');
			return false;
		}
		if(is_string($vector_ids) or is_int($vector_ids))
		{
			$vector_ids = array($vector_ids);
		}
		
		$vector_added_date = date('Y-m-d H:i:s');
		if(is_string($vector_added_dates) or is_int($vector_added_dates))
		{
			$vector_added_date = $vector_added_dates;
		}
		
		// get a list of the 'first' vector sources
		$existing_firsts = $this->find('list', array(
			'fields' => array('VectorSource.vector_id', 'VectorSource.vector_id'),
			'conditions' => array(
				'VectorSource.vector_id' => $vector_ids,
				'VectorSource.first' => true,
			),
		));
		
		// mark all existing ones as no longer last
		$this->updateAll(
			array('VectorSource.last' => false),
			array(
				'VectorSource.last' => true,
				'VectorSource.vector_id' => $vector_ids,
			)
		);
		
		$this->data = array();
		foreach($vector_ids as $vector => $vector_id)
		{
			if(isset($vector_added_dates[$vector]))
			{
				$vector_added_date = $vector_added_dates[$vector];
			}
			elseif(isset($vector_added_dates[$vector_id]))
			{
				$vector_added_date = $vector_added_dates[$vector_id];
			}
			
			$first = false;
			if(!isset($existing_firsts[$vector_id]))
			{
				$first = true;
			}
			
			$this->data[] = array(
				'vector_id' => $vector_id,
				'source_type' => $source_type,
				'source' => $source,
				'sub_source' => $sub_source,
				'created' => $vector_added_date,
				'first' => $first,
				'last' => true,
			);
		}
		
		if(parent::saveMany($this->data))
		{
			return true;
		}
		return false;
	}
	
/** Temporary Functions for instituting changes to the production database **/

	public function tempCheckAdd($vector_id = false, $source_type = false, $source = '', $sub_source = '', $source_date = false)
	{
	/*
	 * Checks if a vector exists, if not, add it along with it's type
	 */
		if(!$id = $this->field('id', array(
			'vector_id' => $vector_id, 
			'source_type' => $source_type,
			'source' => $source,
			'sub_source' => $sub_source,
			'created' => $source_date,
			)))
		{
			return $this->add($vector_id, $source_type, $source, $source_date, $sub_source);
		}
		return false;
	}
}

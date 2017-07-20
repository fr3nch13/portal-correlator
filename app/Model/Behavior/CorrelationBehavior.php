<?php

class CorrelationBehavior extends ModelBehavior 
{
	public $settings = [];
	
	public $defaults = [];
	
	public function setup(Model $Model, $config = []) 
	{
		$this->settings[$Model->alias] = array_merge($this->defaults, $config);
	}
	
	public function correlateCorRToFismaSystems(Model $Model, $record = [])
	{
		$Model->modelError = false;
		
		if(!$record)
		{
			$Model->modelError = __('Unknwon %s/%s (1)', __('Category'), __('Report'));
			return false;
		}
		
		$inventoryConditions = $this->correlateCorRToFismaInventories($Model, $record);
		if(!$inventoryConditions)
		{
			return [];
		}
		
		// get a list of systems for the matching inventory
		$fismaSystemIds = $Model->FismaInventory->find('list', [
			'recursive' => -1,
			'conditions' => $inventoryConditions,
			'fields' => ['FismaInventory.fisma_system_id', 'FismaInventory.fisma_system_id']
		]);
		
		return [$Model->alias.'.'.$Model->primaryKey => $fismaSystemIds];
	}
	
	public function correlateCorRToFismaInventories(Model $Model, $record = [])
	{
		$Model->modelError = false;
		
		if(!$record)
		{
			$Model->modelError = __('Unknwon %s/%s (1)', __('Category'), __('Report'));
			return false;
		}
		
		$alias = false;
		if(isset($record['Category']))
		{
			$alias = 'Category';
			$record = $record['Category'];
			$record = $this->fixData($Model, $record);
		}
		elseif(isset($record['Report']))
		{
			$alias = 'Report';
			$record = $record['Report'];
			$record = $this->fixData($Model, $record);
		}
		else
		{
			$Model->modelError = __('Unknwon %s/%s (1)', __('Category'), __('Report'));
			return false;
		}
		
		$conditions = [];
		
		if($record['victim_ip'] and !in_array(strtolower($record['victim_ip']), ['tbd', 'na', 'n/a']))
		{
			$conditions['FismaInventory.ip_address'] = $record['victim_ip'];
			$conditions['FismaInventory.nat_ip_address'] = $record['victim_ip'];
		}
			
		if($record['victim_mac'] and !in_array(strtolower($record['victim_mac']), ['tbd', 'na', 'n/a']))
			$conditions['FismaInventory.mac_address'] = $record['victim_mac'];
			
		if($record['victim_asset_tag'] and !in_array(strtolower($record['victim_asset_tag']), ['tbd', 'na', 'n/a']))
			$conditions['FismaInventory.asset_tag'] = $record['victim_asset_tag'];
		
		if($conditions)
			$conditions = ['OR' => $conditions];
		
		return $conditions;
	}
	
	public function correlateCorRToFismaSystem(Model $Model, $record = [])
	{
		// assume it's an id
		if(!is_array($record))
		{
			if($record = $Model->AdAccount->FismaSystem->read(null, $record))
			{
				$record = $record['FismaSystem'];
			}
			else
			{
				$Model->modelError = __('Unknown %s', __('FISMA System'));
				return false;
			}
		}
		
		// find all of it's direct inventory
		$inventories = $Model->AdAccount->FismaSystem->FismaInventory->find('all', [
			'recursive' => -1,
			'conditions' => ['FismaInventory.fisma_system_id' => $record['id']],
		]);
		
		$ip_addresses = [];
		$mac_addresses = [];
		$asset_tags = [];
		foreach($inventories as $inventory)
		{
			$inventory = $this->fixData($Model, $inventory['FismaInventory']);
			
			if($inventory['ip_address'] and !in_array(strtolower($inventory['ip_address']), ['tbd', 'na', 'n/a']))
				$ip_addresses[$inventory['ip_address']] = $inventory['ip_address'];
			if($inventory['nat_ip_address'] and !in_array(strtolower($inventory['nat_ip_address']), ['tbd', 'na', 'n/a']))
				$ip_addresses[$inventory['nat_ip_address']] = $inventory['nat_ip_address'];
			if($inventory['mac_address'] and !in_array(strtolower($inventory['mac_address']), ['tbd', 'na', 'n/a']))
				$mac_addresses[$inventory['mac_address']] = $inventory['mac_address'];
			if($inventory['asset_tag'] and !in_array(strtolower($inventory['asset_tag']), ['tbd', 'na', 'n/a']))
				$asset_tags[$inventory['asset_tag']] = $inventory['asset_tag'];
		}
		
		$conditions = [];
		if($ip_addresses)
			$conditions[$Model->alias.'.victim_ip'] = $ip_addresses;
		if($mac_addresses)
			$conditions[$Model->alias.'.victim_mac'] = $mac_addresses;
		if($asset_tags)
			$conditions[$Model->alias.'.victim_asset_tag'] = $asset_tags;
		
		if($conditions)
			$conditions = ['OR' => $conditions];
		
		return $conditions;
	}
	
	public function correlateCorRToFismaInventory(Model $Model, $record = [])
	{
		// assume it's an id
		if(!is_array($record))
		{
			if($record = $Model->AdAccount->FismaSystem->FismaInventory->read(null, $record))
			{
				$record = $record['FismaInventory'];
			}
			else
			{
				$Model->modelError = __('Unknown %s', __('FISMA Inventory'));
				return false;
			}
		}
		
		$record = $this->fixData($Model, $record);
		
		$conditions = [];
		
		$ip_addresses = [];
		if($record['ip_address'] and !in_array(strtolower($record['ip_address']), ['tbd', 'na', 'n/a']))
			$ip_addresses[] = $record['ip_address'];
		if($record['nat_ip_address'] and !in_array(strtolower($record['nat_ip_address']), ['tbd', 'na', 'n/a']))
			$ip_addresses[] = $record['nat_ip_address'];
		if($ip_addresses)
		{
			if(count($ip_addresses) == 1) $ip_addresses = array_pop($ip_addresses);
			$conditions[$Model->alias.'.victim_ip'] = $ip_addresses;
		}
			
		if($record['mac_address'] and !in_array(strtolower($record['mac_address']), ['tbd', 'na', 'n/a']))
			$conditions[$Model->alias.'.victim_mac'] = $record['mac_address'];
			
		if($record['asset_tag'] and !in_array(strtolower($record['asset_tag']), ['tbd', 'na', 'n/a']))
			$conditions[$Model->alias.'.victim_asset_tag'] = $record['asset_tag'];
		
		if($conditions)
			$conditions = ['OR' => $conditions];
		
		return $conditions;
	}
	
	public function fixData(Model $Model, $record = [])
	{
		if(isset($record['mac_address']) and $record['mac_address'])
		{
			$record['mac_address'] = strtoupper($record['mac_address']);
			$record['mac_address'] = preg_replace('/[^a-zA-Z0-9]+/',"", $record['mac_address']);
		}
		if(isset($record['victim_mac']) and $record['victim_mac'])
		{
			$record['victim_mac'] = strtoupper($record['victim_mac']);
			$record['victim_mac'] = preg_replace('/[^a-zA-Z0-9]+/',"", $record['victim_mac']);
		}
		return $record;
	}
	
	public function unfilteredScopedResults(Model $Model, $scope = 'org', $scopeArgs = [], $fismaSystemConditions = [])
	{
		$modelClassPlural = Inflector::pluralize($Model->alias);
		
		$resultDefault = [
			'id' => false,
			'name' => false,
			'url' => ['controller' => false, 'action' => 'view', 0 => false],
			'fismaSystemIds' => [],
			'inventory' => [
				'ip_addresses' => [],
				'host_names' => [],
				'mac_addresses' => [],
				'asset_tags' => [],
			],
			$modelClassPlural => [],
		];
		$results = [];
		
		if($Model->name !== 'FismaSystem')
		{
			return $results;
		}
		
		if($scope == 'org')
		{
			$orgs = $Model->OwnerContact->Sac->Branch->Division->Org->find('all', $scopeArgs);
			
			foreach($orgs as $org)
			{
				$i = $org['Org']['id'];
				// no fisma systems
				if(!$fismaSystemIds = $Model->idsForOrg($org['Org']['id'], $fismaSystemConditions))
				{
					continue;
				}
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $org['Org']['id'];
				$results[$i]['name'] = $org['Org']['name'];
				$results[$i]['object'] = $org;
				$results[$i]['url']['controller'] = 'orgs';
				$results[$i]['url'][0] = $org['Org']['id'];
				$results[$i]['fismaSystemIds'] = $fismaSystemIds;
			}
		}
		elseif($scope == 'division')
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('Org'),
			));
			$divisions = $Model->OwnerContact->Sac->Branch->Division->find('all', $scopeArgs);
			foreach($divisions as $division)
			{
				$i = $division['Division']['id'];
				// no fisma systems
				if(!$fismaSystemIds = $Model->idsForDivision($division['Division']['id'], $fismaSystemConditions))
				{
					continue;
				}
				
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $division['Division']['id'];
				$results[$i]['name'] = $division['Division']['name'];
				$results[$i]['object'] = $division;
				$results[$i]['url']['controller'] = 'divisions';
				$results[$i]['url'][0] = $division['Division']['id'];
				$results[$i]['fismaSystemIds'] = $fismaSystemIds;
			}
		}
		elseif($scope == 'branch')
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('Division', 'Division.Org'),
			));
			$branches = $Model->OwnerContact->Sac->Branch->find('all', $scopeArgs);
			foreach($branches as $branch)
			{
				$i = $branch['Branch']['id'];
				// no fisma systems
				if(!$fismaSystemIds = $Model->idsForBranch($branch['Branch']['id'], $fismaSystemConditions))
				{
					continue;
				}
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $branch['Branch']['id'];
				$results[$i]['name'] = $branch['Branch']['name'];
				$results[$i]['object'] = $branch;
				$results[$i]['url']['controller'] = 'branches';
				$results[$i]['url'][0] = $branch['Branch']['id'];
				$results[$i]['fismaSystemIds'] = $fismaSystemIds;
			}
		}
		elseif($scope == 'sac')
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('Branch', 'Branch.Division', 'Branch.Division.Org'),
			));
			$sacs = $Model->OwnerContact->Sac->find('all', $scopeArgs);
			foreach($sacs as $i => $sac)
			{
				$i = $sac['Sac']['id'];
				// no fisma systems
				if(!$fismaSystemIds = $Model->idsForSac($sac['Sac']['id'], $fismaSystemConditions))
				{
					continue;
				}
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $sac['Sac']['id'];
				$results[$i]['name'] = $sac['Sac']['shortname'];
				$results[$i]['object'] = $sac;
				$results[$i]['url']['controller'] = 'sacs';
				$results[$i]['url'][0] = $sac['Sac']['id'];
				$results[$i]['fismaSystemIds'] = $fismaSystemIds;
			}
		}
		elseif(in_array($scope, array('owner', 'ad_account')))
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'),
			));
			$owners = $Model->OwnerContact->find('all', $scopeArgs);
			foreach($owners as $owner)
			{
				$i = $owner['OwnerContact']['id'];
				$owner['AdAccount'] = $owner['OwnerContact'];
				
				// no fisma systems
				if(!$fismaSystemIds = $Model->idsForOwnerContact($owner['OwnerContact']['id'], $fismaSystemConditions))
				{
					continue;
				}
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $owner['OwnerContact']['id'];
				$results[$i]['name'] = $owner['OwnerContact']['name'];
				$results[$i]['object'] = $owner;
				$results[$i]['url']['controller'] = 'ad_accounts';
				$results[$i]['url'][0] = $owner['OwnerContact']['id'];
				$results[$i]['fismaSystemIds'] = $fismaSystemIds;
			}
		}
		elseif(in_array($scope, array('system', 'fisma_system')))
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('OwnerContact', 'OwnerContact.Sac', 'OwnerContact.Sac.Branch', 'OwnerContact.Sac.Branch.Division', 'OwnerContact.Sac.Branch.Division.Org'),
			));
			$fismaSystems = $Model->find('all', $scopeArgs);
			foreach($fismaSystems as $fismaSystem)
			{
				$i = $fismaSystem['FismaSystem']['id'];
				$fismaSystem['AdAccount'] = $fismaSystem['OwnerContact'];
				
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $fismaSystem['FismaSystem']['id'];
				$results[$i]['name'] = $fismaSystem['FismaSystem']['name'];
				$results[$i]['object'] = $fismaSystem;
				$results[$i]['url']['controller'] = 'fisma_systems';
				$results[$i]['url'][0] = $fismaSystem['FismaSystem']['id'];
				$results[$i]['fismaSystemIds'] = array($fismaSystem['FismaSystem']['id'] => $fismaSystem['FismaSystem']['id']);
			}
		}
		elseif($scope == 'crm')
		{
			$scopeArgs = array_merge($scopeArgs, array(
				'contain' => array('OwnerContact', 'OwnerContact.Sac', 'OwnerContact.Sac.Branch', 'OwnerContact.Sac.Branch.Division', 'OwnerContact.Sac.Branch.Division.Org'),
			));
			$fismaSystems = $Model->find('all', $scopeArgs);
			foreach($fismaSystems as $fismaSystem)
			{
				$i = $fismaSystem['FismaSystem']['id'];
				$fismaSystem['AdAccount'] = $fismaSystem['OwnerContact'];
				
				$results[$i] = $resultDefault;
				$results[$i]['id'] = $fismaSystem['FismaSystem']['id'];
				$results[$i]['name'] = $fismaSystem['FismaSystem']['name'];
				$results[$i]['object'] = $fismaSystem;
				$results[$i]['url']['controller'] = 'fisma_systems';
				$results[$i]['url'][0] = $fismaSystem['FismaSystem']['id'];
				$results[$i]['fismaSystemIds'] = array($fismaSystem['FismaSystem']['id'] => $fismaSystem['FismaSystem']['id']);
			}
		}
		return $results;
	}
	
	public function scopedResults(Model $Model, $scope = 'org', $conditions = [], $scopeArgs = [], $returnConditions = false, $fismaSystemConditions = [])
	{
		if(!in_array($Model->name, ['Category', 'Report']))
		{
			return [];
		}
		
		$modelClassPlural = Inflector::pluralize($Model->alias);
		
		$results = $this->unfilteredScopedResults($Model->AdAccount->FismaSystem, $scope, $scopeArgs, $fismaSystemConditions);
		
		$returningConditions = array('OR' => []);
		
		foreach($results as $resultId => $result)
		{
			$results[$resultId]['inventory']['ip_addresses'] = $Model->AdAccount->FismaSystem->getRelatedIpAddresses($result['fismaSystemIds']);
			$results[$resultId]['inventory']['mac_addresses'] = $Model->AdAccount->FismaSystem->getRelatedMacAddresses($result['fismaSystemIds']);
			$results[$resultId]['inventory']['asset_tags'] = $Model->AdAccount->FismaSystem->getRelatedAssetTags($result['fismaSystemIds']);
				
			if(!$results[$resultId]['inventory']['ip_addresses'] and !$results[$resultId]['inventory']['mac_addresses'] and !$results[$resultId]['inventory']['asset_tags'])
			{
				unset($results[$resultId]);
				continue;
			}
			
			$resultConditions = $conditions;
			if(!isset($resultConditions['OR']))
				$resultConditions['OR'] = [];
			
			$resultConditions['OR'] = [
				$Model->alias.'.fisma_system_id' => $result['fismaSystemIds'],
				array($Model->alias.'.fisma_system_id' => 0)
			];
			
			if($results[$resultId]['inventory']['ip_addresses'])
			{
				$resultConditions['OR'][0]['OR'][] = array(
					$Model->alias.'.victim_ip !=' => '',
					$Model->alias.'.victim_ip' => $results[$resultId]['inventory']['ip_addresses'],
				);
				if($returnConditions)
				{
					if(!isset($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_ip']))
						$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_ip'] = [];
					$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_ip'] = array_merge($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_ip'], $results[$resultId]['inventory']['ip_addresses']);
				}
			}
			if($results[$resultId]['inventory']['mac_addresses'])
			{
				$resultConditions['OR'][0]['OR'][] = array(
					$Model->alias.'.victim_mac !=' => '',
					$Model->alias.'.victim_mac' => $results[$resultId]['inventory']['mac_addresses'],
				);
				if($returnConditions)
				{
					if(!isset($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_mac']))
						$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_mac'] = [];
					$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_mac'] = array_merge($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_mac'], $results[$resultId]['inventory']['mac_addresses']);
				}
			}
			if($results[$resultId]['inventory']['asset_tags'])
			{
				$resultConditions['OR'][0]['OR'][] = array(
					$Model->alias.'.victim_asset_tag !=' => '',
					$Model->alias.'.victim_asset_tag' => $results[$resultId]['inventory']['asset_tags'],
				);
				if($returnConditions)
				{
					if(!isset($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_asset_tag']))
						$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_asset_tag'] = [];
					$returningConditions['OR'][0]['OR'][$Model->alias.'.victim_asset_tag'] = array_merge($returningConditions['OR'][0]['OR'][$Model->alias.'.victim_asset_tag'], $results[$resultId]['inventory']['asset_tags']);
				}
			}
			
			if($returnConditions)
				continue;
			
			$myResults = $Model->find('all', array(
				'conditions' => $resultConditions,
			));
			
			if(!$myResults)
			{
				unset($results[$resultId]);
				continue;
			}
			
			$correlated = array(
				'ip_addresses' => [],
				'host_names' => [],
				'mac_addresses' => [],
				'asset_tags' => [],
			);
			$_myResults = [];
			foreach($myResults as $myI => $myResult)
			{
				$myResult_id = $myResult[$Model->alias]['id'];
				
				$_myResults[$myResult_id] = $myResult;
				
				if(isset($myResult[$Model->alias]['fisma_system_id']) and $myResult[$Model->alias]['fisma_system_id'])
				{
					// make sure it's only in here
					if(!in_array($myResult[$Model->alias]['fisma_system_id'], $result['fismaSystemIds']))
					{
						unset($myResults[$myI]);
						continue;
					}
				}
				
				// try to find where it is associated
				if($myResult[$Model->alias]['victim_ip'])
				{
					if(isset($results[$resultId]['inventory']['ip_addresses'][$myResult[$Model->alias]['victim_ip']]))
						$correlated['ip_addresses'][$myResult[$Model->alias]['victim_ip']] = $myResult[$Model->alias]['victim_ip'];
				}
				if($myResult[$Model->alias]['victim_mac'])
				{
					if(isset($results[$resultId]['inventory']['mac_addresses'][$myResult[$Model->alias]['victim_mac']]))
						$correlated['mac_addresses'][$myResult[$Model->alias]['victim_mac']] = $myResult[$Model->alias]['victim_mac'];
				}
				if($myResult[$Model->alias]['victim_asset_tag'])
				{
					if(isset($results[$resultId]['inventory']['asset_tags'][$myResult[$Model->alias]['victim_asset_tag']]))
						$correlated['asset_tags'][$myResult[$Model->alias]['victim_asset_tag']] = $myResult[$Model->alias]['victim_asset_tag'];
				}
			}
			$myResults = $_myResults;
			$results[$resultId][$modelClassPlural] = $myResults;
			$results[$resultId]['inventory'] = $correlated;
		}
		
		if($returnConditions)
			return $returningConditions;
		
		return $results;
	}
	
	public function attachFismaSystem(Model $Model, $result = [])
	{
		if(!isset($result[$Model->alias]))
			return $result;
		
		$result['FismaInventories'] = [];
		$result['overridden'] = false;
		$resultConditions = array('OR' => []);
		if(isset($result[$Model->alias]['fisma_system_id']) and $result[$Model->alias]['fisma_system_id'])
		{
			$resultConditions['FismaInventory.fisma_system_id'] = $result[$Model->alias]['fisma_system_id'];
			$result['overridden'] = true;
		}
		
		if(isset($result[$Model->alias]['victim_ip']) and $result[$Model->alias]['victim_ip'])
			$resultConditions['OR'][] = array(
				'FismaInventory.ip_address !=' => '',
				'FismaInventory.ip_address' => $result[$Model->alias]['victim_ip'],
			);
		if(isset($result[$Model->alias]['victim_mac']) and $result[$Model->alias]['victim_mac'])
			$resultConditions['OR'][] = array(
				'FismaInventory.mac_address !=' => '',
				'FismaInventory.mac_address' => $result[$Model->alias]['victim_mac'],
			);
		if(isset($result[$Model->alias]['victim_asset_tag']) and $result[$Model->alias]['victim_asset_tag'])
			$resultConditions['OR'][] = array(
				'FismaInventory.asset_tag !=' => '',
				'FismaInventory.asset_tag' => $result[$Model->alias]['victim_asset_tag'],
			);
				
		if($resultConditions['OR'])
		{
			$result['FismaInventories'] = $Model->AdAccount->FismaSystem->FismaInventory->find('all', array(
				'contain' => array('FismaSystem', 'FismaSystem.OwnerContact', 'FismaSystem.OwnerContact.Sac', 'FismaSystem.OwnerContact.Sac.Branch', 'FismaSystem.OwnerContact.Sac.Branch.Division', 'FismaSystem.OwnerContact.Sac.Branch.Division.Org'),
				'conditions' => $resultConditions,
			));
		}
		return $result;
	}
}
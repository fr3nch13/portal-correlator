<?php
App::uses('AppModel', 'Model');
App::uses('ContactsDivision', 'Contacts.Model');

class Division extends ContactsDivision 
{
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return array();
		
		if(!isset($this->snapshotStats))
			return array();
		
		extract($this->snapshotStats);
		
		if(!isset($timestamps))
			return array();
		
		// get the list of divisions
		$this->virtualFields = false;
		$divisions = $this->find('all');
		
		foreach($divisions as $i => $division)
		{
			$divisions[$i]['counts'] = $this->getStatsCounts($division['Division']['id'], array());
		}
		// get the stats for each division
		return $entities;
	}
	
	public function getStatsCounts($division_id = false, $runningStats = array(), $contactType = 'owner')
	{
		$stats = array(
			'divisions' => 0,
			'ad_accounts' => 0,
			'parents' => 0,
			'children' => 0,
			'fisma_systems' => 0,
			'pii_count' => 0,
			'ongoing_auth_na' => 0,
			'ongoing_auth_no' => 0,
			'ongoing_auth_yes' => 0,
			'fisma_reportable_na' => 0,
			'fisma_reportable_no' => 0,
			'fisma_reportable_yes' => 0,
		);
		
		$fismaSystemGssStatuses = $this->AdAccount->OwnerContact->FismaSystemGssStatus->find('list');
		$fismaSystemFipsRatings = $this->AdAccount->OwnerContact->FismaSystemFipsRating->find('list');
		$fismaSystemRiskAssessments = $this->AdAccount->OwnerContact->FismaSystemRiskAssessment->find('list');
		$fismaSystemThreatAssessments = $this->AdAccount->OwnerContact->FismaSystemThreatAssessment->find('list');
		
		$fismaSystemSensitivityCategories = $this->AdAccount->OwnerContact->FismaSystemSensitivityCategory->find('list');
		$fismaSystemSensitivityRatings = $this->AdAccount->OwnerContact->FismaSystemSensitivityRating->find('list');
		$fismaSystemTypes = $this->AdAccount->OwnerContact->FismaSystemType->find('list');
		$fismaSystemComTotals = $this->AdAccount->OwnerContact->FismaSystemComTotal->find('list');
		$fismaSystemImpacts = $this->AdAccount->OwnerContact->FismaSystemImpact->find('list');
		$fismaSystemUniquenesses = $this->AdAccount->OwnerContact->FismaSystemUniqueness->find('list');
		$fismaSystemAmounts = $this->AdAccount->OwnerContact->FismaSystemAmount->find('list');
		$fismaSystemDependencies = $this->AdAccount->OwnerContact->FismaSystemDependency->find('list');
		
		foreach($fismaSystemGssStatuses as $id => $name)
			$stats['fismaSystemGssStatus_'. $id] = 0;
		foreach($fismaSystemFipsRatings as $id => $name)
			$stats['fismaSystemFipsRating_'. $id] = 0;
		foreach($fismaSystemRiskAssessments as $id => $name)
			$stats['fismaSystemRiskAssessment_'. $id] = 0;
		foreach($fismaSystemThreatAssessments as $id => $name)
			$stats['fismaSystemThreatAssessment_'. $id] = 0;
			
		foreach($fismaSystemSensitivityCategories as $id => $name)
			$stats['fismaSystemSensitivityCategory_'. $id] = 0;
		foreach($fismaSystemSensitivityRatings as $id => $name)
			$stats['fismaSystemSensitivityRating_'. $id] = 0;
		foreach($fismaSystemTypes as $id => $name)
			$stats['fismaSystemType_'. $id] = 0;
		foreach($fismaSystemComTotals as $id => $name)
			$stats['fismaSystemComTotal_'. $id] = 0;
		foreach($fismaSystemImpacts as $id => $name)
			$stats['fismaSystemImpact_'. $id] = 0;
		foreach($fismaSystemUniquenesses as $id => $name)
			$stats['fismaSystemUniqueness_'. $id] = 0;
		foreach($fismaSystemAmounts as $id => $name)
			$stats['fismaSystemAmount_'. $id] = 0;
		foreach($fismaSystemDependencies as $id => $name)
			$stats['fismaSystemDependency_'. $id] = 0;
			
		$stats = array_merge($stats, $runningStats);
		$stats['divisions']++;
		
		$ad_accounts = $this->AdAccount->find('list', array(
			'conditions' => array(
				'AdAccount.division_id' => $division_id,
			),
		));
		
		$stats['ad_accounts'] = ($stats['ad_accounts'] + count($ad_accounts));
		
		if(!$field = $this->AdAccount->contactTypeField($contactType))
		{
			return $stats;
		}
		
		$fisma_systems = $this->AdAccount->OwnerContact->find('all', array(
			'conditions' => array(
				'OwnerContact.'.$field => array_keys($ad_accounts),
			),
		));
		$stats['fisma_systems'] = ($stats['fisma_systems'] + count($fisma_systems));
		
		foreach($fisma_systems as $i => $fisma_system)
		{
			if($fisma_system['OwnerContact']['parent_id'])
				$stats['children']++;
			else
				$stats['parents']++;
			
			$stats['pii_count'] = ($stats['pii_count'] + $fisma_system['OwnerContact']['pii_count']);
			
			if($fisma_system['OwnerContact']['ongoing_auth'] == 0)
				$stats['ongoing_auth_na']++;
			elseif($fisma_system['OwnerContact']['ongoing_auth'] == 1)
				$stats['ongoing_auth_no']++;
			elseif($fisma_system['OwnerContact']['ongoing_auth'] == 2)
				$stats['ongoing_auth_yes']++;
			
			if($fisma_system['OwnerContact']['fisma_reportable'] == 0)
				$stats['fisma_reportable_na']++;
			elseif($fisma_system['OwnerContact']['fisma_reportable'] == 1)
				$stats['fisma_reportable_no']++;
			elseif($fisma_system['OwnerContact']['fisma_reportable'] == 2)
				$stats['fisma_reportable_yes']++;
			
			foreach($fismaSystemGssStatuses as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_gss_status_id'])
					$stats['fismaSystemGssStatus_'. $id]++;
			
			foreach($fismaSystemFipsRatings as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_fips_rating_id'])
					$stats['fismaSystemFipsRating_'. $id]++;
			
			foreach($fismaSystemRiskAssessments as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_risk_assessment_id'])
					$stats['fismaSystemRiskAssessment_'. $id]++;
			
			foreach($fismaSystemThreatAssessments as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_threat_assessment_id'])
					$stats['fismaSystemThreatAssessment_'. $id]++;
//
			foreach($fismaSystemSensitivityCategories as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_sensitivity_category_id'])
					$stats['fismaSystemSensitivityCategory_'. $id]++;
			
			foreach($fismaSystemSensitivityRatings as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_sensitivity_rating_id'])
					$stats['fismaSystemSensitivityRating_'. $id]++;
			
			foreach($fismaSystemTypes as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_type_id'])
					$stats['fismaSystemType_'. $id]++;
			
			foreach($fismaSystemComTotals as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_com_total_id'])
					$stats['fismaSystemComTotal_'. $id]++;
			
			foreach($fismaSystemImpacts as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_impact_id'])
					$stats['fismaSystemImpact_'. $id]++;
			
			foreach($fismaSystemUniquenesses as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_uniqueness_id'])
					$stats['fismaSystemUniqueness_'. $id]++;
			
			foreach($fismaSystemAmounts as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_amount_id'])
					$stats['fismaSystemAmount_'. $id]++;
			
			foreach($fismaSystemDependencies as $id => $name)
				if($id == $fisma_system['OwnerContact']['fisma_system_dependency_id'])
					$stats['fismaSystemDependency_'. $id]++;
		}
		
		return $stats;
	}
}

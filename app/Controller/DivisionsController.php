<?php

App::uses('ContactsDivisionsController', 'Contacts.Controller');

class DivisionsController extends ContactsDivisionsController
{
	public function db_block_overview()
	{
		$divisions = $this->Division->find('all');
		$this->set(compact('divisions'));
	}
	
	public function index($contactType = false)
	{	
		parent::index();
		
		$divisions = $this->viewVars['divisions'];
		
		if($contactType and $field = $this->Division->AdAccount->contactTypeField($contactType))
		{
			foreach($divisions as $i => $division)
			{
				$divisions[$i]['counts'] = $this->Division->getStatsCounts($division['Division']['id'], array(), $contactType);
			}
			$this->set('divisions', $divisions);
		
			$fismaSystemGssStatuses = $this->Division->AdAccount->OwnerContact->FismaSystemGssStatus->find('list');
			$fismaSystemFipsRatings = $this->Division->AdAccount->OwnerContact->FismaSystemFipsRating->find('list');
			$fismaSystemRiskAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemRiskAssessment->find('list');
			$fismaSystemThreatAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemThreatAssessment->find('list');
			$this->set(compact(array(
				'fismaSystemGssStatuses', 'fismaSystemFipsRatings', 'fismaSystemRiskAssessments', 'fismaSystemThreatAssessments'
			)));
		
			$fismaSystemSensitivityCategories = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityCategory->find('list');
			$fismaSystemSensitivityRatings = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityRating->find('list');
			$fismaSystemTypes = $this->Division->AdAccount->OwnerContact->FismaSystemType->find('list');
			$fismaSystemComTotals = $this->Division->AdAccount->OwnerContact->FismaSystemComTotal->find('list');
			$fismaSystemImpacts = $this->Division->AdAccount->OwnerContact->FismaSystemImpact->find('list');
			$fismaSystemUniquenesses = $this->Division->AdAccount->OwnerContact->FismaSystemUniqueness->find('list');
			$fismaSystemAmounts = $this->Division->AdAccount->OwnerContact->FismaSystemAmount->find('list');
			$fismaSystemDependencies = $this->Division->AdAccount->OwnerContact->FismaSystemDependency->find('list');
			$this->set(compact(array(
				'fismaSystemSensitivityCategories', 'fismaSystemSensitivityRatings', 'fismaSystemTypes',
				'fismaSystemComTotals', 'fismaSystemImpacts', 'fismaSystemUniquenesses',
				'fismaSystemAmounts', 'fismaSystemDependencies',
			)));
			
			if($contactType == 'owner')
				$contactType = __('System Owners');
			elseif($contactType == 'business')
				$contactType = __('Business Owners');
			elseif($contactType == 'tech')
				$contactType = __('Tech Contacts');
			else
				$contactType = ucfirst(strtolower($contactType));
		}
		$this->set('contactType', $contactType);
		
	}
	
	public function byorg($contactType = false, $db_block = false)
	{
		$this->conditions = array();
		
		parent::byorg();
		
		$divisions = $this->viewVars['divisions'];
		
		$orgs = array();
		foreach($divisions as $i => $division)
		{
			$org = $division['Division']['org'];
			
			if(!isset($orgs[$org]))
				$orgs[$org] = array(
					'name' => $org,
					'counts' => array(),
				);
			if($contactType and $field = $this->Division->AdAccount->contactTypeField($contactType))
			{
				$orgs[$org]['counts'] = $this->Division->getStatsCounts($division['Division']['id'], $orgs[$org]['counts'], $contactType);
			}
		}
		sort($orgs);
		
		$this->set('orgs', $orgs);
		$this->set('db_block', $db_block);
		
		if($contactType and $field = $this->Division->AdAccount->contactTypeField($contactType))
		{
			if($contactType == 'owner')
				$contactType = __('System Owners');
			elseif($contactType == 'business')
				$contactType = __('Business Owners');
			elseif($contactType == 'tech')
				$contactType = __('Tech Contacts');
			else
				$contactType = ucfirst(strtolower($contactType));
		
			$fismaSystemGssStatuses = $this->Division->AdAccount->OwnerContact->FismaSystemGssStatus->find('list');
			$fismaSystemFipsRatings = $this->Division->AdAccount->OwnerContact->FismaSystemFipsRating->find('list');
			$fismaSystemRiskAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemRiskAssessment->find('list');
			$fismaSystemThreatAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemThreatAssessment->find('list');
			$this->set(compact(array(
				'fismaSystemGssStatuses', 'fismaSystemFipsRatings', 'fismaSystemRiskAssessments', 'fismaSystemThreatAssessments'
			)));
		
			$fismaSystemSensitivityCategories = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityCategory->find('list');
			$fismaSystemSensitivityRatings = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityRating->find('list');
			$fismaSystemTypes = $this->Division->AdAccount->OwnerContact->FismaSystemType->find('list');
			$fismaSystemComTotals = $this->Division->AdAccount->OwnerContact->FismaSystemComTotal->find('list');
			$fismaSystemImpacts = $this->Division->AdAccount->OwnerContact->FismaSystemImpact->find('list');
			$fismaSystemUniquenesses = $this->Division->AdAccount->OwnerContact->FismaSystemUniqueness->find('list');
			$fismaSystemAmounts = $this->Division->AdAccount->OwnerContact->FismaSystemAmount->find('list');
			$fismaSystemDependencies = $this->Division->AdAccount->OwnerContact->FismaSystemDependency->find('list');
			$this->set(compact(array(
				'fismaSystemSensitivityCategories', 'fismaSystemSensitivityRatings', 'fismaSystemTypes',
				'fismaSystemComTotals', 'fismaSystemImpacts', 'fismaSystemUniquenesses',
				'fismaSystemAmounts', 'fismaSystemDependencies',
			)));
		}
		$this->set('contactType', $contactType);
	}
	
	public function byshortname($contactType = false)
	{	
		$this->conditions = array();
		
		parent::byshortname();
		
		$divisions = $this->viewVars['divisions'];
		
		$count = 0;
		
		$shortnames = array();
		foreach($divisions as $i => $division)
		{
			$shortname = $division['Division']['shortname'];
			
			if(!isset($shortnames[$shortname]))
			{
				$shortnames[$shortname] = array(
					'name' => $shortname,
					'org' => array(),
					'sac' => array(),
					'director' => array(),
					'email' => array(),
					'counts' => array(),
				);
			}
			if($contactType and $field = $this->Division->AdAccount->contactTypeField($contactType))
			{
				$shortnames[$shortname]['counts'] = $this->Division->getStatsCounts($division['Division']['id'], $shortnames[$shortname]['counts'], $contactType);
			}
			
			// add the extra stuff
			$director_id = Inflector::slug(strtolower($division['Division']['email']));
			
			$shortnames[$shortname]['directors'][$director_id]['org'] = $division['Division']['org'];
			$shortnames[$shortname]['directors'][$director_id]['director'] =  $division['Division']['director'];
			$shortnames[$shortname]['directors'][$director_id]['email'] = $division['Division']['email'];
		}
		sort($shortnames);
		
		$this->set('shortnames', $shortnames);
		
		if($contactType and $field = $this->Division->AdAccount->contactTypeField($contactType))
		{
			if($contactType == 'owner')
				$contactType = __('System Owners');
			elseif($contactType == 'business')
				$contactType = __('Business Owners');
			elseif($contactType == 'tech')
				$contactType = __('Tech Contacts');
			else
				$contactType = ucfirst(strtolower($contactType));
		
			$fismaSystemGssStatuses = $this->Division->AdAccount->OwnerContact->FismaSystemGssStatus->find('list');
			$fismaSystemFipsRatings = $this->Division->AdAccount->OwnerContact->FismaSystemFipsRating->find('list');
			$fismaSystemRiskAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemRiskAssessment->find('list');
			$fismaSystemThreatAssessments = $this->Division->AdAccount->OwnerContact->FismaSystemThreatAssessment->find('list');
			$this->set(compact(array(
				'fismaSystemGssStatuses', 'fismaSystemFipsRatings', 'fismaSystemRiskAssessments', 'fismaSystemThreatAssessments'
			)));
		
			$fismaSystemSensitivityCategories = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityCategory->find('list');
			$fismaSystemSensitivityRatings = $this->Division->AdAccount->OwnerContact->FismaSystemSensitivityRating->find('list');
			$fismaSystemTypes = $this->Division->AdAccount->OwnerContact->FismaSystemType->find('list');
			$fismaSystemComTotals = $this->Division->AdAccount->OwnerContact->FismaSystemComTotal->find('list');
			$fismaSystemImpacts = $this->Division->AdAccount->OwnerContact->FismaSystemImpact->find('list');
			$fismaSystemUniquenesses = $this->Division->AdAccount->OwnerContact->FismaSystemUniqueness->find('list');
			$fismaSystemAmounts = $this->Division->AdAccount->OwnerContact->FismaSystemAmount->find('list');
			$fismaSystemDependencies = $this->Division->AdAccount->OwnerContact->FismaSystemDependency->find('list');
			$this->set(compact(array(
				'fismaSystemSensitivityCategories', 'fismaSystemSensitivityRatings', 'fismaSystemTypes',
				'fismaSystemComTotals', 'fismaSystemImpacts', 'fismaSystemUniquenesses',
				'fismaSystemAmounts', 'fismaSystemDependencies',
			)));
		}
		$this->set('contactType', $contactType);
	}
}

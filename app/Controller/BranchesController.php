<?php

App::uses('ContactsBranchesController', 'Contacts.Controller');

class BranchesController extends ContactsBranchesController
{
	public function db_block_overview()
	{
		$branches = $this->Branch->find('all');
		$this->set(compact('branches'));
	}
	
	public function index($contactType = false)
	{	
		parent::index();
		
		$branches = $this->viewVars['branches'];
		
		if($contactType and $field = $this->Branch->Sac->AdAccount->contactTypeField($contactType))
		{
			foreach($branches as $i => $branch)
			{
				$branches[$i]['counts'] = $this->Branch->getStatsCounts($branch['Branch']['id'], array(), $contactType);
			}
			$this->set('branches', $branches);
		
			$fismaSystemGssStatuses = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemGssStatus->find('list');
			$fismaSystemFipsRatings = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemFipsRating->find('list');
			$fismaSystemRiskAssessments = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemRiskAssessment->find('list');
			$fismaSystemThreatAssessments = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemThreatAssessment->find('list');
			$this->set(compact(array(
				'fismaSystemGssStatuses', 'fismaSystemFipsRatings', 'fismaSystemRiskAssessments', 'fismaSystemThreatAssessments'
			)));
		
			$fismaSystemSensitivityCategories = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemSensitivityCategory->find('list');
			$fismaSystemSensitivityRatings = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemSensitivityRating->find('list');
			$fismaSystemTypes = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemType->find('list');
			$fismaSystemComTotals = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemComTotal->find('list');
			$fismaSystemImpacts = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemImpact->find('list');
			$fismaSystemUniquenesses = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemUniqueness->find('list');
			$fismaSystemAmounts = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemAmount->find('list');
			$fismaSystemDependencies = $this->Branch->Sac->AdAccount->OwnerContact->FismaSystemDependency->find('list');
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
}

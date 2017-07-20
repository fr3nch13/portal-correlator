<?php
App::uses('AppModel', 'Model');

class AppVectorParent extends AppModel 
{
	public $useTable = false;
	
	public $vectorFields = array('adaccount', 'sac_id', 'victim_ip', 'victim_mac', 'victim_asset_tag');
	public $beforeSaveObject = array();
	public $saveDiff = array();
	
	// determine if an email should be sent when an editor/contributor changes something
	public $editorEmail = false;
	
	public $isTemp = false;
	public $aliasPlural = false;
	public $uploadModel = 'Upload';
	public $editorModel = false;
	public $detailsModel = false;
	public $vectorsField = 'vectors';
	public $vectorField = 'vector';
	public $vectorModel = 'Vector';
	public $xrefModel = false;
	public $xrefField = false;
	public $xrefVectorField = 'vector_id';
	
	public function __construct($id = false, $table = null, $ds = null) 
	{
		parent::__construct();
		
		$this->xrefField = Inflector::underscore($this->alias). '_id';
		$this->aliasPlural = Inflector::pluralize($this->alias);
		$this->editorModel = $this->aliasPlural.'Editor';
		$this->detailsModel = $this->aliasPlural.'Detail';
		$this->xrefModel = $this->aliasPlural.'Vector';
		
		// figure out if we're Temp or Live
		$this->isTemp = false;
		if(substr($this->alias, 0, 4) == 'Temp')
		{
			$this->isTemp = true;
			$this->vectorsField = 'temp_vectors';
			$this->vectorField = 'temp_vector';
			$this->vectorModel = 'TempVector';
			$this->uploadModel = 'TempUpload';
			$this->xrefVectorField = 'temp_'. $this->xrefVectorField;
		}
	}
	
	public function beforeSave($options = array())
	{
		// copy some information over from the report to the upload
		if(isset($this->data[$this->uploadModel]))
		{
			if(!isset($this->data[$this->uploadModel]['tmp_name']) or !$this->data[$this->uploadModel]['tmp_name'])
			{
				unset($this->data[$this->uploadModel]);
			}
			else
			{
				$this->data[$this->uploadModel][$this->xrefField] = 0;
				if(isset($this->data[$this->alias]['id'])) $this->data[$this->uploadModel][$this->xrefField] = $this->data[$this->alias]['id'];
				
				$this->data[$this->uploadModel]['user_id'] = 0;
				if(isset($this->data[$this->alias]['user_id'])) $this->data[$this->uploadModel]['user_id'] = $this->data[$this->alias]['user_id'];
				
				$this->data[$this->uploadModel]['org_group_id'] = 0;
				if(isset($this->data[$this->alias]['org_group_id'])) $this->data[$this->alias]['org_group_id'] = $this->data[$this->alias]['org_group_id'];
				
				$this->data[$this->uploadModel]['public'] = 0;
				if(isset($this->data[$this->alias]['public'])) $this->data[$this->uploadModel]['public'] = $this->data[$this->alias]['public'];
			}
		}
		
		if(isset($this->data[$this->alias]['id']))
		{
			$saveData = $this->data;
			$this->contain(array('AdAccount'));
			$this->beforeSaveObject = $this->read(null, $this->data[$this->alias]['id']);
			$this->data = $saveData;
		}
		else
		{
			$this->beforeSaveObject = array($this->alias => array());
		}
		
		// for temp and regular reports/categories
		$vectorsFromAssessments = array();
		if(isset($this->data[$this->alias]['adaccount']))
		{
			if($this->data[$this->alias]['adaccount'])
			{
				$ad_account_id = $this->AdAccount->checkAdd($this->data[$this->alias]['adaccount']);
				$this->data[$this->alias]['ad_account_id'] = $ad_account_id;
			}
			else
			{
				$this->data[$this->alias]['ad_account_id'] = 0;
			}
			$vectorsFromAssessments[$this->data[$this->alias]['adaccount']] = $this->data[$this->alias]['adaccount'];
		}
		if(isset($this->data[$this->alias]['sac_id']) and $this->data[$this->alias]['sac_id'])
		{
			$sac = $this->AdAccount->Sac->typeFormList(array(
				'conditions' => array('Sac.id' => $this->data[$this->alias]['sac_id'])
			));
			if(isset($sac[$this->data[$this->alias]['sac_id']]))
			{
				$sac = $sac[$this->data[$this->alias]['sac_id']];
				$sac = explode('-', $sac);
				$sac = trim($sac[1]);
				$assessmentOffice_id = $this->AssessmentOffice->checkAdd($sac);
				$this->data[$this->alias]['assessment_office_id'] = $assessmentOffice_id;
			}
			$vectorsFromAssessments[$sac] = $sac;
		}
		if(isset($this->data[$this->alias]['victim_ip']) and $this->data[$this->alias]['victim_ip'])
		{
			$vectorsFromAssessments[$this->data[$this->alias]['victim_ip']] = $this->data[$this->alias]['victim_ip'];
		}
		if(isset($this->data[$this->alias]['victim_mac']) and $this->data[$this->alias]['victim_mac'])
		{
			$this->data[$this->alias]['victim_mac'] = $this->EX_fixMacAddress($this->data[$this->alias]['victim_mac']);
			$vectorsFromAssessments[$this->data[$this->alias]['victim_mac']] = $this->data[$this->alias]['victim_mac'];
		}
		if(isset($this->data[$this->alias]['victim_asset_tag']) and $this->data[$this->alias]['victim_asset_tag'])
		{
			$this->data[$this->alias]['victim_asset_tag'] = $this->EX_fixAssetTag($this->data[$this->alias]['victim_asset_tag']);
			$vectorsFromAssessments[$this->data[$this->alias]['victim_asset_tag']] = $this->data[$this->alias]['victim_asset_tag'];
		}
		
		$this->saveDiff = $this->getChanges($this->beforeSaveObject[$this->alias], $this->data[$this->alias], $this->vectorFields);
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// check and format the editors and contributors
		if(isset($this->data[$this->alias]['editors_editors']) or isset($this->data[$this->alias]['editors_contributors']))
		{
			$this->{$this->editorModel}->updateLists($this->id, $this->data[$this->alias]);
		}
		
		$this->notifyOwner();
		
		// Save the vectors
		$vectors = array();
		
		if(isset($this->data[$this->alias][$this->vectorsField]) and $this->data[$this->alias][$this->vectorsField])
		{
			// clean up the vectors
			$this->data[$this->alias][$this->vectorsField] = $this->cleanString($this->data[$this->alias][$this->vectorsField]);
			foreach(explode("\n", $this->data[$this->alias][$this->vectorsField]) as $vector)
			{
				$vector = trim($vector);
				if($vector)
					$vectors[$vector] = $vector; // format and make unique
			}
		}
		
		// find all of the vectors from the description
		if(isset($this->data[$this->alias]['scan_desc']) and $this->data[$this->alias]['scan_desc'] and isset($this->data[$this->detailsModel]['desc']))
		{
			$all_vectors = $this->extractItems($this->data[$this->detailsModel]['desc']);

			// clean them up and format them for a saveMany()
			foreach($all_vectors as $type => $_vectors)
			{
				foreach($_vectors as $i => $vector)
				{
					$vector = trim($vector);
					if($vector)
						$vectors[$vector] = $vector; // format and make unique
				}
			}
		}
		
		// before adding new vectors from descriptions, etc.
		$this->handleSaveDiff($this->id);
		
		// save the temporary vectors
		if($vectors)
		{
			sort($vectors);
			
			$data = array(
				$this->xrefModel => array(
					$this->vectorsField => $vectors,
					$this->xrefField => $this->id,
				),
			);
		
			$this->{$this->xrefModel}->add($data);
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		// checking to make sure this vector has a related detail, only if it's requested
		foreach($results as $i => $result)
		{
			if(isset($this->AdAccount))
			{
				if(isset($results[$i][$this->AdAccount->alias]['username']))
					$results[$i][$this->alias]['adaccount'] = $results[$i][$this->AdAccount->alias]['username'];
				else
					$results[$i][$this->alias]['adaccount'] = false;
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function saveEditor($data = null, $user = array(), $options = array())
	{
	/*
	 * Wrapper around saveAssociated to modify the info foe what an editor can do
	 */
		if(empty($data))
		{
			$data = $this->data;
		}
		if(isset($user['id']))
		{
			$data[$this->alias]['editor_user_id'] = $user['id'];
		}
		
		if($this->id)
		{
			$this->contain('User');
			$report = $this->read(null, $this->id);
			
			$this->editorEmail = array(
				'subject' => __('Your %s has been updated by an Editor', __($this->alias)),
				'to' => $report['User']['email'],
				'editor' => $user,
				'report' => $report,
				'data' => $data,
			);
		}
 		
 		return $this->saveAssociated($data, $options);
	}
	
	public function saveContributor($data = null, $user = array(), $options = array())
	{
		if(empty($data))
		{
			$data = $this->data;
		}
		if(isset($user['id']))
		{
			$data[$this->alias]['contributor_user_id'] = $user['id'];
		}
		
		$original = false;
		if($this->id)
		{
			$this->contain($this->detailsModel, 'Tag', 'User');
			$original = $this->read(null, $this->id);
			unset($original['Tag']);
		}
		
		// fix the appending stuff
		if($original)
		{
			// append the description
			if(isset($data[$this->detailsModel]['desc']) and isset($original[$this->detailsModel]['desc']))
			{
				$desc = $original[$this->detailsModel]['desc'];
				$desc .= "\n\n ---- Appended on: ". date('Y-m-d H:i:s');
				if(isset($user['name']))
				{
					$desc .= ' - By: '. $user['name'];
				}
				$desc .= "\n\n". $data[$this->detailsModel]['desc'];
				$data[$this->detailsModel]['desc'] = $desc;
			}
			
			if(isset($data[$this->alias]['tags']) and isset($original[$this->alias]['tags']))
			{
				$data[$this->alias]['tags'] = $original[$this->alias]['tags'] .= ', '. $data[$this->alias]['tags'];
			}
			
			$this->editorEmail = array(
				'subject' => __('Your %s has been updated by a Contributor', __($this->alias)),
				'to' => $original['User']['email'],
				'editor' => $user,
				'report' => $original,
				'data' => $data,
			);
		}
 		
 		return $this->saveAssociated($data, $options);
	}
	
	public function handleSaveDiff($id = false)
	{
		if(!$id)
			return false;
		
		foreach($this->saveDiff as $field => $vectors)
		{
			if($field == 'sac_id')
			{
				$vectors['old'] = $this->Sac->field('shortname', array('id' => $vectors['old']));
				$vectors['new'] = $this->Sac->field('shortname', array('id' => $vectors['new']));
			}
			$this->vectorReplace($id, $vectors['old'], $vectors['new']);
		}
	}
	
	public function vectorReplace($id = false, $oldVector = false, $newVector = false, $parent_field = false)
	{
	// used to replace vctor ids on an xref for things like adaccounts, victim asset tags, etc.
		
		if(!$id)
			return false;
		
		// nothing is needed to do
		if(!$oldVector and !$newVector) 
			return false;
		
		// if the oldVector is blank, but not the new vector, we're adding
		if(!$oldVector and $newVector)
		{
			$data = array(
				$this->xrefModel => array(
					$this->vectorsField => array($newVector => $newVector),
					$this->xrefField => $id,
				),
			);
		
			return $this->{$this->xrefModel}->add($data);
		}
		
		$conditions = array(
			$this->vectorModel.'.'.$this->vectorField => $oldVector,
			$this->xrefModel.'.'.$this->xrefField => $id,
		);
		
		$xrefRecords = $this->{$this->xrefModel}->find('all', array(
			'contain' => array($this->vectorModel),
			'conditions' => $conditions,
		));
		
		// if the new vector is blank, then we're removing the old vector
		if(!$newVector)
		{
			foreach($xrefRecords as $i => $xrefRecord)
			{
				$this->{$this->xrefModel}->delete($xrefRecord[$this->xrefModel]['id']);
			}
		}	
		// otherwise we're replacing	
		else
		{
			$newVector_id = $this->{$this->xrefModel}->{$this->vectorModel}->checkAdd($newVector);
			$data = array();
			foreach($xrefRecords as $i => $xrefRecord)
			{
				$this->{$this->xrefModel}->id = $xrefRecord[$this->xrefModel]['id'];
				$this->{$this->xrefModel}->data = array('id' => $xrefRecord[$this->xrefModel]['id'], $this->xrefVectorField => $newVector_id);
				$this->{$this->xrefModel}->save($this->{$this->xrefModel}->data);
			}
		}
	}
	
	public function notifyOwner()
	{
		// check if we have info to send an email
		if(!$this->editorEmail)
		{
			return true;
		}
		
		App::uses('CakeEmail', 'Network/Email');
		$Email = new CakeEmail();
		$result = $Email->template(Inflector::underscore($this->alias).'_edit')
			->config('default')
			->emailFormat('text')
			->subject($this->editorEmail['subject'])
			->to($this->editorEmail['to'])
			->viewVars($this->editorEmail)
			->send();
			
		$this->editorEmail = false;
		return $result;
	}
	
	public function conditionsAvailable($user_id = false)
	{
		$user = [];
		
		if($user_id and $this->User)
		{
			if($user = $this->User->find('first', ['conditions' => ['User.id' => $user_id]]))
			{
				$user = $user['User'];
			}
		}
		
		if(!$user)
		{
			$user = AuthComponent::user();
		}
		
		if(!$user)
			return [];
		
		if($user['role'] == 'admin')
			return [];
		
		return [
			'OR' => [
				// global
				[$this->alias.'.public' => 2],
				// org shared
				[$this->alias.'.public' => 1, $this->alias.'.org_group_id' => $user['org_group_id']],
				// private
				[$this->alias.'.public' => 0, $this->alias.'.user_id' => $user['id']]
			],
		];
	}
	
	public function listforUser($user_id = false, $conditions = [])
	{
		if(!$user_id)
			return [];
		
		$allConditions = $this->conditionsAvailable($user_id);
		
		$conditions = array_merge($allConditions, $conditions);
		
		return $this->find('list', ['conditions' => $conditions]);
	}
	
	public function _buildIndexConditions($ids = array(), $field = 'sac_id')
	{
		$conditions = $this->conditionsAvailable();
		$conditions[$this->alias.'.'. $field] = $ids;
		return $conditions;
	}
}
<?php
App::uses('AppModel', 'Model');
/**
 * ReportsEditor Model
 *
 * @property Report $Report
 * @property User $User
 */
class ReportsEditor extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */ 
	public $belongsTo = array(
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'report_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
	
	public function isEditor($report_id, $user_id) 
	{
	/*
	 * Checks if a user is an editor
	 */
		$value = $this->field('id', array('report_id' => $report_id, 'user_id' => $user_id, 'type' => 1));
		return $value;
	}
	
	public function isContributor($report_id, $user_id) 
	{
	/*
	 * Checks if a user is an contributor
	 */
		$value = $this->field('id', array('report_id' => $report_id, 'user_id' => $user_id, 'type' => 0));
		return $value;
	}
	
/**
 * Takes an report id and returns the list of editors, contributors, and available users
 */
	public function editorsList($report_id = false, $org_group_id = false, $exclude_user_id = false)
	{
		$out = array(
			'available' => array(),
			'editors' => array(),
			'contributors' => array(),
		);
		
		// new
		if(!$report_id)
		{
			if(!$org_group_id)
			{
				return $out;
			}
			
			$users = $this->User->typeFormList($org_group_id);
			if($exclude_user_id and isset($users[$exclude_user_id])) unset($users[$exclude_user_id]);
			$out['available'] = $users;
			return $out;
		}
		
		// all users in this org group
		$users = $this->User->typeFormList($org_group_id);
		if($exclude_user_id and isset($users[$exclude_user_id])) unset($users[$exclude_user_id]);

		
		// already selected users
		$editors = $this->find('all', array(
			'recursive' => 0,
			'contain' => array('User'),
			'conditions' => array(
				'ReportsEditor.report_id' => $report_id,
			),
		));
		
		foreach($editors as $editor)
		{
			$user_id = $editor['ReportsEditor']['user_id'];
			
			// build the editors list
			if($editor['ReportsEditor']['type'] == 1)
			{
				$out['editors'][$user_id] = $users[$user_id];
			}
			// build the contributors list
			elseif($editor['ReportsEditor']['type'] == 0)
			{
				$out['contributors'][$user_id] = $users[$user_id];
			}
			
			// remove user from the available users
			if(isset($users[$user_id]))
			{
				unset($users[$user_id]);
			}
		}
		$out['available'] = $users;
		
		return $out;
	}
	
	public function updateLists($report_id = false, $data = array())
	{
		if(!$report_id) return false;
		
		if(isset($data['editors_available']) and isset($data['editors_editors']) and isset($data['editors_contributors']))
		{
			$editors_available = array();
			$editors_editors = array();
			$editors_contributors = array();
			// convert the strings to an array()
			parse_str($data['editors_available'], $editors_available);
			if($editors_available) $editors_available = array_pop($editors_available);
			parse_str($data['editors_editors'], $editors_editors);
			if($editors_editors) $editors_editors = array_pop($editors_editors);
			parse_str($data['editors_contributors'], $editors_contributors);
			if($editors_contributors) $editors_contributors = array_pop($editors_contributors);
			
			//////// remove any that are in the available list
			if($editors_available and !empty($editors_available))
			{
				$this->deleteAll(array(
					'ReportsEditor.report_id' => $report_id,
					'ReportsEditor.user_id' => $editors_available,
				), false);
			}
			
			// get the current list
			$_current_editors = $this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'ReportsEditor.report_id' => $report_id,
				),
			));
			
			////// rearrange the $current_editors with a key we can check against
			$current_editors = array();
			foreach($_current_editors as $_current_editor)
			{
				$key = $_current_editor['ReportsEditor']['report_id']. '-'. $_current_editor['ReportsEditor']['user_id'];
				$current_editors[$key] = $_current_editor;
			}
			unset($_current_editors);
			
			$data_saveAll = array();
			$conditions_updateAll_contributors = array();
			$conditions_updateAll_editors = array();
			
			////// add/check the editors (type = 1 is an editor, type = 0 is a contributor)
			if($editors_editors and !empty($editors_editors))
			{
				foreach($editors_editors as $editors_editor_id)
				{
					$key = $report_id. '-'. $editors_editor_id;
					
					// check to see if they've changed from an editor to a contributor
					if(isset($current_editors[$key]))
					{
						// changed from contributor to editor
						if($current_editors[$key]['ReportsEditor']['type'] == 0)
						{
							$conditions_updateAll_contributors[] = $current_editors[$key]['ReportsEditor']['id'];
						}
					}
					else
					{
						$data_saveAll[] = array(
							'ReportsEditor' => array(
								'report_id' => $report_id,
								'user_id' => $editors_editor_id,
								'type' => 1,
							),
						);
					}
				}
			}
			
			////// add/check the contributors (type = 1 is an editor, type = 0 is a contributor)
			if($editors_contributors and !empty($editors_contributors))
			{
				foreach($editors_contributors as $editors_contributor_id)
				{
					$key = $report_id. '-'. $editors_contributor_id;
					
					// check to see if they've changed from an editor to a contributor
					if(isset($current_editors[$key]))
					{
						// changed from editor to contributor
						if($current_editors[$key]['ReportsEditor']['type'] == 1)
						{
							$conditions_updateAll_editors[] = $current_editors[$key]['ReportsEditor']['id'];
						}
					}
					else
					{
						$data_saveAll[] = array(
							'ReportsEditor' => array(
								'report_id' => $report_id,
								'user_id' => $editors_contributor_id,
								'type' => 0,
							),
						);
					}
				}
			}
			
			// change the ones going from an editor to a contributor
			if($conditions_updateAll_contributors)
			{
				$this->updateAll(
					array('ReportsEditor.type' => 1, 'ReportsEditor.modified' => 'NOW()'), 
					array('ReportsEditor.id' => $conditions_updateAll_contributors)
				);
			}
			// change the ones going from an editor to a contributor
			if($conditions_updateAll_editors)
			{
				$this->updateAll(
					array('ReportsEditor.type' => 0, 'ReportsEditor.modified' => 'NOW()'), 
					array('ReportsEditor.id' => $conditions_updateAll_editors)
				);
			}
			// add the new contributors/editors
			if($data_saveAll)
			{
				$this->saveAll($data_saveAll);
			}
		}
		
		return true;
	}
	
	function reviewed($report_id = false, $data = false)
	{
	/*
	 * Saves editors/contributors for a reviewed report
	 */
		
		if(!$report_id) return false;
		if(!$data) return false;
		
		// build the list of vectors to be saved
		$data_saveAll = array();
		foreach($data as $item)
		{
			
			// remove some of the items in the arrays
			unset(
				$item['id'],
				$item['temp_report_id']
			);
			
			$item['report_id'] = $report_id;
			
			$data_saveAll[]['ReportsEditor'] = $item;
		}
		
		
		// save the associations of vectors to this report
		return $this->saveAll($data_saveAll);
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * TempCategoriesEditor Model
 *
 * @property TempCategory $TempCategory
 * @property User $User
 */
class TempCategoriesEditor extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'temp_category_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
	
/**
 * Takes an category id and returns the list of editors, contributors, and available users
 */
	public function editorsList($temp_category_id = false, $org_group_id = false, $exclude_user_id = false)
	{
		$out = array(
			'available' => array(),
			'editors' => array(),
			'contributors' => array(),
		);
		
		// new
		if(!$temp_category_id)
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
				'TempCategoriesEditor.temp_category_id' => $temp_category_id,
			),
		));
		
		foreach($editors as $editor)
		{
			$user_id = $editor['TempCategoriesEditor']['user_id'];
			
			// build the editors list
			if($editor['TempCategoriesEditor']['type'] == 1)
			{
				if(isset($users[$user_id])) $out['editors'][$user_id] = $users[$user_id];
			}
			// build the contributors list
			elseif($editor['TempCategoriesEditor']['type'] == 0)
			{
				if(isset($users[$user_id])) $out['contributors'][$user_id] = $users[$user_id];
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
	
	public function updateLists($temp_category_id = false, $data = array())
	{
		if(!$temp_category_id) return false;
		
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
					'TempCategoriesEditor.temp_category_id' => $temp_category_id,
					'TempCategoriesEditor.user_id' => $editors_available,
				), false);
			}
			
			// get the current list
			$_current_editors = $this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'TempCategoriesEditor.temp_category_id' => $temp_category_id,
				),
			));
			
			////// rearrange the $current_editors with a key we can check against
			$current_editors = array();
			foreach($_current_editors as $_current_editor)
			{
				$key = $_current_editor['TempCategoriesEditor']['temp_category_id']. '-'. $_current_editor['TempCategoriesEditor']['user_id'];
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
					$key = $temp_category_id. '-'. $editors_editor_id;
					
					// check to see if they've changed from an editor to a contributor
					if(isset($current_editors[$key]))
					{
						// changed from contributor to editor
						if($current_editors[$key]['TempCategoriesEditor']['type'] == 0)
						{
							$conditions_updateAll_contributors[] = $current_editors[$key]['TempCategoriesEditor']['id'];
						}
					}
					else
					{
						$data_saveAll[] = array(
							'TempCategoriesEditor' => array(
								'temp_category_id' => $temp_category_id,
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
					$key = $temp_category_id. '-'. $editors_contributor_id;
					
					// check to see if they've changed from an editor to a contributor
					if(isset($current_editors[$key]))
					{
						// changed from editor to contributor
						if($current_editors[$key]['TempCategoriesEditor']['type'] == 1)
						{
							$conditions_updateAll_editors[] = $current_editors[$key]['TempCategoriesEditor']['id'];
						}
					}
					else
					{
						$data_saveAll[] = array(
							'TempCategoriesEditor' => array(
								'temp_category_id' => $temp_category_id,
								'user_id' => $editors_contributor_id,
								'type' => 0,
							),
						);
					}
				}
			}
			
			// change the ones going from a contributor to an editor
			if($conditions_updateAll_contributors)
			{
				$this->updateAll(
					array('TempCategoriesEditor.type' => 1, 'TempCategoriesEditor.modified' => 'NOW()'), 
					array('TempCategoriesEditor.id' => $conditions_updateAll_contributors)
				);
			}
			// change the ones going from an editor to a contributor
			if($conditions_updateAll_editors)
			{
				$this->updateAll(
					array('TempCategoriesEditor.type' => 0, 'TempCategoriesEditor.modified' => 'NOW()'), 
					array('TempCategoriesEditor.id' => $conditions_updateAll_editors)
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
}

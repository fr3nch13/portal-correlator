<?php
// app/Model/User.php

App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	
	public $displayField = 'name';
	
	public $validate = array(
		'email' => array(
			'required' => array(
				'rule' => array('email'),
				'message' => 'A valid email adress is required',
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false,
			),
		),
	);

	public $hasMany = array(
		'CombinedView' => array(
			'className' => 'CombinedView',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'UploadAddedUser' => array(
			'className' => 'Upload',
			'foreignKey' => 'added_user_id',
			'dependent' => true,
		),
		'TempUpload' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'TempUploadAddedUser' => array(
			'className' => 'TempUpload',
			'foreignKey' => 'added_user_id',
			'dependent' => true,
		),
		'LoginHistory' => array(
			'className' => 'LoginHistory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'TempCategoriesEditor' => array(
			'className' => 'TempCategoriesEditor',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'TempReportsEditor' => array(
			'className' => 'TempReportsEditor',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'CategoriesEditor' => array(
			'className' => 'CategoriesEditor',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ReportsEditor' => array(
			'className' => 'ReportsEditor',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'VectorTypeUser' => array(
			'className' => 'Vector',
			'foreignKey' => 'user_vtype_id',
			'dependent' => false,
		),
		'TempVectorTypeUser' => array(
			'className' => 'TempVector',
			'foreignKey' => 'user_vtype_id',
			'dependent' => false,
		),
		'IpaddressDnsTrackingUser' => array(
			'className' => 'Ipaddress',
			'foreignKey' => 'dns_auto_lookup_user_id',
			'dependent' => false,
		),
		'HostnameDnsTrackingUser' => array(
			'className' => 'Hostname',
			'foreignKey' => 'dns_auto_lookup_user_id',
			'dependent' => false,
		),
		'SignatureAddedUser' => array(
			'className' => 'Signature',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'SignatureUpdatedUser' => array(
			'className' => 'Signature',
			'foreignKey' => 'updated_user_id',
			'dependent' => false,
		),
		'YaraSignatureAddedUser' => array(
			'className' => 'YaraSignature',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'YaraSignatureUpdatedUser' => array(
			'className' => 'YaraSignature',
			'foreignKey' => 'updated_user_id',
			'dependent' => false,
		),
		'SnortSignatureAddedUser' => array(
			'className' => 'SnortSignature',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'SnortSignatureUpdatedUser' => array(
			'className' => 'SnortSignature',
			'foreignKey' => 'updated_user_id',
			'dependent' => false,
		),
		'WhoiserTransaction' => array(
			'className' => 'WhoiserTransaction',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
	);
	
	public $belongsTo = array(
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Usage.Usage',
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
				'active' => array(
					'conditions' => array(
						'User.active' => true,
					),
				),
			),
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active', 'admin_emails');
	
	// the path to the config file.
	public $configPath = false;
	
	// Any error relating to the config
	public $configError = false;
	
	public $includeOrgName = true;
	
	public function beforeFind($queryData = array())
	{
		// add the org group with the name to make the total name
		if($this->recursive == -1)
		{
			$this->recursive = 0;
		}
		
		return parent::beforeFind($queryData);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		if($results and $this->includeOrgName)
		{
			foreach($results as $i => $result)
			{
				if(isset($result[$this->OrgGroup->alias]['name']) and isset($result[$this->alias]['name']))
				{
					$results[$i][$this->alias]['name'] = '['. $results[$i][$this->OrgGroup->alias]['name']. '] '. $results[$i][$this->alias]['name'];
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function beforeSave($options = array())
	{
		// hash the password before saving it to the database
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// existing users
		if(!$created)
		{
			if(isset($this->data['User']['org_group_id']))
			{
				if(!$this->data['User']['org_group_id'])
				{
					$this->data['User']['org_group_id'] = 0;
				}
				$this->Category->updateAll(
					array('Category.org_group_id' => $this->data['User']['org_group_id']),
					array('Category.user_id' => $this->id)
				);
				$this->TempCategory->updateAll(
					array('TempCategory.org_group_id' => $this->data['User']['org_group_id']),
					array('TempCategory.user_id' => $this->id)
				);
				$this->Report->updateAll(
					array('Report.org_group_id' => $this->data['User']['org_group_id']),
					array('Report.user_id' => $this->id)
				);
				$this->TempReport->updateAll(
					array('TempReport.org_group_id' => $this->data['User']['org_group_id']),
					array('TempReport.user_id' => $this->id)
				);
				$this->Upload->updateAll(
					array('Upload.org_group_id' => $this->data['User']['org_group_id']),
					array('Upload.user_id' => $this->id)
				);
				$this->TempUpload->updateAll(
					array('TempUpload.org_group_id' => $this->data['User']['org_group_id']),
					array('TempUpload.user_id' => $this->id)
				);
			}
		}
		$this->afterdata = $this->data;
		
		return parent::afterSave($created, $options);
	}
	
	public function loginAttempt($input = false, $success = false, $user_id = false)
	{
		$email = false;
		if(isset($input['User']['email'])) 
		{
			$email = $input['User']['email'];
			if(!$user_id)
			{
				$user_id = $this->field('id', array('email' => $email));
			}
		}
		
		$data = array(
			'email' => $email,
			'user_agent' => env('HTTP_USER_AGENT'),
			'ipaddress' => env('REMOTE_ADDR'),
			'success' => $success,
			'user_id' => $user_id,
			'timestamp' => date('Y-m-d H:i:s'),
		);
		
		// count the login usage
		$this->Usage_updateCounts('login_attempt', 'user');
		if($success)
		{
			$this->Usage_updateCounts('login_attempt_success', 'user');
		}
		else
		{
			$this->Usage_updateCounts('login_attempt_fail', 'user');
		}
		
		$this->LoginHistory->create();
		return $this->LoginHistory->save($data);
	}
	
	public function lastLogin($user_id = null)
	{
		if($user_id)
		{
			$this->id = $user_id;
			return $this->saveField('lastlogin', date('Y-m-d H:i:s'));
		}
		return false;
	}
	
	public function adminEmails()
	{
		return $this->find('list', array(
			'conditions' => array(
				'User.active' => true,
				'User.role' => 'admin',
				'User.admin_emails' => true,
			),
			'fields' => array(
				'User.email',
			),
		));
	}
	
	public function availableRoles($user_id = false)
	{
		$this->id = $user_id;
		$originalRole = $this->field('role');
		$roles = $this->userRoles(); // returns a list of roles from hightest to lowest
		
		// automatically take out some roles that aren't available to any users
		if(isset($roles['api']))
			unset($roles['api']);
		
		// filter out the roles;
		foreach($roles as $role => $roleNice)
		{
			// the admin
			if($originalRole != $role)
			{
				unset($roles[$role]);
				continue;
			}
			return $roles;
		}
		
		return $roles;
	}
	
	public function snapshotDashboardGetStats($snapshotKeyRegex = false, $start = false, $end = false)
	{
		return $this->Snapshot_dashboardStats($snapshotKeyRegex, $start, $end);
	}
	
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return [];
	}
}


<?php
App::uses('AppModel', 'Model');

/**
 * TempUpload Model
 *
 * @property User $User
 * @property TempCategory $TempCategory
 * @property TempReport $TempReport
 */
class TempUpload extends AppModel 
{

	public $displayField = 'filename';
	
	public $validate = array(
		'filename' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'temp_category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'temp_report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'public' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'TempUploadAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
			'dependent' => true,
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'foreignKey' => 'temp_category_id',
		),
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'foreignKey' => 'temp_report_id',
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'report_id',
		),
		'UploadType' => array(
			'className' => 'UploadType',
			'foreignKey' => 'upload_type_id',
		),
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'TempVector' => array(
			'className' => 'TempVector',
			'joinTable' => 'temp_uploads_vectors',
			'foreignKey' => 'temp_upload_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempUploadsVector',
		),
	);
	
	public $actsAs = array('Tags.Taggable');
	
	// define the fields that can be searched
	public $searchFields = array(
		'TempUpload.filename',
		'TempUpload.mimetype',
		'TempUpload.type',
		'TempUpload.md5',
		'TempUpload.mysource',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('public');
	
	// set in beforeSave/beforeDelete and used in afterSave/afterDelete
	public $file_info = false;
	
	// set in beforeDelete and used in afterDelete
	public $delete_file = false;
	
	// holds a copy of the active Upload model
	public $Upload = false;
	
	// determine where to redirect after a save
	public $saveRedirect = false;
	
	// determine if an email should be sent when an editor/contributor added an upload
	public $editorEmail = false;
	
	public function beforeValidate($options = array())
	{
		if(isset($this->data[$this->alias]))
		{
			$this->data = $this->fixData($this->data);
		}
		return parent::beforeValidate($options);
	}

	public function beforeSave($options = array())
	{
		
		if(isset($this->data[$this->alias]) and (!isset($this->data[$this->alias]['tmp_name']) or !$this->data[$this->alias]['tmp_name']))
		{
			// a category/report that has been added, but no file set
			unset($this->data[$this->alias]);
			return true;
		}
		// see if the file uploaded ok
		// if there is no id, then it's being created
		if(isset($this->data[$this->alias]) and !$this->id and !isset($this->data[$this->alias]['id']))
		{
			if(isset($this->data[$this->alias]['error']) and $this->data[$this->alias]['error'] == 4)
			{
				unset($this->data[$this->alias]);
				return parent::beforeSave($options);
			}
			
			// make sure the user is associated with this upload when adding a new one
			if(!isset($this->data[$this->alias]['user_id']))
			{
				$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
			}
			
			// make sure the user is associated with this upload when adding a new one
			if(!isset($this->data[$this->alias]['added_user_id']))
			{
				$this->data[$this->alias]['added_user_id'] = AuthComponent::user('id');
			}
			
			// also make sure this file has the same org group as the user adding it
			if(!isset($this->data[$this->alias]['org_group_id']))
			{
				$this->data[$this->alias]['org_group_id'] = AuthComponent::user('org_group_id');
			}
			
			// if it belongs to a category, get info from that category
			if(isset($this->data[$this->alias]['temp_category_id']) and $this->data[$this->alias]['temp_category_id'])
			{
				if(!isset($this->data[$this->alias]['user_id']) or !isset($this->data[$this->alias]['public']))
				{
					$this->TempCategory->recursive = -1;
					$fields = $this->TempCategory->read(array('public', 'user_id'), $this->data[$this->alias]['temp_category_id']);
					$this->data[$this->alias]['user_id'] = $fields['TempCategory']['user_id'];
					$this->data[$this->alias]['public'] = $fields['TempCategory']['public'];
				}
				$this->saveRedirect = array('controller' => 'temp_categories', 'action' => 'view', $this->data[$this->alias]['temp_category_id'], '#' => 'ui-tabs-2');
			}
			elseif(isset($this->data[$this->alias]['category_id']) and $this->data[$this->alias]['category_id'])
			{
				if(!isset($this->data[$this->alias]['user_id']) or !isset($this->data[$this->alias]['public']))
				{
					$this->Category->recursive = -1;
					$fields = $this->Category->read(array('public', 'user_id'), $this->data[$this->alias]['category_id']);
					$this->data[$this->alias]['user_id'] = $fields['Category']['user_id'];
					$this->data[$this->alias]['public'] = $fields['Category']['public'];
				}
				$this->saveRedirect = array('controller' => 'categories', 'action' => 'view', $this->data[$this->alias]['category_id'], '#' => 'ui-tabs-13');
			}
			// if it belongs to a report, get info from that report
			if(isset($this->data[$this->alias]['temp_report_id']) and $this->data[$this->alias]['temp_report_id'])
			{
				if(!isset($this->data[$this->alias]['user_id']) or !isset($this->data[$this->alias]['public']))
				{
					$this->TempReport->recursive = -1;
					$fields = $this->TempReport->read(array('public', 'user_id'), $this->data[$this->alias]['temp_report_id']);
					$this->data[$this->alias]['user_id'] = $fields['TempReport']['user_id'];
					$this->data[$this->alias]['public'] = $fields['TempReport']['public'];
				}
				$this->saveRedirect = array('controller' => 'temp_reports', 'action' => 'view', $this->data[$this->alias]['temp_report_id'], '#' => 'ui-tabs-2');
			}
			elseif(isset($this->data[$this->alias]['report_id']) and $this->data[$this->alias]['report_id'])
			{
				if(!isset($this->data[$this->alias]['user_id']) or !isset($this->data[$this->alias]['public']))
				{
					$this->Report->recursive = -1;
					$fields = $this->Report->read(array('public', 'user_id'), $this->data[$this->alias]['report_id']);
					$this->data[$this->alias]['user_id'] = $fields['Report']['user_id'];
					$this->data[$this->alias]['public'] = $fields['Report']['public'];
				}
				$this->saveRedirect = array('controller' => 'reports', 'action' => 'view', $this->data[$this->alias]['report_id'], '#' => 'ui-tabs-12');
			}
			
			// set some more variables based on the file info
			$this->data[$this->alias]['filename'] = $this->data[$this->alias]['name'];
			$this->data[$this->alias]['mimetype'] = $this->data[$this->alias]['type'];
			$this->data[$this->alias]['size'] = $this->data[$this->alias]['size'];
			$this->data[$this->alias]['type'] = pathinfo($this->data[$this->alias]['name'], PATHINFO_EXTENSION);
			$this->data[$this->alias]['md5'] = md5_file($this->data[$this->alias]['tmp_name']);
			
			// for some reason *.log files are coming in as application/octet-stream.
			// (see also dump files)
			if(strtolower($this->data[$this->alias]['type']) == 'log')
			{
				$this->data[$this->alias]['mimetype'] = 'text/plain';
			}
			
			$this->file_info = $this->data[$this->alias];
		}
		return parent::beforeSave($options);
	}

	public function afterSave($created = false, $options = array())
	{
		// move the file to it's proper location
		$file_path = false;
		if($this->file_info)
		{
			$file_info = $this->file_info;
			$paths = $this->paths($this->data[$this->alias]['id'], true, $file_info['name']);
			if($paths['sys'])
			{
				umask(0);
				if(rename($file_info['tmp_name'], $paths['sys']))
				{
					$file_path = $paths['sys'];
				}
			}
		}
		
		if(isset($this->data[$this->alias]['id']) and $file_path)
		{
			// Save the vectors
			$temp_vectors = array();
			$all_vectors = array();
		
			$mimetype = false;
			if(isset($this->data[$this->alias]['mimetype']))
			{
				$mimetype = $this->data[$this->alias]['mimetype'];
			}
			
			if(isset($this->data[$this->alias]['scan']) and $this->data[$this->alias]['scan'])
			{
				$all_vectors = $this->extractItemsFromFile($file_path, $mimetype);
			}
			
			if($all_vectors)
			{
				foreach($all_vectors as $type => $vectors)
				{
					foreach($vectors as $i => $vector)
					{
						$vector = trim($vector);
						$temp_vectors[$vector] = $vector; // format and make unique
					}
				}
			}
			
			if($temp_vectors)
			{
				sort($temp_vectors);
				
				$data = array(
					'TempUploadsVector' => array(
						'temp_vectors' => $temp_vectors,
						'temp_upload_id' => $this->id,
					),
				);
				
				$this->TempUploadsVector->add($data);
			}
		}
		if($this->editorEmail)
		{
			$this->editorEmail['temp_upload'] = $this->data;
			$this->notifyOwner();
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function beforeDelete($cascade = true)
	{
		// find the info for deleting the file
		if($filename = $this->field('filename'))
		{
			$paths = $this->paths($this->id, false, $filename);
			$this->delete_file = $paths['sys'];
		}
		return parent::beforeDelete($cascade = true);
	}
	
	public function afterDelete()
	{
		// delete the file
		if($this->delete_file)
		{
			// try to delete the file
			if(is_file($this->delete_file) and is_writable($this->delete_file)) unlink($this->delete_file);
			
			// should be the only file in this directory
			$path_parts = explode(DS, $this->delete_file);
			// remove the filename
			array_pop($path_parts);
			while($path_parts)
			{
				$this_dir = array_pop($path_parts);
				if($this_dir == 'uploads') break;
				if(in_array($this_dir, range(0, 9)))
				{
					$dir = implode(DS, $path_parts). DS. $this_dir;
					$listing = glob ("$dir/*");
					// directory is empty
					if(empty($listing)) rmdir($dir);
				}
			}
		}
		
		return parent::afterDelete();
	}
	
	public function reviewed($ids = array(), $category_id = 0, $report_id = 0)
	{
		// track the new ids added to the uploads table
		$out = array();
		
		// save the category and it's details first
		if(!$this->Upload)
		{
			App::import('Model', 'Upload');
			$this->Upload = new Upload();
		}
		
		if(is_string($ids))
		{
			$ids = array($ids);
		}
		
		if(!$category_id) $category_id = 0;
		if(!$report_id) $report_id = 0;
		
		// track the temp ids for later removal
		$temp_ids = array();
		$upload_ids = array();
		
		// make the reviewError an array for any possible issues
		$this->reviewError = array();
		
		foreach($ids as $id)
		{
			$this->recursive = 1;
			$temp_upload = $this->read(null, $id);
			if(!$temp_upload) continue;
		
			// keep track of the old temp id
			// for use in moving over the fole and for deleting the temp record
			$temp_upload_id = $temp_upload['TempUpload']['id'];
			
			// build the data array to be moved over to the Uploads table
			$data = array();
			$data['Upload'] = $temp_upload['TempUpload'];
			
			// add some info to the data table
			$data['Upload']['reviewed'] = date('Y-m-d H:i:s');
			
			if(!$data['Upload']['category_id'] and $category_id)
			{
				$data['Upload']['category_id'] = $category_id;
			}
			if(!$data['Upload']['report_id'] and $report_id)
			{
				$data['Upload']['report_id'] = $report_id;
			}
			
			// if it's associated with a Category or Report and they havent been made active
			if($data['Upload']['category_id'])
			{
				$this->Category->id = $data['Upload']['category_id'];
				if (!$this->Category->exists()) 
				{
					$this->reviewError[] = __('The %s for this %s doesn\'t exist.', __('Category'), __('File'));
					continue;
				}
			}
			if($data['Upload']['temp_category_id'] and !$data['Upload']['category_id'])
			{
				$this->TempCategory->id = $data['Upload']['temp_category_id'];
				if ($this->TempCategory->exists()) 
				{
					$this->reviewError[] = __('The %s for this %s exists. Please review the %s.', __('Temp Category'), __('File'), __('Category'));
					continue;
				}
			}
			if($data['Upload']['report_id'])
			{
				$this->Report->id = $data['Upload']['report_id'];
				if (!$this->Report->exists()) 
				{
					$this->reviewError[] = __('The %s for this %s doesn\'t exist.', __('Report'), __('File'));
					continue;
				}
			}
			if($data['Upload']['temp_report_id'] and !$data['Upload']['report_id'])
			{
				$this->TempReport->id = $data['Upload']['temp_report_id'];
				if ($this->TempReport->exists()) 
				{
					$this->reviewError[] = __('The %s for this %s exists. Please review the %s.', __('Temp Report'), __('File'), __('Report'));
					continue;
				}
			}
			
			unset(
				$data['Upload']['id'],
				$data['Upload']['temp_category_id'],
				$data['Upload']['temp_report_id']
			);
			$this->Upload->create();
			$this->Upload->data = $data;
		
			// save the reviewed upload as a new one in the uploads table
			if(!$this->Upload->saveAssociated($this->Upload->data)) continue;
			
			// move it's file to the uploads directory
			$temp_paths = $this->paths($temp_upload_id, true, $data['Upload']['filename']);
			$paths = $this->Upload->paths($this->Upload->id, true, $data['Upload']['filename']);
			
			if($paths['sys'] and $temp_paths['sys'])
			{
				umask(0);
				if(@rename($temp_paths['sys'], $paths['sys']))
				{
					$file_path = $paths['sys'];
				}
			}
			
			// save the vectors and associations
			if(isset($temp_upload['TempVector']) and count($temp_upload['TempVector']))
			{
				if(!$this->Upload->UploadsVector->reviewed($this->Upload->id, $temp_upload['TempVector']))
				{
					$this->reviewError[] = __('The %s weren\'t transfered correctly.', __('Vectors'));
					continue;
				}
			}
			
			// track the new ids
			$upload_ids[] = $this->Upload->id;
			
			// track the old ids
			$temp_ids[] = $temp_upload_id;
		}
		
		if(empty($this->reviewError))
		{
			$this->reviewError = false;
		}
		
		if($temp_ids)
		{
			$this->deleteAll(array(
				'TempUpload.id' => $temp_ids,
			));
		}
		
		return $upload_ids;
	}
	
	public function fixData($data)
	{
		if(isset($data[$this->alias]['file']))
		{
			$data[$this->alias] = array_merge($data[$this->alias], $data[$this->alias]['file']);
			unset($data[$this->alias]['file']);
			
			//if the error is a 4, then there is no file
			if(isset($data[$this->alias]['error']) and $data[$this->alias]['error'] == 4)
			{
				unset($data[$this->alias]);
			}
		}
		return $data;
	}
	
	public function checkNewPermissions($named = array(), $data = array())
	{
		if(!isset($data[$this->alias]))
		{
			$data[$this->alias] = array();
		}
		
		$out = array();
		$out[$this->alias]['temp_category_id'] = 0;
		$out[$this->alias]['temp_report_id'] = 0;
		$out[$this->alias]['category_id'] = 0;
		$out[$this->alias]['report_id'] = 0;
		$out[$this->alias]['user_id'] = AuthComponent::user('id');
		$out[$this->alias]['org_group_id'] = AuthComponent::user('org_group_id');
		$out[$this->alias]['public'] = 0;
		$out[$this->alias]['added_user_id'] = AuthComponent::user('id');
		$out[$this->alias]['_title'] = '';
		
		if(!$named and isset($data[$this->alias]))
		{
			$named = $data[$this->alias];
		}
		
		if(isset($named['temp_category_id']))
		{
			$id = $named['temp_category_id'];
			$this->TempCategory->contain('User');
			$temp_category = $this->TempCategory->read(null, $id);
			
			// check permissions
			// only owner of temps can add
			if($temp_category['User']['id'] != AuthComponent::user('id'))
			{
				$this->modelError = __('Only the owner of a %s can add a %s.', __('Temp Category'), __('File'));
				return false;
			}
			
			// fill out some info
			$out[$this->alias]['temp_category_id'] = $id;
			$out[$this->alias]['temp_report_id'] = 0;
			$out[$this->alias]['category_id'] = 0;
			$out[$this->alias]['report_id'] = 0;
			$out[$this->alias]['user_id'] = $temp_category['User']['id'];
			$out[$this->alias]['org_group_id'] = $temp_category['User']['org_group_id'];
			$out[$this->alias]['public'] = $temp_category['TempCategory']['public'];
			$out[$this->alias]['added_user_id'] = AuthComponent::user('id');
			$out[$this->alias]['_title'] = __(' to %s: %s', __('Temp Category'), $temp_category['TempCategory']['name']);
		}
		elseif(isset($named['temp_report_id']))
		{
			$id = $named['temp_report_id'];
			$this->TempReport->contain('User');
			$temp_report = $this->TempReport->read(null, $id);
			
			// check permissions
			// only owner of temps can add
			if($temp_report['User']['id'] != AuthComponent::user('id'))
			{
				$this->modelError = __('Only the owner of a %s can add a %s.', __('Temp Report'), __('File'));
				return false;
			}
			
			// fill out some info
			$out[$this->alias]['temp_category_id'] = 0;
			$out[$this->alias]['temp_report_id'] = $id;
			$out[$this->alias]['category_id'] = 0;
			$out[$this->alias]['report_id'] = 0;
			$out[$this->alias]['user_id'] = $temp_report['User']['id'];
			$out[$this->alias]['org_group_id'] = $temp_report['User']['org_group_id'];
			$out[$this->alias]['public'] = $temp_report['TempReport']['public'];
			$out[$this->alias]['added_user_id'] = AuthComponent::user('id');
			$out[$this->alias]['_title'] = __(' to %s: %s', __('Temp Report'), $temp_report['TempReport']['name']);
		}
		elseif(isset($named['category_id']))
		{
			$id = $named['category_id'];
			$this->Category->contain('User', 'CategoriesEditor');
			$category = $this->Category->read(null, $id);
			$user_editor_ids = Set::extract('/user_id', $category['CategoriesEditor']);
			
			// check permissions
			// owner, editors, and contributors can add file
			if($category['User']['id'] != AuthComponent::user('id') and !in_array(AuthComponent::user('id'), $user_editor_ids))
			{
				$this->modelError = __('Only the Owner, Editors, and Contributors of a %s can add a %s.', __('Category'), __('File'));
				return false;
			}
			// mark this upload to be emailed to the owner
			if(in_array(AuthComponent::user('id'), $user_editor_ids))
			{
				$this->editorEmail = array(
					'subject' => __('A new %s has been uploaded to your %s', __('File'), __('Category')),
					'to' => $category['User']['email'],
					'editor' => AuthComponent::user(),
					'category' => $category,
				);
			}
			
			// fill out some info
			$out[$this->alias]['temp_category_id'] = 0;
			$out[$this->alias]['temp_report_id'] = 0;
			$out[$this->alias]['category_id'] = $id;
			$out[$this->alias]['report_id'] = 0;
			$out[$this->alias]['user_id'] = $category['User']['id'];
			$out[$this->alias]['org_group_id'] = $category['User']['org_group_id'];
			$out[$this->alias]['public'] = $category['Category']['public'];
			$out[$this->alias]['added_user_id'] = AuthComponent::user('id');
			$out[$this->alias]['_title'] = __(' to %s: %s', __('Category'), $category['Category']['name']);
		}
		elseif(isset($named['report_id']))
		{
			$id = $named['report_id'];
			$this->Report->contain('User', 'ReportsEditor');
			$report = $this->Report->read(null, $id);
			$user_editor_ids = Set::extract('/user_id', $report['ReportsEditor']);
			
			// check permissions
			// owner, editors, and contributors can add file
			if($report['User']['id'] != AuthComponent::user('id') and !in_array(AuthComponent::user('id'), $user_editor_ids))
			{
				$this->modelError = __('Only the Owner, Editors, and Contributors of a %s can add a %s.', __('Report'), __('File'));
				return false;
			}
			// mark this upload to be emailed to the owner
			if(in_array(AuthComponent::user('id'), $user_editor_ids))
			{
				$this->editorEmail = array(
					'subject' => __('A new %s has been uploaded to your %s', __('File'), __('Report')),
					'to' => $report['User']['email'],
					'editor' => AuthComponent::user(),
					'report' => $report,
				);
			}
			
			// fill out some info
			$out[$this->alias]['temp_category_id'] = 0;
			$out[$this->alias]['temp_report_id'] = 0;
			$out[$this->alias]['category_id'] = 0;
			$out[$this->alias]['report_id'] = $id;
			$out[$this->alias]['user_id'] = $report['User']['id'];
			$out[$this->alias]['org_group_id'] = $report['User']['org_group_id'];
			$out[$this->alias]['public'] = $report['Report']['public'];
			$out[$this->alias]['added_user_id'] = AuthComponent::user('id');
			$out[$this->alias]['_title'] = __(' to %s: %s', __('Report'), $report['Report']['name']);
		}
		$data[$this->alias] = array_merge($data[$this->alias], $out[$this->alias]);
		return $data;
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
		$result = $Email->template('new_upload')
			->config('default')
			->emailFormat('text')
			->subject($this->editorEmail['subject'])
			->to($this->editorEmail['to'])
			->viewVars($this->editorEmail)
			->send();
			
		$this->editorEmail = false;
		return $result;
	}
	
	// Validation Rules
	public function RuleFileError($data = false)
	{
		if(!isset($data['error'])) return true;
		$error = $data['error'];
		if($error === 0) return false;
		
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		
		// change the validation message based on what the file error is
		$errorMessage = '(Error)'. $error;
		switch ($error)
		{
			case 1: 
			case 2: 
				$errorMessage = __('The uploaded %s exceeds allowed size of %sM', __('File'), $upload_mb); 
				break;
			case 3: 
				$errorMessage = __('The uploaded %s was only partially uploaded.', __('File')); 
				break;
			case 4: 
				return true; 
				break;
			case 6: 
				$errorMessage = __('Missing a temporary upload folder.'); 
				break;
			case 7: 
				$errorMessage = __('Failed to write %s to disk.', __('File')); 
				break;
			
		}
		$this->validationErrors['file'][] = $errorMessage;
		return false;
	}
}
<?php
App::uses('AppModel', 'Model');

/**
 * Upload Model
 *
 * @property User $User
 * @property Category $Category
 * @property Report $Report
 */
class Upload extends AppModel 
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
		'category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'report_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'public' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		// checking that the file is uploaded, etc
		'file' => array(
			'exists' => array(
				'rule' => array('RuleFile'),
				'message' => 'There was an error with this file',
				'last'    => true
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'UploadAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
			'dependent' => true,
		),
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
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
		'Vector' => array(
			'className' => 'Vector',
			'joinTable' => 'uploads_vectors',
			'foreignKey' => 'upload_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'UploadsVector',
		),
	);
	
	public $actsAs = array('Tags.Taggable');
	
	
	// define the fields that can be searched
	public $searchFields = array(
		'Upload.filename',
		'Upload.mimetype',
		'Upload.type',
		'Upload.md5',
		'Upload.mysource',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('public');
	
	// set in beforeSave/beforeDelete and used in afterSave/afterDelete
	public $file_info = false;
	
	// set in beforeDelete and used in afterDelete
	public $delete_file = false;
	
	public function beforeDelete($cascade = true)
	{
		// find the info for deleting the file
		if($filename = $this->field('filename'))
		{
			$paths = $this->paths($this->id, false, $filename);
			$this->delete_file = $paths['sys'];
			
//			$dir = DS. implode(DS, );
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
		
	
	public function listUploadRelatedIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		/////////// this upload's vectors
		if(!$object_vector_ids = $this->UploadsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'UploadsVector.active' => 1,
			'UploadsVector.vector_id' => $object_vector_ids,
			'UploadsVector.upload_id !=' => $object_id,
			'Vector.bad' => 0,
		);
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('UploadsVector.upload_id', 'UploadsVector.upload_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listUploadRelated('.$object_id.')';
		}
		
		return $this->UploadsVector->find('list', $options);
	}
	
	public function sqlUploadRelated($upload_id = false, $admin = false)
	{
	/*
	 * Upload related to another upload
	 * Builds the complex query for the conditions
	 */
		if(!$upload_id) return false;
		
		// get the vector ids from this upload
		$this->UploadsVector->recursive = 0;
		$db = $this->UploadsVector->getDataSource();
		
		$subQuery_conditions = array('UploadsVector1.upload_id' => $upload_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['UploadsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`UploadsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `UploadsVector1`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		$subQuery = ' `UploadsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['UploadsVector2.active'] = 1;
		}
		
		// get the upload_ids from this model that share the came vectors,
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`UploadsVector2`.`upload_id`'),
				'table'		 => $db->fullTableName($this->UploadsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`UploadsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `UploadsVector2`.`vector_id`'
					),
				),
			),
			$this->UploadsVector
		);
		// get the uploads themselves
		
		$subQuery2 = ' `Upload`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
	
	public function compare($upload_id_1 = false, $upload_id_2 = false, $admin = false)
	{
	/*
	 * Compare 2 categories based on their vectors
	 */
		$data = array(
			'upload_1' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'upload_2' => array(
				'string' => false,
				'hash' => false,
				'unique_vector_ids' => array(),
			),
			'ssdeep_percent' => 0,
			'similar_percent' => 0,
			'similar_vector_ids' => array(),
		);
		
		// get all of the good and active vectors for each of the categories
		$vectors_1_conditions = array(
			'UploadsVector.upload_id' => $upload_id_1,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_1_conditions['UploadsVector.active'] = 1;
		}
		$vectors_1 = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_1_conditions,
		));
		asort($vectors_1);
		
		$vectors_2_conditions = array(
			'UploadsVector.upload_id' => $upload_id_2,
			'Vector.bad' => 0,
		);
		if(!$admin)
		{
			$vectors_2_conditions['UploadsVector.active'] = 1;
		}
		
		$vectors_2 = $this->UploadsVector->find('list', array(
			'recursive' => 0,
			'contain' => 'Vector',
			'fields' => array('Vector.id', 'Vector.vector'),
			'conditions' => $vectors_2_conditions,
		));		
		asort($vectors_2);
		
		// find the unique vector_ids
		$vectors_1_unique = array_diff_assoc($vectors_1, $vectors_2);
		$vectors_2_unique = array_diff_assoc($vectors_2, $vectors_1);
		
		// find the similar vector_ids
		$vectors_similar = array_intersect_assoc($vectors_1, $vectors_2);

		// find out the percent of similar vectors
		$vector_count_similar = count($vectors_similar);
		$vector_count_total = count(array_merge($vectors_1, $vectors_2));
		$similar_percent = round(($vector_count_similar / $vector_count_total) * 100, 2);
		
		
		
		// build the strings for the ssdeep comparisons
		$string_1 = "\n". implode("\n",$vectors_1);

		$string_2 = "\n". implode("\n",$vectors_2);
		
		$this->id = $upload_id_1;
		$paths_1 = $this->paths($upload_id_1, false, $this->field('filename'));
		$hash_1 = $this->ssdeep_fuzzy_hash_filename($paths_1['sys']);
		
		$this->id = $upload_id_2;
		$paths_2 = $this->paths($upload_id_2, false, $this->field('filename'));
		$hash_2 = $this->ssdeep_fuzzy_hash_filename($paths_2['sys']);
		
		$this->id = false;
		
		$data = array(
			'upload_1' => array(
				'hash' => $hash_1,
				'unique_vectors' => $vectors_1_unique,
			),
			'upload_2' => array(
				'hash' => $hash_2,
				'unique_vectors' => $vectors_2_unique,
			),
			'ssdeep_percent' => $this->ssdeep_fuzzy_compare($hash_1, $hash_2),
			'similar_percent' => $similar_percent,
			'similar_vectors' => $vectors_similar,
		);
		return $data;
	}
	
	public function transferVectors($id = false)
	{
		$upload = $this->read(null, $id);
		
		if(!$upload)
		{
			$this->modelError = __('Unable to find the file');
			return false;
		}
		
		if(!$upload['Upload']['report_id'] and !$upload['Upload']['category_id'])
		{
			$this->modelError = __('This File isn\'t associated with a Report, or a Category');
			return false;
		}
		
		$vectors = $this->UploadsVector->find('all', array(
			'conditions' => array(
				'UploadsVector.upload_id' => $id,
			),
			'recursive' => 0,
			'contain' => array('Vector.vector', 'Vector.id'),
		));
		
		// build the array to transfer the data to the parent
		$vector_ids = array();
		$vector_xref_data = array();
		foreach($vectors as $item)
		{
			$vector = $item['Vector']['vector'];
			$vector_ids[$vector] = $item['Vector']['id'];
			
			// track the xref vector data
			$vector_xref_data[$vector] = $item['UploadsVector'];
			
			// unset some variables
			unset(
				$vector_xref_data[$vector]['id'],
				$vector_xref_data[$vector]['upload_id'],
				$vector_xref_data[$vector]['modified']
			);
			
			// add some variables
			$vector_xref_data[$vector]['created'] = date('Y-m-d H:i:s');
		}
		
		if($upload['Upload']['report_id'])
		{
			if(!$this->Vector->ReportsVector->saveAssociations($upload['Upload']['report_id'], $vector_ids, $vector_xref_data))
			{
				$this->modelError = __('Unable to transfer the Vectors to the parent Report.');
				return false;
			}
			
			return true;
		}
		elseif($upload['Upload']['category_id'])
		{
			if($this->Vector->CategoriesVector->saveAssociations($upload['Upload']['category_id'], $vector_ids, $vector_xref_data))
			{
				$this->modelError = __('Unable to transfer the Vectors to the parent Category.');
				return false;
			}
			
			return true;
		}
		else
		{
			$this->modelError = __('Unable to transfer the Vectors.');
			return false;
		}
	}
	
	// Validation Rules
	public function RuleFile($file = false)
	{
		if(!$file) return false;
		if(!isset($file['file']['error'])) return false;
		if($file['file']['error'] === 0) return true;
		
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		
		// change the validation message based on what the file error is
		$errorMessage = '';
		switch ($file['file']['error'])
		{
			case 1: 
			case 2: 
				$errorMessage = __('The uploaded file exceeds allowed size of %sM', $upload_mb); 
				break;
			case 3: 
				$errorMessage = __('The uploaded file was only partially uploaded.'); 
				break;
			case 4: 
				$errorMessage = __('No file was uploaded.'); 
				break;
			case 6: 
				$errorMessage = __('Missing a temporary upload folder.'); 
				break;
			case 7: 
				$errorMessage = __('Failed to write file to disk.'); 
				break;
			
		}
		$this->validationErrors['file'][] = $errorMessage;
		return false;
	}
	
	
/// Old functions, not used anymore

	public function OLD_beforeSave()
	{
/*
		// see if the file uploaded ok
		// if there is no id, then it's being created
		if($this->data['Upload'] and !$this->id and !isset($this->data['Upload']['id']))
		{
			// make sure [file] exists
			if(isset($this->data['Upload']['file']))
			{
				$this->data['Upload'] = array_merge($this->data['Upload']['file'], $this->data['Upload']);
				unset($this->data['Upload']['file']);
			}
			
			if($this->data['Upload']['error'] != 0)
			{
				unset($this->data['Upload']);
				return parent::beforeSave($options);
			}
			
			// make sure the user is associated with this upload when adding a new one
			if(!isset($this->data['Upload']['user_id']))
			{
				$this->data['Upload']['user_id'] = AuthComponent::user('id');
			}
			
			// if it belongs to a category, get info from that category
			if(isset($this->data['Upload']['category_id']) and $this->data['Upload']['category_id'])
			{
				if(!isset($this->data['Upload']['user_id']) or !isset($this->data['Upload']['public']))
				{
					$this->Category->recursive = -1;
					$fields = $this->Category->read(array('public', 'user_id'), $this->data['Upload']['category_id']);
					$this->data['Upload']['user_id'] = $fields['Category']['user_id'];
					$this->data['Upload']['public'] = $fields['Category']['public'];
				}
			}
			// if it belongs to a report, get info from that report
			if(isset($this->data['Upload']['report_id']) and $this->data['Upload']['report_id'])
			{
				if(!isset($this->data['Upload']['user_id']) or !isset($this->data['Upload']['public']))
				{
					$this->Report->recursive = -1;
					$fields = $this->Report->read(array('public', 'user_id'), $this->data['Upload']['report_id']);
					$this->data['Upload']['user_id'] = $fields['Report']['user_id'];
					$this->data['Upload']['public'] = $fields['Report']['public'];
				}
			}
			
			// set some more variables based on the file info
			$this->data['Upload']['filename'] = $this->data['Upload']['name'];
			$this->data['Upload']['mimetype'] = $this->data['Upload']['type'];
			$this->data['Upload']['size'] = $this->data['Upload']['size'];
			$this->data['Upload']['type'] = pathinfo($this->data['Upload']['name'], PATHINFO_EXTENSION);
			$this->data['Upload']['md5'] = md5_file($this->data['Upload']['tmp_name']);
			
			// for some reason *.log files are coming in as application/octet-stream.
			// (see also dump files)
			if(strtolower($this->data['Upload']['type']) == 'log')
			{
				$this->data['Upload']['mimetype'] = 'text/plain';
			}
			
			$this->file_info = $this->data['Upload'];
//			unset($this->data['Upload']['file']);
		}
*/
		return parent::beforeSave($options);
	}

	public function OLD_afterSave($created = false)
	{
/*				
		// move the file to it's proper location
		$file_path = false;
		if($this->file_info)
		{
			$file_info = $this->file_info;
			$paths = $this->paths($this->data['Upload']['id'], true, $file_info['name']);
			if($paths['sys'])
			{
				umask(0);
				if(rename($file_info['tmp_name'], $paths['sys']))
				{
					$file_path = $paths['sys'];
				}
			}
		}
		// try to scan the file for vectors
		$this->sessionVectors = false;
		$this->sessionVectorId = false;
		
		if(isset($this->data['Upload']['id']) and $file_path)
		{
			$mimetype = false;
			if(isset($this->data['Upload']['mimetype']))
			{
				$mimetype = $this->data['Upload']['mimetype'];
			}
			
			$all_vectors = $this->extractItemsFromFile($paths['sys'], $mimetype);
			
			// clean them up and format them for a saveMany()
			$_vectors = array();
			if($all_vectors)
			{
				foreach($all_vectors as $type => $vectors)
				{
					foreach($vectors as $i => $vector)
					{
						$vector = trim($vector);
						$_vectors[$vector] = $vector; // format and make unique
					}
				}
				sort($_vectors);
				$_vectors = implode("\n", $_vectors);
				
				$this->sessionVectors .= "\n". $_vectors;
				
				// found vectors, make sure we track the ids and have the sirte forward the user to review the vectors
				if(trim($this->sessionVectors))
				{
					$this->sessionVectorId = $this->data['Upload']['id'];
					// associated with a Category
					if(isset($this->data['Upload']['category_id']) and $this->data['Upload']['category_id'])
					{
						$this->sessionIds['Category'] = $this->data['Upload']['category_id'];
					}
					// associated with a report
					elseif(isset($this->data['Upload']['report_id']) and $this->data['Upload']['report_id'])
					{
						$this->sessionIds['Report'] = $this->data['Upload']['report_id'];
					}
				}
			}
		}
		
*/
		return parent::afterSave($created);
	}
}
?>
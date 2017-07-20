<?php
App::uses('AppModel', 'Model');

/**
 * Import Model
 *
 * @property User $User
 * @property ImportManager $ImportManager
 */
class Import extends AppModel 
{	
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
		'public' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		// checking that the file is imported, etc
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
		'ImportManager' => array(
			'className' => 'ImportManager',
			'foreignKey' => 'import_manager_id',
		),
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Vector' => array(
			'className' => 'Vector',
			'joinTable' => 'imports_vectors',
			'foreignKey' => 'import_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'ImportsVector',
		),
		'TempVector' => array(
			'className' => 'TempVector',
			'joinTable' => 'temp_imports_vectors',
			'foreignKey' => 'import_id',
			'associationForeignKey' => 'temp_vector_id',
			'unique' => 'keepExisting',
			'with' => 'TempImportsVector',
		),
	);
	
	public $actsAs = array('Tags.Taggable', 'Importer');
	
	
	// define the fields that can be searched
	public $searchFields = array(
		'Import.name',
		'Import.filename',
		'Import.mimetype',
		'Import.type',
		'Import.sha1',
		'ImportManager.name',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('public');
	
	// set in beforeSave/beforeDelete and used in afterSave/afterDelete
	public $file_info = false;
	
	// set in beforeDelete and used in afterDelete
	public $delete_file = false;
	
	// track the import managers
	public $import_managers = array();
	
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
				if($this_dir == 'imports') break;
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
	
	public function checkSha1Exists($sha1 = false)
	{
		if(!$sha1) return false;
		
		return $this->field('id', array('sha1' => $sha1));
	}
	
	public function processFile($import_manager_id = false, $file_path = false)
	{
		// make sure this import manager exists
		if(!$import_manager_id)
		{
			$this->modelError = __('Unknown Import Manager ID');
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		if(!$file_path)
		{
			$this->modelError = __('Unknown File Path');
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		// file exists
		if(!is_file($file_path))
		{
			$this->modelError = __('File doesn\'t exist. path: %s', $file_path);
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		// able to read file
		if(!is_readable($file_path))
		{
			$this->modelError = __('File isn\'t readable. path: %s', $file_path);
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		// check the sha1 to make sure it doesn't already exist
		$sha1 = sha1_file($file_path);
		if($this->checkSha1Exists($sha1))
		{
			$this->modelError = __('File already imported according to the sha1. file_path: %s - sha1: %s', $file_path, $sha1);
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		// get the import manager
		$import_manager = false;
		if(isset($import_managers[$import_manager_id]))
		{
			$import_manager = $import_managers[$import_manager_id];
		}
		else
		{
			$import_manager = $import_managers[$import_manager_id] = $this->ImportManager->read(null, $import_manager_id);
		}
		
		if(!$import_manager)
		{
			$this->modelError = __('Unknown Import Manager');
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		$filename = basename($file_path);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($finfo, $file_path);
		finfo_close($finfo);
		
		// track the import data for when we add an import later
		$import_data = array(
			'import_manager_id' => $import_manager_id,
			'name' => $filename,
			'filename' => $filename,
			'mimetype' => $mimetype,
			'size' => filesize($file_path),
			'type' => pathinfo($filename, PATHINFO_EXTENSION),
			'sha1' => $sha1,
			
		);
		
		// set the Importer Behavior settings from the import manager settings
		$importer_settings = array(
			'source_key' => $filename,
			'parser' => $import_manager['ImportManager']['parser'],
			'vector_fields' => array(),
		);
		
		if($import_manager['ImportManager']['parser'] == 'csv')
		{
			$importer_settings['vector_fields'] = array_keys($import_manager['ImportManager']['csv_fields']);
		}
		
		// get the vectors as defined in the import manager
		$settings = $this->Importer_setConfig($importer_settings);
		$vector_count = 0;
		if(!$vectors = $this->Importer_extractItemsFromFile($file_path, $mimetype))
		{
			$this->modelError = __('Unable to find any vectors in the file: %s - sha1: %s', $file_path, $sha1);
			$this->shellOut($this->modelError, 'imports', 'warning');
			return $vector_count;
		}
		else
		{
			$vector_count = count($vectors);
			$this->modelError = __('Found %s vectors in the file: %s - sha1: %s', $vector_count, $file_path, $sha1);
			$this->shellOut($this->modelError, 'imports', 'info');
		}
		
		// mark as reviewed if auto_reviewed is check in the import manager
		if($import_manager['ImportManager']['auto_reviewed'])
		{
			$import_data['reviewed'] = date('Y-m-d H:i:s');
		}
		
		// save the import
		$this->create();
		$this->data = $import_data;
		if(!$this->save($this->data))
		{
			$this->modelError = __('Unable to save the Import record from the file: %s - sha1: %s - Import Manager: (%s) %s', $file_path, $sha1, $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
			$this->shellOut($this->modelError, 'imports', 'error');
			return false;
		}
		
		// copy the file over to the proper place
		$paths = $this->paths($this->id, true, $filename);
		if($paths['sys'])
		{
			umask(0);
			if (!copy($file_path, $paths['sys'])) 
			{
				$this->modelError = __('Unable to copy the file from: %s - to: %s - Import Manager: (%s) %s', $file_path, $paths['sys'], $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
				$this->shellOut($this->modelError, 'imports', 'error');
				return false;
			}
			chmod($paths['sys'], 0777);
		}
		
		// process the vectors
		if($import_manager['ImportManager']['auto_reviewed'])
		{
			$data = array(
				'ImportsVector' => array(
					'vectors' => $vectors,
					'import_id' => $this->id,
					'vector_settings' => $import_manager['ImportManager']['csv_fields'],
					'source' => 'import',
					'subsource' => $filename,
				),
			);
			
			if(!$this->ImportsVector->add($data))
			{
				$this->modelError = __('Unable to add the Temp Vectors from the file: %s - Import Id: %s - Import Manager: (%s) %s', $file_path, $this->id, $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
				$this->shellOut($this->modelError, 'imports', 'error');
				return false;
			}
		}
		else
		{
			$data = array(
				'TempImportsVector' => array(
					'temp_vectors' => $vectors,
					'import_id' => $this->id,
					'vector_settings' => $import_manager['ImportManager']['csv_fields'],
					'source' => 'import',
					'subsource' => $filename,
				),
			);
			
			if(!$this->TempImportsVector->add($data))
			{
				$this->modelError = __('Unable to add the Temp Vectors from the file: %s - Import Id: %s - Import Manager: (%s) %s', $file_path, $this->id, $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
				$this->shellOut($this->modelError, 'imports', 'error');
				return false;
			}
		}
		return $vector_count;
	}
	
	public function reviewed($id = false)
	{
		$this->recursive = 1;
		$this->id = $id;
		$import = $this->read(null, $id);
		if(!$import) return false;
		
		// build the save array
		$data = array();
		
		// add the reviewed date
		$data['Import']['reviewed'] = date('Y-m-d H:i:s');
		
		// save the vectors and associations
		if(isset($import['TempVector']) and count($import['TempVector']))
		{
			if(!$this->ImportsVector->reviewed($this->id, $import['TempVector']))
			{
				$this->reviewError .= "\n". __(' The Vectors weren\'t transfered correctly.');
				return false;
			}
		}
		
		// update the import to be marked as reviewed
		$this->data = $data;
		
		return $this->save($this->data);
	}
	
	public function listImportRelatedIds($object_id = false, $org_group_id = false, $user_id = false, $admin = false)
	{
		/////////// this import's vectors
		if(!$object_vector_ids = $this->ImportsVector->listVectorIds($object_id, $org_group_id, $user_id, $admin))
		{
			return false;
		}
		
		$contain = array('Vector');
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $object_vector_ids,
			'ImportsVector.import_id !=' => $object_id,
			'Vector.bad' => 0,
		);
		
		$options = array(
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => $conditions,
			'fields' => array('ImportsVector.import_id', 'ImportsVector.import_id'),
		);
		
		if(isset($this->cacher) and $this->cacher) 
		{
			$options['cacher'] = true;
			$options['cacher_path'] = $this->alias.'::listImportRelated('.$object_id.')';
		}
		
		return $this->ImportsVector->find('list', $options);
	}
	
	public function sqlImportRelated($import_id = false, $admin = false)
	{
	/*
	 * Import related to another import
	 * Builds the complex query for the conditions
	 */
		if(!$import_id) return false;
		
		// get the vector ids from this import
		$this->ImportsVector->recursive = 0;
		$db = $this->ImportsVector->getDataSource();
		
		$subQuery_conditions = array('ImportsVector1.import_id' => $import_id, 'Vector1.bad' => 0);
		if(!$admin)
		{
			$subQuery_conditions['ImportsVector1.active'] = 1;
		}
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector1`.`vector_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery_conditions,
				'alias'		 => '`ImportsVector1`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector1`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector1`.`id` = `ImportsVector1`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		$subQuery = ' `ImportsVector2`.`vector_id` IN (' . $subQuery . ') ';
		
		$subQuery2_conditions = array('Vector2.bad' => 0, $subQuery);
		if(!$admin)
		{
			$subQuery2_conditions['ImportsVector2.active'] = 1;
		}
		
		// get the import_ids from this model that share the came vectors,
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`ImportsVector2`.`import_id`'),
				'table'		 => $db->fullTableName($this->ImportsVector),
				'conditions' => $subQuery2_conditions,
				'alias'		 => '`ImportsVector2`',
				'limit'		 => null,
				'offset'	 => null,
				'order'		 => null,
				'group'		 => null,
				'joins'		 => array(
					array(
						'alias' => '`Vector2`',
						'table' => 'vectors',
						'type' => 'LEFT',
						'conditions' => '`Vector2`.`id` = `ImportsVector2`.`vector_id`'
					),
				),
			),
			$this->ImportsVector
		);
		// get the imports themselves
		
		$subQuery2 = ' `Import`.`id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		return $subQuery2Expression;
	}
}
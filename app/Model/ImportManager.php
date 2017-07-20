<?php
App::uses('AppModel', 'Model');
/**
 * ImportManager Model
 *
 * @property TempImport $TempImport
 * @property Import $Import
 */
class ImportManager extends AppModel 
{

	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => 'create',
			),
			'isUnique' => array(
				'rule'    => array('isUnique'),
				'message' => 'Must be unique, this Name already exists.',
				'required' => true,
			),
		),
		'key' => array(
			'notBlank' => array(
				'rule'    => 'Rule_alphaNumericUnderscore',
				'message' => 'Key can only be letters, numbers, dash and underscore',
				'required' => 'create',
			),
			'isUnique' => array(
				'rule'    => array('isUnique'),
				'message' => 'Must be unique, this Key already exists.',
				'required' => true,
			),
		),
		'parser' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Select the Parser to use.',
				'required' => 'create',
			),
		),
		'location' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Select how we will get the files.',
				'required' => 'create',
			),
		),
		'local_path' => array(
			'notBlank' => array(
				'rule'    => 'Rule_LocalPath',
				'message' => 'Please ender the local path to the files to imported.',
				'required' => 'create',
			),
			'pathexists' => array(
				'rule'    => 'Rule_LocalPathExists',
				'message' => 'The directory doesn\'t exist.',
				'required' => 'create',
			),
			'pathwritable' => array(
				'rule'    => 'Rule_LocalPathWritable',
				'message' => 'The directory isn\'t writable.',
				'required' => 'create',
			),
		),
	);
	
	public $hasMany = array(
		'Import' => array(
			'className' => 'Import',
			'foreignKey' => 'import_manager_id',
			'dependent' => true,
		),
		'ImportManagerLog' => array(
			'className' => 'ImportManagerLog',
			'foreignKey' => 'import_manager_id',
			'dependent' => true,
		),
	);
	
	public $belongsTo = array(
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	public $actsAs = array('Tags.Taggable', 'Importer');
	
	// define the fields that can be searched
	public $searchFields = array(
		'ImportManager.name',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active', 'cron', 'auto_reviewed');
	
	// used with the cron job cronUpdate()
	public $cronStats = array('imported' => 0, 'duplicates' => 0, 'failed' => 0);
	
	// used with the cron job cronUpdate()
	public $final_results = false;
	
	
	// Used to decode, and encode the csv column settings
	public function beforeSave($options = array())
	{
		if(isset($this->data[$this->alias]['csv_fields']) and $this->data[$this->alias]['csv_fields'])
		{
			$new = array();
			foreach($this->data[$this->alias]['csv_fields'] as $csv_field)
			{
				$field_name = $csv_field['csv_field'];
				unset($csv_field['csv_field']);
				$new[$field_name] = $csv_field;
			}
			$this->data[$this->alias]['csv_fields'] = $new;
			$this->data[$this->alias]['csv_fields'] = $this->convertCsvFields($this->data[$this->alias]['csv_fields'], true);
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		foreach($results as $i => $result)
		{
			if(isset($result['ImportManager']['csv_fields']))
			{
				$results[$i]['ImportManager']['csv_fields'] = $this->convertCsvFields($result['ImportManager']['csv_fields']);
			}
		}
		
		return parent::afterFind($results, $primary);
	}
	
	
//// Function used to run an import manager in the cron
	public function cronUpdate()
	{
		$this->final_results = __('Starting Import Manager Updates');
		$this->shellOut($this->final_results, 'imports', 'info');
		
		// list of import managers that are set to run in the cron
		$import_managers = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'ImportManager.cron' => true,
				'ImportManager.active' => true,
			),
		));
		
		$this->final_results = __('Found %s active Import Managers to process', count($import_managers));
		$this->shellOut($this->final_results, 'imports', 'info');
		
		$time_start = microtime(true);
		
		foreach($import_managers as $import_manager)
		{
			$this->final_results = __('Processing Import Manager: (id: %s) %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
			$this->shellOut($this->final_results, 'imports', 'info');
			
			$this->processImport($import_manager['ImportManager']['id'], $import_manager);
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Processed %s Import Managers. - took: %s seconds', count($import_managers), $time);
		$this->shellOut($this->final_results, 'imports', 'info');
	}
	
	// used when someone manually pushes the update button on the website
	// also gets called from the cron function
	public function processImport($import_manager_id = false, $import_manager = false)
	{
		if(!$import_manager and $import_manager_id)
		{
			$import_manager = $this->read(null, $import_manager_id);
		}
		
		// dealing with a local path
		if($import_manager['ImportManager']['local_path'])
		{
			return $this->run_local($import_manager_id, $import_manager);
		}
		// dealing with a remote url
		elseif($import_manager['ImportManager']['url'])
		{
			return $this->run_url($import_manager_id, $import_manager);
		}
		return true;
	}
	
	/// runs the import manager for local paths and updates/add the imports 
	public function run_local($import_manager_id = false, $import_manager = false, $iml_id = false)
	{
		$time_start = microtime(true);
		
		if(!$import_manager and $import_manager_id)
		{
			$import_manager = $this->read(null, $import_manager_id);
		}
		
		// begin logging this update instance
		if(!$iml_id)
		{
			$iml_id = $this->ImportManagerLog->add($import_manager_id);
		}
		else
		{
			$this->final_results = __('Processing Import Manager: (%s) %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
			$this->shellOut($this->final_results, 'imports', 'info');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results));
		}
		
		// make sure we have the import manager details
		if(!$import_manager)
		{
			$this->modelError = __('Unable to find the details for this Import Manager');
			$this->cronStats['failed']++;
			$this->shellOut($this->modelError, 'imports', 'error');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
			return false;
		}
		
		// get the path
		$path = false;
		if(isset($import_manager['ImportManager']['local_path']) and $import_manager['ImportManager']['local_path'])
		{
			$path = $import_manager['ImportManager']['local_path'];
		}
		else
		{
			$this->modelError = __('Unable to find the path for this Import Manager');
			$this->cronStats['failed']++;
			$this->shellOut($this->modelError, 'imports', 'error');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
			return false;
		}
		
		// check the path exists
		if(!is_dir($path))
		{
			$this->modelError = __('The path doesn\'t exist: %s', $path);
			$this->cronStats['failed']++;
			$this->shellOut($this->modelError, 'imports', 'error');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
			return false;
		}
		
		// check the path is writable
		if(!is_writable($path))
		{
			$this->modelError = __('Unable to write to the path: %s', $path);
			$this->cronStats['failed']++;
			$this->shellOut($this->modelError, 'imports', 'error');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
			return false;
		}
		
		// check to make sure the subdirectories for processing files are there
		$paths = array(
			'imported' => $path. DS. 'imported',
			'duplicates' => $path. DS. 'duplicates',
			'processing' => $path. DS. 'processing',
			'empty' => $path. DS. 'empty',
			'failed' => $path. DS. 'failed',
		);
		
		// check the paths
		umask(0); // so we get the full permission set
		$path_fail = false;
		foreach($paths as $k => $v)
		{
			if(!is_dir($v))
			{
				$this->final_results = __('Attempting to create path: %s', $v);
				$this->shellOut($this->final_results, 'imports', 'info');
				
				if(!mkdir($v, 0777))
				{
					$this->modelError = __('Unable to create the path: %s', $v);
					$this->shellOut($this->modelError, 'imports', 'error');
					$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
					$path_fail = true;
				}
			}
			if(!is_writable($v))
			{
				$this->modelError = __('Unable to write to the path: %s', $v);
				$this->shellOut($this->modelError, 'imports', 'error');
				$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
				$path_fail = true;
			}
		}
		
		if($path_fail)
		{
			$this->modelError = __('Unable to create/access to the paths in: %s ', $path);
			$this->cronStats['failed']++;
			$this->shellOut($this->modelError, 'imports', 'error');
			$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError));
			return false;
		}
		
		// read the files in this directory
		$items = glob ("$path/*");
		$files = array();
		
		foreach($items as $item)
		{
			if(is_file($item) and !is_dir($item))
			{
				$files[] = $item;
			}
		}
		
		$success = 0;
		if(!count($files)) $success = 1;
		
		$this->final_results = __('Found %s files to process for the Import Manager: (%s) %s', count($files), $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name']);
		$this->shellOut($this->final_results, 'imports', 'info');
		$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results, 'success' => $success));
		
		
		// process each file
		$count_failed = $count_duplicate = $count_empty = $count_processed = 0;
		foreach($files as $file_path)
		{
			// check to see if this file has been processed
			$sha1 = sha1_file($file_path);
			$filename = basename($file_path);
			
			if($this->Import->checkSha1Exists($sha1))
			{
				// if so, document it, and move the file to the duplicate directory
				$this->final_results = __('Duplicate file for Import Manager: (%s) %s - file_path: %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path, $sha1);
				$this->shellOut($this->final_results, 'imports', 'warning');
				$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results, 'num_duplicate' => 1));
				
			
				// move the file to the processing directory
				$file_path_duplicates = $paths['duplicates']. DS. $filename;
				if(!rename($file_path, $file_path_duplicates))
				{
					$this->modelError = __('Error for Import Manager: (%s) %s - unable to move %s to %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path, $file_path_processing, $sha1);
					$this->shellOut($this->modelError, 'imports', 'error');
					$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
				}
				
				$count_duplicate++;
				continue;
			}
			
			// move the file to the processing directory
			$file_path_processing = $paths['processing']. DS. $filename;
			if(!rename($file_path, $file_path_processing))
			{
				$this->modelError = __('Error for Import Manager: (%s) %s - unable to move %s to %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path, $file_path_processing, $sha1);
				$this->shellOut($this->modelError, 'imports', 'error');
				$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
				
				$count_failed++;
				continue;
			}
			
			// call the Import Model to process the file
			$results = $this->Import->processFile($import_manager['ImportManager']['id'], $file_path_processing);
			if($results !== false)
			{
				if($results === 0)
				{
					// move to the imported directory
					$file_path_empty = $paths['empty']. DS. $filename;
					if(!rename($file_path_processing, $file_path_empty))
					{
						$this->modelError = __('Error for Import Manager: (%s) %s - unable to move %s to %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path_processing, $file_path_empty, $sha1);
						$this->shellOut($this->modelError, 'imports', 'error');
						$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
						
						$count_failed++;
						continue;
					}
					$count_empty++;
					$this->final_results = __('Empty file for Import Manager: (%s) %s - file_path: %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path_empty, $sha1);
					$this->shellOut($this->final_results, 'imports', 'info');
					$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results, 'num_empty' => 1, 'success' => 1));
				}
				else
				{
					// move to the imported directory
					$file_path_imported = $paths['imported']. DS. $filename;
					if(!rename($file_path_processing, $file_path_imported))
					{
						$this->modelError = __('Error for Import Manager: (%s) %s - unable to move %s to %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path_processing, $file_path_imported, $sha1);
						$this->shellOut($this->modelError, 'imports', 'error');
						$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
						
						$count_failed++;
						continue;
					}

					$count_processed++;
					$this->final_results = __('Processed file for Import Manager: (%s) %s - vector count: %s - file_path: %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $results, $file_path_imported, $sha1);
					$this->shellOut($this->final_results, 'imports', 'info');
					$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results, 'num_added' => 1, 'success' => 1));
				}
			}
			// failed to process the file
			else
			{
				$this->modelError = __('Error for Import Manager: (%s) %s - unable to process file: %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path_processing, $sha1);
				$this->shellOut($this->modelError, 'imports', 'error');
				$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
				
				// move to the failed directory
				$file_path_failed = $paths['failed']. DS. $filename;
				if(!rename($file_path_processing, $file_path_failed))
				{
					$this->modelError = __('Error for Import Manager: (%s) %s - unable to move %s to %s - sha1: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $file_path_processing, $file_path_failed, $sha1);
					$this->shellOut($this->modelError, 'imports', 'error');
					$this->ImportManagerLog->update($iml_id, array('msg' => $this->modelError, 'num_failed' => 1));
				}
				
				$count_failed++;
			}
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$this->final_results = __('Processed files for Import Manager: (%s) %s - took: %s seconds - processed: %s - empty: %s - duplicates: %s - failed: %s', $import_manager['ImportManager']['id'], $import_manager['ImportManager']['name'], $time, $count_processed, $count_empty, $count_duplicate, $count_failed);
		$this->shellOut($this->final_results, 'imports', 'info');
		$this->ImportManagerLog->update($iml_id, array('msg' => $this->final_results));
		return true;
	}
	
	/// runs the import manager for urls and updates/add the imports 
	public function run_url()
	{
	}
	
////// Support functions
	public function convertCsvFields($fields = false, $arrayToJson = false)
	{
		if(!$fields) return array();
		
		if($arrayToJson) return json_encode($fields);
		else return $this->Importer_objectToArray(json_decode($fields));
	}
	
////// Validation rules
	public function Rule_alphaNumericUnderscore($check)
	{
		$value = array_values($check);
		$value = $value[0];
		
		return preg_match('|^[0-9a-zA-Z_]*$|', $value);
	}
	
	public function Rule_LocalPath($check)
	{
		// can be empty as long as we're not using the local path location
		if(isset($this->data[$this->alias]['location']) and $this->data[$this->alias]['location'] == 'local')
		{
			$value = array_values($check);
			$value = $value[0];
			$value = trim($value);
			
			if($value{0} !== DS)
			{
				return false;
			}
			
			return preg_match('|^[0-9a-zA-Z/\\\]*$|', $value);
		}
		return true;
	}
	
	public function Rule_LocalPathExists($check)
	{
		// can be empty as long as we're not using the local path location
		if(isset($this->data[$this->alias]['location']) and $this->data[$this->alias]['location'] == 'local')
		{
			$value = array_values($check);
			$value = $value[0];
			$value = trim($value);
			
			return is_dir($value);
		}
		return true;
	}
	
	public function Rule_LocalPathWritable($check)
	{
		// can be empty as long as we're not using the local path location
		if(isset($this->data[$this->alias]['location']) and $this->data[$this->alias]['location'] == 'local')
		{
			$value = array_values($check);
			$value = $value[0];
			$value = trim($value);
			
			return is_writable($value);
		}
		return true;
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * Dump Model
 *
 * @property DumpsDetail $DumpsDetail
 * @property User $User
 * @property Vector $Vector
 */
class Dump extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		// checking that the file is dumped, etc
		'file' => array(
			'exists' => array(
				'rule' => array('RuleFile'),
				'message' => 'There was an error with this file',
				'last'    => true
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'DumpsDetail' => array(
			'className' => 'DumpsDetail',
			'foreignKey' => 'dump_id',
			'dependent' => true,
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Vector' => array(
			'className' => 'Vector',
			'joinTable' => 'dumps_vectors',
			'foreignKey' => 'dump_id',
			'associationForeignKey' => 'vector_id',
			'unique' => 'keepExisting',
			'with' => 'DumpsVector',
		)
	);
	
	public $actsAs = array('Tags.Taggable');
	
	
	// define the fields that can be searched
	public $searchFields = array(
		'Dump.name',
		'Dump.filename',
		'Dump.mimetype',
		'Dump.type',
		'Dump.md5',
		'DumpsDetail.allvectors',
		'DumpsDetail.dumptext',
	);
	
	// set in beforeSave/beforeDelete and used in afterSave/afterDelete
	public $file_info = false;
	
	// set in beforeDelete and used in afterDelete
	public $delete_file = false;

	public function beforeSave($options = array())
	{
		// see if the file dumped ok
		// if there is no id, then it's being created
		if($this->data['Dump'] and !$this->id and !isset($this->data['Dump']['id']))
		{
			// make sure [file] exists
			if(isset($this->data['Dump']['file']))
			{
				$this->data['Dump']['filename'] = $this->data['Dump']['file']['name'];
				$this->data['Dump'] = array_merge($this->data['Dump']['file'], $this->data['Dump']);
				unset($this->data['Dump']['file']);
			}
			
			// make sure the user is associated with this dump when adding a new one
			if(!isset($this->data['Dump']['user_id']))
			{
				$this->data['Dump']['user_id'] = AuthComponent::user('id');
			}
			
			// set some more variables based on the file info
			// defaults, incase there is no file
			$this->data['Dump']['mimetype'] = $this->data['Dump']['type'];
			$this->data['Dump']['size'] = $this->data['Dump']['size'];
			$this->data['Dump']['md5'] = false;
			
			// if there is a file
			if($this->data['Dump']['error'] == 0)
			{
				$this->data['Dump']['type'] = pathinfo($this->data['Dump']['filename'], PATHINFO_EXTENSION);
				$this->data['Dump']['md5'] = md5_file($this->data['Dump']['tmp_name']);
			}
			
			// for some reason *.log files are coming in as application/octet-stream.
			// (see also dump files)
			if(strtolower($this->data['Dump']['type']) == 'log')
			{
				$this->data['Dump']['mimetype'] = 'text/plain';
			}
			
			if(isset($this->data['DumpsDetail']['dumptext']))
			{
				$this->data['DumpsDetail']['dumptext'] .= ' ';
			}
			
			$this->file_info = $this->data['Dump'];
		}
		
		return parent::beforeSave($options);
	}

	public function afterSave($created = false, $options = array())
	{
/*
 * Don't save the contents of the file in the database
		if($this->data['Dump']['mimetype'] == 'text/plain')
		{
			Cache::write('DumpsDetail.dumptext', $this->fileContent_text($this->data['Dump']['tmp_name']), 'file');
		}
		elseif($this->data['Dump']['mimetype'] == 'application/pdf')
		{
			Cache::write('DumpsDetail.dumptext', $this->fileContent_pdf($this->data['Dump']['tmp_name']), 'file');
		}
*/
		
		// move the file to it's proper location
		$file_path = false;
		if($this->file_info and isset($this->file_info['error']) and $this->file_info['error'] == 0)
		{
			$file_info = $this->file_info;
			$paths = $this->paths($this->data['Dump']['id'], true, $file_info['filename']);
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
		$_vectors = array();
		
		if(isset($this->data['DumpsDetail']['dumptext']))
		{
			$all_vectors = $this->extractItems($this->data['DumpsDetail']['dumptext']);
			
			// clean them up and format them for a saveMany()
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
			}
		}
		
		if(isset($this->data['Dump']['id']) and $file_path)
		{
			$mimetype = false;
			if(isset($this->data['Dump']['mimetype']))
			{
				$mimetype = $this->data['Dump']['mimetype'];
			}
			
			$all_vectors = $this->extractItemsFromFile($paths['sys'], $mimetype);
			
			// clean them up and format them for a saveMany()
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
			}
		}
		
		// if vectors were found, find the ids from the Vector Model to track in the DumpsVector Model
		if($_vectors)
		{
			// save this in the dumpdetails
			$this->data['DumpsDetail']['allvectors'] = $this->data['DumpsDetail']['allvectors'] = implode("\n", $_vectors);
			
			Cache::write('DumpsDetail.allvectors', implode("\n", $_vectors), 'file');
			
			// associate only with existing vectors
			if($vector_ids = $this->Vector->find('list', array(
				'fields' => array('Vector.vector', 'Vector.id'),
				'conditions' => array('Vector.bad' => 0, 'Vector.vector' => $_vectors),
			)))
			{
				// build the proper save array
				$data = array();
				foreach($vector_ids as $vector => $vector_id)
				{
					$data[] = array('dump_id' => $this->data['Dump']['id'], 'vector_id' => $vector_id);
					
					// track only the new vectors
					if(isset($_vectors[$vector]))
					{
						unset($_vectors[$vector]);
					}
				}
				// save the association
				$this->DumpsVector->saveMany($data);
			}
			
			Cache::write('DumpsDetail.newvectors', implode("\n", $_vectors), 'file');
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
				if($this_dir == 'dumps') break;
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
	
	// Validation Rules
	public function RuleFile($file = false)
	{
		if(!$file) return false;
		if(!isset($file['file']['error'])) return false;
		if($file['file']['error'] === 0) return true;
		
		// allows for no file to be uploaded
		if($file['file']['error'] === 4) return true;
		
		$max_dump = (int)(ini_get('dump_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$dump_mb = min($max_dump, $max_post, $memory_limit);
		
		// change the validation message based on what the file error is
		$errorMessage = '';
		switch ($file['file']['error'])
		{
			case 1: 
			case 2: 
				$errorMessage = __('The dumped file exceeds allowed size of %sM', $dump_mb); 
				break;
			case 3: 
				$errorMessage = __('The dumped file was only partially dumped.'); 
				break;
			case 4: 
				$errorMessage = __('No file was dumped.'); 
				break;
			case 6: 
				$errorMessage = __('Missing a temporary dump folder.'); 
				break;
			case 7: 
				$errorMessage = __('Failed to write file to disk.'); 
				break;
			
		}
		$this->validationErrors['file'][] = $errorMessage;
		return false;
	}

}

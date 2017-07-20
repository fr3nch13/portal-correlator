<?php
App::uses('AppModel', 'Model');
/**
 * Signature Model
 *
 * @property SignatureSource $SignatureSource
 * @property Category $Category
 * @property Report $Report
 */
class Signature extends AppModel 
{
	public $displayField = 'name';
	
	public $validate = array(
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'signature_type' => array(
			'required' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $belongsTo = array(
		'SignatureSource' => array(
			'className' => 'SignatureSource',
			'foreignKey' => 'signature_source_id',
		),
		'SignatureAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
	);
	
	public $hasOne = array(
		'YaraSignature' => array(
			'className' => 'YaraSignature',
			'foreignKey' => 'signature_id',
			'dependent' => true,
		),
		'SnortSignature' => array(
			'className' => 'SnortSignature',
			'foreignKey' => 'signature_id',
			'dependent' => true,
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Category' => array(
			'className' => 'Category',
			'joinTable' => 'categories_signatures',
			'foreignKey' => 'signature_id',
			'associationForeignKey' => 'category_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesSignature',
		),
		'Report' => array(
			'className' => 'Report',
			'joinTable' => 'reports_signatures',
			'foreignKey' => 'signature_id',
			'associationForeignKey' => 'report_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsSignature',
		),
		'TempCategory' => array(
			'className' => 'TempCategory',
			'joinTable' => 'categories_signatures',
			'foreignKey' => 'signature_id',
			'associationForeignKey' => 'temp_category_id',
			'unique' => 'keepExisting',
			'with' => 'CategoriesSignature',
		),
		'TempReport' => array(
			'className' => 'TempReport',
			'joinTable' => 'reports_signatures',
			'foreignKey' => 'signature_id',
			'associationForeignKey' => 'temp_report_id',
			'unique' => 'keepExisting',
			'with' => 'ReportsSignature',
		)
	);
	
	// add plugins and other behaviors
	public $actsAs = array(
		'Utilities.Signature',
		'Tags.Taggable',
    );
    
	// define the fields that can be searched
	public $searchFields = array(
		'Signature.name',
		'Signature.signature',
	);
	
	public $toggleFields = array('active');
	
	public $modelRedirect = false;
	
	public function beforeSave($options = array())
	{
			
		// check the signature source 
		if(isset($this->data[$this->alias]['signature_source']))
		{
			$this->data[$this->alias]['signature_source_id'] = $this->SignatureSource->add($this->data[$this->alias]['signature_source']);
			unset($this->data[$this->alias]['signature_source']);
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
			
		// update all related xrefs with this signature_source
		if(isset($this->data[$this->alias]['signature_source_id']))
		{	
			// update all related xrefs with this signature_source
			$this->CategoriesSignature->updateAll(
				array('CategoriesSignature.signature_source_id' => $this->data[$this->alias]['signature_source_id']),
				array('CategoriesSignature.signature_id' => $this->data[$this->alias]['id'])
			);
			$this->ReportsSignature->updateAll(
				array('ReportsSignature.signature_source_id' => $this->data[$this->alias]['signature_source_id']),
				array('ReportsSignature.signature_id' => $this->data[$this->alias]['id'])
			);
			
			// update all parsed signatures
			$this->YaraSignature->updateAll(
				array('YaraSignature.signature_source_id' => $this->data[$this->alias]['signature_source_id']),
				array('YaraSignature.signature_id' => $this->data[$this->alias]['id'])
			);
			$this->SnortSignature->updateAll(
				array('SnortSignature.signature_source_id' => $this->data[$this->alias]['signature_source_id']),
				array('SnortSignature.signature_id' => $this->data[$this->alias]['id'])
			);
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function add($data = array())
	{
		$id = false;
		if(!$data = $this->formatData($data))
		{
			if(!$this->modelError) $this->modelError = __('Unable to create the %s', __('Signature'));
			return false;
		}
		
		// data should always return a list of signatures in the format: array('snort' => array([snort signatures]), 'yara' => array([yara signatures]))
		// add the yara signatures
		$ids = array();
		foreach($data['yara'] as $signature)
		{
			// save the signature record to get an id
			if(!$id = $this->idByHash($signature[$this->alias]['hash']))
			{
				$this->create();
				$this->data[$this->alias] = $signature[$this->alias];
				if(!$this->save($this->data))
				{
					$this->modelError = __('Unable to save this %s', __('Signatures'));
					continue;
				}
				$id = $this->id;
				
			}
			$this->id = $id;
			
			// save the xref references
			if(isset($signature[$this->alias]['category_id']) and $signature[$this->alias]['category_id'])
			{
				$xref_data = array(
					'category_id' => $signature[$this->alias]['category_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->CategoriesSignature->add($xref_data, 'category_id');
				
				$this->modelRedirect = array('controller' => 'categories', 'action' => 'view', $signature[$this->alias]['category_id'], '#' => 'ui-tabs-12');
			}
			elseif(isset($signature[$this->alias]['temp_category_id']) and $signature[$this->alias]['temp_category_id'])
			{
				$xref_data = array(
					'temp_category_id' => $signature[$this->alias]['temp_category_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->CategoriesSignature->add($xref_data, 'temp_category_id');
				$this->modelRedirect = array('controller' => 'temp_categories', 'action' => 'view', $signature[$this->alias]['temp_category_id'], '#' => 'ui-tabs-3');
			}
			elseif(isset($signature[$this->alias]['report_id']) and $signature[$this->alias]['report_id'])
			{
				$xref_data = array(
					'report_id' => $signature[$this->alias]['report_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->ReportsSignature->add($xref_data, 'report_id');
				$this->modelRedirect = array('controller' => 'reports', 'action' => 'view', $signature[$this->alias]['report_id'], '#' => 'ui-tabs-12');
			}
			elseif(isset($signature[$this->alias]['temp_report_id']) and $signature[$this->alias]['temp_report_id'])
			{
				$xref_data = array(
					'temp_report_id' => $signature[$this->alias]['temp_report_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->ReportsSignature->add($xref_data, 'temp_report_id');
				$this->modelRedirect = array('controller' => 'temp_reports', 'action' => 'view', $signature[$this->alias]['temp_report_id'], '#' => 'ui-tabs-3');
			}
			
			// save the yara signature
			if(isset($signature['YaraSignature']))
			{
				$signature['YaraSignature']['signature_id'] = $id;
				$this->YaraSignature->add($signature);
			}
			
			$ids[$id] = $id;
		}
		
		foreach($data['snort'] as $signature)
		{
			// save the signature record to get an id
			if(!$id = $this->idByHash($signature[$this->alias]['hash']))
			{
				$this->create();
				$this->data[$this->alias] = $signature[$this->alias];
				if(!$this->save($this->data))
				{
					$this->modelError = __('Unable to save this %s', __('Signatures'));
					continue;
				}
				$id = $this->id;
			}
			$this->id = $id;
			
			// save the xref references
			if(isset($signature[$this->alias]['category_id']) and $signature[$this->alias]['category_id'])
			{
				$xref_data = array(
					'category_id' => $signature[$this->alias]['category_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->CategoriesSignature->add($xref_data, 'category_id');
				
				$this->modelRedirect = array('controller' => 'categories', 'action' => 'view', $signature[$this->alias]['category_id'], '#' => 'ui-tabs-12');
			}
			elseif(isset($signature[$this->alias]['temp_category_id']) and $signature[$this->alias]['temp_category_id'])
			{
				$xref_data = array(
					'temp_category_id' => $signature[$this->alias]['temp_category_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->CategoriesSignature->add($xref_data, 'temp_category_id');
				$this->modelRedirect = array('controller' => 'temp_categories', 'action' => 'view', $signature[$this->alias]['temp_category_id'], '#' => 'ui-tabs-3');
			}
			elseif(isset($signature[$this->alias]['report_id']) and $signature[$this->alias]['report_id'])
			{
				$xref_data = array(
					'report_id' => $signature[$this->alias]['report_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->ReportsSignature->add($xref_data, 'report_id');
				$this->modelRedirect = array('controller' => 'reports', 'action' => 'view', $signature[$this->alias]['report_id'], '#' => 'ui-tabs-12');
			}
			elseif(isset($signature[$this->alias]['temp_report_id']) and $signature[$this->alias]['temp_report_id'])
			{
				$xref_data = array(
					'temp_report_id' => $signature[$this->alias]['temp_report_id'],
					'signature_id' => $id,
					'signature_source_id' => $signature[$this->alias]['signature_source_id'],
				);
				$this->ReportsSignature->add($xref_data, 'temp_report_id');
				$this->modelRedirect = array('controller' => 'temp_reports', 'action' => 'view', $signature[$this->alias]['temp_report_id'], '#' => 'ui-tabs-3');
			}
			
			// save the snort signature
			if(isset($signature['SnortSignature']))
			{
				$signature['SnortSignature']['signature_id'] = $id;
				$this->SnortSignature->add($signature);
			}
			$ids[$id] = $id;
		}
		
		return $ids;
	}
	
	public function update($data = array())
	{
		if(!$data = $this->formatData($data))
		{
			if(!$this->modelError) $this->modelError = __('Unable to create the %s', __('Signature'));
			return false;
		}
		
		// data should always return a list of signatures in the format: array('snort' => array([snort signatures]), 'yara' => array([yara signatures]))
		// add the yara signatures
		$ids = array();
		foreach($data['yara'] as $signature)
		{
			// save the signature record to get an id
			$this->id = $signature[$this->alias]['id'];
			$this->data[$this->alias] = $signature[$this->alias];
			if(!$this->save($this->data))
			{
				$this->modelError = __('Unable to update this %s', __('Signature'));
				continue;
			}
			$id = $this->id;
			
			// save the yara signature
			if(isset($signature['YaraSignature']))
			{
				$signature['YaraSignature']['signature_id'] = $id;
				$this->YaraSignature->update($id, $signature);
			}
			
			$ids[$id] = $id;
		}
		
		foreach($data['snort'] as $signature)
		{
			// save the signature record to get an id
			$this->id = $signature[$this->alias]['id'];
			$this->data[$this->alias] = $signature[$this->alias];
			if(!$this->save($this->data))
			{
				$this->modelError = __('Unable to update this %s', __('Signature'));
				continue;
			}
			$id = $this->id;
			
			// save the snort signature
			if(isset($signature['SnortSignature']))
			{
				$signature['SnortSignature']['signature_id'] = $id;
				$this->SnortSignature->update($id, $signature);
			}
			$ids[$id] = $id;
		}
		
		return $ids;
	}
	
	public function idByHash($hash = false)
	{
		return $this->field('id', array($this->alias.'.hash' => $hash));
	}
	
	public function formatData($data = array())
	{
	// used for both adding and updating a signature
		if(!isset($data[$this->alias]['signatures']) or !trim($data[$this->alias]['signatures']))
		{
			$this->modalError = __('No data.');
			return false;
		}
		
		$out = array('yara' => array(), 'snort' => array());
		
		$data[$this->alias]['signatures'] = trim($data[$this->alias]['signatures']);
		
		$yara_signatures = $this->YaraSignature->extractSignatures($data[$this->alias]['signatures']);
		$snort_signatures = $this->SnortSignature->extractSignatures($data[$this->alias]['signatures']);
		unset($data[$this->alias]['signatures']);
		
		$i=0;
		foreach($yara_signatures as $yara_signature)
		{
			$i++;
			$signature = $data[$this->alias];
			$signature['signature_type'] = 'yara';
			if(!isset($signature['name']))
			{
				if(isset($yara_signature['title']) and $yara_signature['title'])
					$signature['name'] = $yara_signature['title'];
				elseif(isset($yara_signature['name']) and $yara_signature['name'])
					$signature['name'] = $yara_signature['name'];
			}
			
			// merge the tags to both records have the same tag
			if(!isset($signature['tags']))
				$signature['tags'] = array();
			elseif(!is_array($signature['tags']))
				$signature['tags'] = explode(',', $signature['tags']);
			if(!isset($signature['tags']))
				$yara_signature['tags'] = array();
			elseif(!is_array($yara_signature['tags']))
				$yara_signature['tags'] = explode(',', $yara_signature['tags']);
			
			$tags = array_merge($signature['tags'], $yara_signature['tags']);
			
			foreach($tags as $k => $v)
			{
				$v = trim($v);
				if(!$v) { unset($tags[$k]); continue; }
				$v = strtolower($v);
				$v = Inflector::slug($v);
				$tags[$k] = $v;
			}
			$signature['tags'] = $yara_signature['tags'] = implode(',', $tags);
			
			// userid
			if(isset($signature['added_user_id']))
				$yara_signature['added_user_id'] = $signature['added_user_id'];
			
			// org_group_id
			if(isset($signature['org_group_id']))
				$yara_signature['org_group_id'] = $signature['org_group_id'];
			
			// copy the raw signature from the yara to the main
			if(isset($yara_signature['raw']))
				$signature['signature'] = $yara_signature['raw'];
			
			// copy the raw signature from the yara to the main
			if(isset($yara_signature['compiled']))
				$signature['signature'] = $yara_signature['compiled'];
			
			// set the hash
			if(isset($yara_signature['hash']))
				$signature['hash'] = $yara_signature['hash'];
			
			// check the signature source 
			$signature_source = false;
			if(isset($signature['signature_source']))
			{
				$signature_source = $signature['signature_source'];
				unset($signature['signature_source']);
			}
			elseif(isset($yara_signature['signature_source']))
			{
				$signature_source = $yara_signature['signature_source'];
				unset($yara_signature['signature_source']);
			}
			
			if($signature_source)
			{
				if($signature_source_id = $this->SignatureSource->add($signature_source))
				{
					$signature['signature_source_id'] = $signature_source_id;
					$yara_signature['signature_source_id'] = $signature_source_id;
				}
			}
			
			// fill out the Signature record
			$out['yara'][$i][$this->alias] = $signature;
			
			// fill out the YaraSignature record
			$out['yara'][$i]['YaraSignature'] = $yara_signature;
		}
		
		foreach($snort_signatures as $snort_signature)
		{
			$i++;
			$signature = $data[$this->alias];
			$signature['signature_type'] = 'snort';
			if(!isset($signature['name']) and isset($snort_signature['name']))
			{
				$signature['name'] = $snort_signature['name'];
			}
			
			// fix the signature's tags to match like how we're doing it in yara
			if(!isset($signature['tags']))
				$signature['tags'] = array();
			$tags = $signature['tags'];
			if(!is_array($tags))
				$tags = explode(',', $tags);		
			foreach($tags as $k => $v)
			{
				$v = trim($v);
				if(!$v) { unset($tags[$k]); continue; }
				$v = strtolower($v);
				$v = Inflector::slug($v);
				$tags[$k] = $v;
			}
			$signature['tags'] = $snort_signature['tags'] = implode(',', $tags);
			
			// userid
			if(isset($signature['added_user_id']))
				$snort_signature['added_user_id'] = $signature['added_user_id'];
			
			// org_group_id
			if(isset($signature['org_group_id']))
				$snort_signature['org_group_id'] = $signature['org_group_id'];
			
			// copy the raw signature from the yara to the main
			if(isset($snort_signature['raw']))
				$signature['signature'] = $snort_signature['raw'];
			
			// copy the raw signature from the yara to the main
			if(isset($snort_signature['compiled']))
				$signature['signature'] = $snort_signature['compiled'];
			
			// set the hash
			if(isset($snort_signature['hash']))
				$signature['hash'] = $snort_signature['hash'];
			
			// check the signature source 
			$signature_source = false;
			if(isset($signature['signature_source']))
			{
				$signature_source = $signature['signature_source'];
				unset($signature['signature_source']);
			}
			elseif(isset($snort_signature['signature_source']))
			{
				$signature_source = $snort_signature['signature_source'];
				unset($yara_signature['signature_source']);
			}
			
			if($signature_source)
			{
				if($signature_source_id = $this->SignatureSource->add($signature_source))
				{
					$signature['signature_source_id'] = $signature_source_id;
					$snort_signature['signature_source_id'] = $signature_source_id;
				}
			}
			
			// fill out the Signature record
			$out['snort'][$i][$this->alias] = $signature;
			
			// fill out the YaraSignature record
			$out['snort'][$i]['SnortSignature'] = $snort_signature;
		}
		
		return $out;
	}
}

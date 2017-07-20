<?php
App::uses('AppModel', 'Model');
/**
 * YaraSignature Model
 *
 * @property Signature $Signature
 * @property YaraSignatureIndex $YaraSignatureIndex
 */
class YaraSignature extends AppModel 
{
	public $displayField = 'name';
	
	public $validate = array(
		'signature_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);
	
	public $belongsTo = array(
		'Signature' => array(
			'className' => 'Signature',
			'foreignKey' => 'signature_id',
		),
		'SignatureSource' => array(
			'className' => 'SignatureSource',
			'foreignKey' => 'signature_source_id',
		),
		'YaraSignatureAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'YaraSignatureUpdatedUser' => array(
			'className' => 'User',
			'foreignKey' => 'updated_user_id',
		),
	);
	
	public $hasMany = array(
		'YaraSignatureIndex' => array(
			'className' => 'YaraSignatureIndex',
			'foreignKey' => 'yara_signature_id',
			'dependent' => true,
		),
		'YaraSignatureMeta' => array(
			'className' => 'YaraSignatureIndex',
			'foreignKey' => 'yara_signature_id',
			'dependent' => true,
			'conditions' => array('YaraSignatureMeta.type' => 'meta'),
		),
		'YaraSignatureString' => array(
			'className' => 'YaraSignatureIndex',
			'foreignKey' => 'yara_signature_id',
			'dependent' => true,
			'conditions' => array('YaraSignatureString.type' => 'string'),
		),
		'YaraSignatureCondition' => array(
			'className' => 'YaraSignatureIndex',
			'foreignKey' => 'yara_signature_id',
			'dependent' => true,
			'conditions' => array('YaraSignatureCondition.type' => 'condition'),
		),
	);
	
	public $hasManyAlwaysConnect = array(
		'YaraSignatureMeta',
		'YaraSignatureString',
		'YaraSignatureCondition',
	);
	
	// add plugins and other behaviors
	public $actsAs = array(
		'Utilities.Signature',
		'Tags.Taggable',
    );
    
    public function afterFind($results = array(), $primary = false)
    {
    	foreach($results as $i => $result)
    	{
    		if(!isset($results[$i][$this->alias]['compiled']) 
    			and isset($results[$i][$this->alias]['id']) 
    			and $results[$i][$this->alias]['id']
    			and isset($results[$i][$this->alias]['hash']) 
    			and $this->recursive > 0)
    		{
    			foreach($this->hasMany as $alias => $criteria)
    			{
    				if(!isset($result[$alias]))
    				{
	    				$conditions = array($alias.'.'.$criteria['foreignKey'] => $results[$i][$this->alias]['id']);
	    				if(isset($criteria['conditions']) and is_array($criteria['conditions']))
	    				{
	    					$conditions = array_merge($conditions, $criteria['conditions']);
	    				}
    					$alias_results = $this->{$alias}->find('all', array(
    						'recursive' => -1,
    						'conditions' => $conditions,
    					));
    					foreach($alias_results as $alias_result)
    					{
    						$results[$i][$alias][] = $alias_result[$alias];
    					}
//    					$results[$i][$alias] 
    				}
    			}
    			foreach($this->belongsTo as $alias => $criteria)
    			{
    				if(!isset($result[$alias]))
    				{
	    				$conditions = array($alias.'.id' => $results[$i][$this->alias][$criteria['foreignKey']]);
	    				if(isset($criteria['conditions']) and is_array($criteria['conditions']))
	    				{
	    					$conditions = array_merge($conditions, $criteria['conditions']);
	    				}
    					$alias_results = $this->{$alias}->find('first', array(
    						'recursive' => -1,
    						'conditions' => $conditions,
    					));
    					$results[$i][$alias] = (isset($alias_results[$alias])?$alias_results[$alias]:array());
    				}
    			}
    			$results[$i][$this->alias]['compiled'] = $this->compileSignature($results[$i]);
    		}
    	}
    	return parent::afterFind($results, $primary);
    }
	
	public function extractSignatures($string = false)
	{
		$string = trim($string);
		if(!$string)
		{
			$this->modelError = __('No %s were provided.', __('Signatures'));
			return false;
		}
		
		$this->Sig_setType('yara');
		$this->Sig_setString($string);
		return $this->Sig_getSignatures();
	}
	
	public function add($data = array())
	{
	// shouldn't be called directly, should be called from Signature::afterSave();
	// the data should be one of the items returned from self::extractSignatures() above
	// the Signature::afterSave(); should add some extra information like signature_id
		
		$id = false;
		if(isset($data[$this->alias]['hash']))
		{
			$hash = $data[$this->alias]['hash'];
		}
		else
		{
			$hash = $this->Sig_getHash($data[$this->alias]['compiled']);
		}
		
		if(!$id = $this->idByHash($hash))
		{
			$this->create();
			$this->data[$this->alias] = $data[$this->alias];
			if(!$this->save($this->data))
			{
				$this->modelError = __('Unable to save this %s', __('Signatures'));
				return false;
			}
			$id = $this->id;
			
			// save each of the different types
			if(isset($data[$this->alias]['meta']) and is_array($data[$this->alias]['meta']))
			{
				foreach($data[$this->alias]['meta'] as $key => $value)
				{
					$this->YaraSignatureIndex->checkAdd($id, 'meta', $key, $value);
				}
			}
			
			if(isset($data[$this->alias]['strings']) and is_array($data[$this->alias]['strings']))
			{
				foreach($data[$this->alias]['strings'] as $key => $value)
				{
					$this->YaraSignatureIndex->checkAdd($id, 'string', $key, $value);
				}
			}
			
			if(isset($data[$this->alias]['condition']) and is_array($data[$this->alias]['condition']))
			{
				foreach($data[$this->alias]['condition'] as $key => $value)
				{
					$this->YaraSignatureIndex->checkAdd($id, 'condition', false, $value);
				}
			}
		}
		
		$this->id = $id;
		return $id;
		
	}
	
	public function update($signature_id = false, $data = array())
	{	
		$id = false;
		if(!$signature_id)
		{
			$this->modelError = __('Unknown %s id.', __('Signature'));
			return false;
		}
		
		if(isset($data[$this->alias]['hash']))
		{
			$hash = $data[$this->alias]['hash'];
		}
		else
		{
			$hash = $this->Sig_getHash($data[$this->alias]['compiled']);
		}
		
		if(!$id = $this->field('id', array($this->alias.'.signature_id' => $signature_id)))
		{
			$this->create();
		}
		else
		{
			$this->id = $id;
		}
		
		$this->data[$this->alias] = $data[$this->alias];
		$this->data[$this->alias]['id'] = $this->id;
		if(!$this->save($this->data))
		{
			$this->modelError = __('Unable to update this %s', __('Signature'));
			return false;
		}
		$id = $this->id;
			
		// save each of the different types
		
		if(isset($data[$this->alias]['strings']) and is_array($data[$this->alias]['strings']))
		{
			$this->YaraSignatureIndex->update($id, 'string', $data[$this->alias]['strings']);
		}
		if(isset($data[$this->alias]['meta']) and is_array($data[$this->alias]['meta']))
		{
			$this->YaraSignatureIndex->update($id, 'meta', $data[$this->alias]['meta']);
		}
		
		if(isset($data[$this->alias]['condition']) and is_array($data[$this->alias]['condition']))
		{
			$this->YaraSignatureIndex->update($id, 'condition', $data[$this->alias]['condition']);
		}
		
		$this->id = $id;
		return $id;
		
	}
	
	public function compileSignature($data = array(), $add_fo_meta = true)
	{
		
		$toCompile = array(
			'name' => $data['YaraSignature']['name'],
			'scope' => $data['YaraSignature']['scope'],
			'tags' => '',
			'meta' => array(),
			'strings' => array(),
			'condition' => array(),
		);
		
		$tags = array();
		if(isset($data['Tag']) and !empty($data['Tag']))
		{
			foreach($data['Tag'] as $tag)
			{
				$tags[] = Inflector::camelize($tag['keyname']);
			}
		}
		
		if(isset($data['Signature']['Tag']) and !empty($data['Signature']['Tag']))
		{
			foreach($data['Signature']['Tag'] as $tag)
			{
				$tags[] = Inflector::camelize($tag['keyname']);
			}
		}
		
		$tags = array_flip($tags);
		$tags = array_flip($tags);
		
		$toCompile['tags'] = implode(',', $tags);
		
		if($add_fo_meta)
		{
			// load ability to create an html link
			App::uses('View', 'View');
			$View = new View();
			App::uses('HtmlExtHelper', 'Utilities.View/Helper');
			$HtmlHelper = new HtmlExtHelper($View);
			
			// add our own meta tags
			$toCompile['meta']['fo_site_title'] = Configure::read('Site.title');
			$toCompile['meta']['fo_site_uri'] = $HtmlHelper->url('/', true);
			$toCompile['meta']['fo_rule_id'] = $data['Signature']['id'];
			$toCompile['meta']['fo_rule_added'] = $data['Signature']['created'];
//			$toCompile['meta']['fo_rule_hash'] = $data['YaraSignature']['hash'];
			$toCompile['meta']['fo_rule_uri'] = $HtmlHelper->url(array('controller' => 'signatures', 'action' => 'view', $data['Signature']['id'], 'plugin' => false, 'admin' => false), true);
			
			if(isset($data['Signature']['SignatureSource']) and !empty($data['Signature']['SignatureSource']))
			{
				$toCompile['meta']['fo_rule_source_name'] = $data['Signature']['SignatureSource']['name'];
				$toCompile['meta']['fo_rule_source_slug'] = $data['Signature']['SignatureSource']['slug'];
			}
			
			if(isset($data['Signature']['SignatureAddedUser']) and !empty($data['Signature']['SignatureAddedUser']))
			{
				$toCompile['meta']['fo_added_user_name'] = $data['Signature']['SignatureAddedUser']['name'];
				$toCompile['meta']['fo_added_user_email'] = $data['Signature']['SignatureAddedUser']['email'];
				$toCompile['meta']['fo_added_user_uri'] = $HtmlHelper->url(array('controller' => 'users', 'action' => 'view', $data['Signature']['SignatureAddedUser']['id'], 'plugin' => false, 'admin' => false), true);
			}
		}
		
		// the meta data
		if(isset($data['YaraSignatureMeta']) and !empty($data['YaraSignatureMeta']))
		{	
			foreach($data['YaraSignatureMeta'] as $meta)
			{
				$toCompile['meta'][$meta['key']] = $meta['value'];
			}
		}
		
		// the Strings
		$lines[] = "\tstrings:";
		if(isset($data['YaraSignatureString']) and !empty($data['YaraSignatureString']))
		{
			foreach($data['YaraSignatureString'] as $string)
			{
				$toCompile['strings'][$string['key']] = $string['value'];
			}
		}
		
		// the Conditions
		$lines[] = "\tcondition:";
		if(isset($data['YaraSignatureCondition']) and !empty($data['YaraSignatureCondition']))
		{
			foreach($data['YaraSignatureCondition'] as $condition)
			{
				$toCompile['condition'][] = $condition['value'];
			}
		}
		
		return $this->Sig_YaraCompileSignature($toCompile);
	}
	
	public function idByHash($hash = false)
	{
		return $this->field('id', array($this->alias.'.hash' => $hash));
	}
}

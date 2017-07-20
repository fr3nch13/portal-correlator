<?php
App::uses('AppModel', 'Model');
/**
 * SnortSignature Model
 *
 * @property Signature $Signature
 * @property SnortSignatureIndex $SnortSignatureIndex
 */
class SnortSignature extends AppModel 
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
		'SnortSignatureAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'SnortSignatureUpdatedUser' => array(
			'className' => 'User',
			'foreignKey' => 'updated_user_id',
		),
	);
	
	public $hasMany = array(
		'SnortSignatureIndex' => array(
			'className' => 'SnortSignatureIndex',
			'foreignKey' => 'snort_signature_id',
			'dependent' => true,
		),
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
		'SnortSignature.action',
		'SnortSignature.protocol',
		'SnortSignature.src_ip',
		'SnortSignature.src_port',
		'SnortSignature.dest_ip',
		'SnortSignature.dest_port',
		'SnortSignature.raw',
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
    				if(!isset($result[$alias]) and isset($results[$i][$this->alias][$criteria['foreignKey']]))
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
		
		$this->Sig_setType('snort');
		$this->Sig_setString($string);
		return $this->Sig_getSignatures();
	}
	
	public function add($data = array())
	{
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
			
			// save each of the options
			if(isset($data[$this->alias]['options']) and is_array($data[$this->alias]['options']))
			{
				foreach($data[$this->alias]['options'] as $key => $value)
				{
					$this->SnortSignatureIndex->checkAdd($id, $key, $value);
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
			$this->modelError = __('Unable to update this %s', __('Signatures'));
			return false;
		}
		$id = $this->id;
		
		// save each of the options
		if(isset($data[$this->alias]['options']) and is_array($data[$this->alias]['options']))
		{
			$this->SnortSignatureIndex->update($id, $data[$this->alias]['options']);
		}
		
		$this->id = $id;
		return $id;
		
	}
	
	public function compileSignature($data = array(), $add_fo_meta = true)
	{
		if(!isset($data['SnortSignature'])) return false;
		$toCompile = array(
			'action' => $data['SnortSignature']['action'],
			'protocol' => $data['SnortSignature']['protocol'],
			'src_ip' => $data['SnortSignature']['src_ip'],
			'src_port' => $data['SnortSignature']['src_port'],
			'direction' => $data['SnortSignature']['direction'],
			'dest_ip' => $data['SnortSignature']['dest_ip'],
			'dest_port' => $data['SnortSignature']['dest_port'],
			'options' => array(
				'msg' => '"'.$data['Signature']['name'].'"',
			),
		);
		
		if(isset($data['SnortSignatureIndex']))
		{
			$options = array();
			foreach($data['SnortSignatureIndex'] as $option)
			{
				$key = $option['key'];
				$value = $option['value'];
				$options[$key] = $value;
			}
			
			// add our info
			if($add_fo_meta)
			{
				// load ability to create an html link
				App::uses('View', 'View');
				$View = new View();
				App::uses('HtmlHelper', 'View/Helper');
				$HtmlHelper = new HtmlHelper($View);
				
				$url = $HtmlHelper->url(array('controller' => 'signatures', 'action' => 'view', $data['Signature']['id'], 'plugin' => false, 'admin' => false), true);
				
				if(!isset($options['metadata']))
				{
					$options['metadata'] = array();
				}
				else
				{
					$options['metadata'] = explode('^^', $options['metadata']);
				}
				
				$metadata =  array();
			
				// add our own meta tags
				$metadata['fo_site_title'] = Configure::read('Site.title');
				$metadata['fo_site_uri'] = $HtmlHelper->url('/', true);
				$metadata['fo_rule_id'] = $data['Signature']['id'];
				$metadata['fo_rule_added'] = $data['Signature']['created'];
//				$metadata['fo_rule_hash'] = $data['YaraSignature']['hash'];
				$metadata['fo_rule_uri'] = $HtmlHelper->url(array('controller' => 'signatures', 'action' => 'view', $data['Signature']['id'], 'plugin' => false, 'admin' => false), true);			
				
				if(isset($data['Signature']['SignatureSource']) and !empty($data['Signature']['SignatureSource']))
				{
					$metadata['fo_rule_source_name'] = $data['Signature']['SignatureSource']['name'];
					$metadata['fo_rule_source_slug'] = $data['Signature']['SignatureSource']['slug'];
				}
				
				if(isset($data['Signature']['SignatureAddedUser']) and !empty($data['Signature']['SignatureAddedUser']))
				{
					$metadata['fo_added_user_name'] = $data['Signature']['SignatureAddedUser']['name'];
					$metadata['fo_added_user_email'] = $data['Signature']['SignatureAddedUser']['email'];
					$metadata['fo_added_user_uri'] = $HtmlHelper->url(array('controller' => 'users', 'action' => 'view', $data['Signature']['SignatureAddedUser']['id'], 'plugin' => false, 'admin' => false), true);
				}
		
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
				if(count($tags))
				{
					$metadata['fo_tags'] = implode(', ', $tags);
				}
/*
				if(isset($data['Signature']['Category']) and !empty($data['Signature']['Category']))
				{
					foreach($data['Signature']['Category'] as $category)
					{
						$category_id = $category['id'];
						$metadata['fo_category_'.$category_id.'_name'] = $category['name'];
						$metadata['fo_category_'.$category_id.'_uri'] = $HtmlHelper->url(array('controller' => 'categories', 'action' => 'view', $category['id'], 'plugin' => false, 'admin' => false), true);
					}
				}
				
				if(isset($data['Signature']['Report']) and !empty($data['Signature']['Report']))
				{	
					foreach($data['Signature']['Report'] as $report)
					{
						$report_id = $report['id'];
						$metadata['fo_report_'.$report_id.'_name'] = $report['name'];
						$metadata['fo_report_'.$report_id.'_uri'] = $HtmlHelper->url(array('controller' => 'reports', 'action' => 'view', $report['id'], 'plugin' => false, 'admin' => false), true);
					}
				}
*/
				
				foreach($metadata as $key => $value)
				{
					$metadata[$key] = '"'.trim($value).'"';
				}
				
				// add the original metadata
				foreach($options['metadata'] as $option)
				{
					$key = $value = $option;
					if(stripos($option, ' ') !== false)
					{
						list($key, $value) = preg_split('/\s+/', $option);
					}
					$metadata[$key] = $value;
				}
				
				foreach($metadata as $key => $value)
				{
					$options['metadata'][] = $key.' '. $value;
				}
				
				$options['metadata'] = implode('^^', $options['metadata']);
				
				if(!isset($options['reference']))
				{
					$options['reference'] = array();
				}
				else
				{
					$options['reference'] = explode('^^', $options['reference']);
				}
				
				// use the options[reference][url] to link to the signature details page
				// reference: url,www.cert.org/advisories/CA-2001-26.html;
				$url = str_replace('http://', '', $url);
				$url = str_replace('https://', '', $url);
				$url = str_replace(':', '.', $url);
				
				$options['reference'][] = 'url,'. $url;
				
				$options['reference'] = implode('^^', $options['reference']);
			}
			
			ksort($options);
			$toCompile['options'] = array_merge($toCompile['options'], $options);
		}
		
		return $this->Sig_SnortCompileSignature($toCompile, true);
	}
	
	public function idByHash($hash = false)
	{
		return $this->field('id', array($this->alias.'.hash' => $hash));
	}
}

<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper 
{
	public $helpers = array(
		'Ajax', 'Time', 'Js' => array('JqueryUi'),
		'Html' => array('className' => 'Utilities.HtmlExt' ),
		'Form' => array('className' => 'Utilities.FormExt' ),
	);
	
	public $source_map = false;
	
	public $sig_type_map = false;
	
	public function getSourceMap($source = false)
	{
		if(!$this->source_map)
		{
			$this->source_map = Configure::read('Site.source_map');
		}
		if($source)
		{
			$source = trim(strtolower($source));
			if(isset($this->source_map[$source])) return $this->source_map[$source];
			else return false;
		}
		return $this->source_map;
	}
	
	public function getSigTypeMap($type = false)
	{
		if(!$this->sig_type_map)
		{
			$this->sig_type_map = Configure::read('Site.sig_type_map');
		}
		if($type)
		{
			$type = trim(strtolower($type));
			if(isset($this->sig_type_map[$type])) return $this->sig_type_map[$type];
			else return false;
		}
		return $this->sig_type_map;
	}
	
	public function sourceUser($source = false, $admin = false)
	{
		if(!$source) return __('None');
		
		$admin = false;
		if(isset($this->params['admin']) and $this->params['admin'])
		{
			$admin = true;
		}
		if(AuthComponent::user('role') == 'admin')
		{
			$admin = true;
		}
		
		if(!$admin)
		{
			if($newsource = $this->getSourceMap($source) and !$admin)
			{
				return $newsource;
			}
		}
		
		return Inflector::humanize($source);
	}
	
	public function sourcesUser($sources = false)
	{
		if(!$sources) return __('None');
		$sources = explode(',', $sources);
		
		$admin = false;
		if(isset($this->params['admin']) and $this->params['admin'])
		{
			$admin = true;
		}
		if(AuthComponent::user('role') == 'admin')
		{
			$admin = true;
		}
		
		foreach($sources as $i => $source)
		{
			$source = trim($source);
			
			if(!$admin)
			{
				if($newsource = $this->getSourceMap($source))
				{
					$source = $newsource;
				}
			}
			
			$source = Inflector::humanize($source);
			$sources[$i] = $source;
		}
		
		return implode(', ', $sources);
	}
	
	public function publicState($public = 0)
	{
		if(!$public) $public = 0;
		
		$options = $this->publicStateOptions();
		
		if(isset($options[$public]))
		{
			return $options[$public];
		}
		
		return $options[0];
	}
	
	public function publicStateOptions()
	{
		return array(
			0 => __('Private'),
			1 => __('Org Shared'),
			2 => __('Global Shared'),
		);
	}
	
	public function editors($options = array())
	{
		$defaults = array(
			'class' => 'editors',
			'label' => __('Select the Editors and Contributors'),
			
			'available_id' => 'editors_available',
			'available_title' => __('Available Users'),
			'available_options' => array(),
			
			'editors_id' => 'editors_editors',
			'editors_title' => __('Active Editors'),
			'editors_options' => array(),
			
			'contributors_id' => 'editors_contributors',
			'contributors_title' => __('Active Contributors'),
			'contributors_options' => array(),
		);
		
		$options = array_merge($defaults, $options);
		
		$out = '';
		
		$ids = '#'. $options['available_id']. ', #'. $options['editors_id']. ', #'. $options['contributors_id'];
		
		$jsScript = '
		 $(function() {
			$( "#'. $options['available_id'].'" ).sortable({
				connectWith: "ul",
				items: "li:not(.col-title)",
				update : function () {
					var order = $("#'. $options['available_id'].'").sortable(\'serialize\');
					$("#'. $options['available_id'].'_hidden").val(order);
				} 
			});
			$( "#'. $options['editors_id'].'" ).sortable({
				connectWith: "ul",
				items: "li:not(.col-title)",
				update : function () {
					var order = $("#'. $options['editors_id'].'").sortable(\'serialize\');
					$("#'. $options['editors_id'].'_hidden").val(order);
				} 
			});
			$( "#'. $options['contributors_id'].'" ).sortable({
				connectWith: "ul",
				items: "li:not(.col-title)",
				update : function () {
					var order = $("#'. $options['contributors_id'].'").sortable(\'serialize\');
					$("#'. $options['contributors_id'].'_hidden").val(order);
				} 
			});
			$( "'. $ids.' " ).disableSelection();
		});';
		
		$available_lis = array(
			$this->Html->tag('li', $options['available_title'], array('class' => 'col-title')),
		);
		foreach($options['available_options'] as $id => $name)
		{
			$available_lis[] = $this->Html->tag('li', $name, array('id' => 'user_id_'. $id));
		}
		$available_ul = $this->Html->tag('ul', implode(' ', $available_lis), array('id' => $options['available_id'], 'class' => $options['class']));
		$out .= $available_ul;
		$out .= $this->Form->input($options['available_id'], array(
			'type' => 'hidden',
			'value' => '',
			'id' => $options['available_id']. '_hidden',
		));
		
		$editors_lis = array(
			$this->Html->tag('li', $options['editors_title'], array('class' => 'col-title')),
		);
		foreach($options['editors_options'] as $id => $name)
		{
			$editors_lis[] = $this->Html->tag('li', $name, array('id' => 'user_id_'. $id));
		}
		$editors_ul = $this->Html->tag('ul', implode(' ', $editors_lis), array('id' => $options['editors_id'], 'class' => $options['class']));
		$out .= $editors_ul;
		$out .= $this->Form->input($options['editors_id'], array(
			'type' => 'hidden',
			'value' => '',
			'id' => $options['editors_id'].'_hidden',
		));
		
		$contributors_lis = array(
			$this->Html->tag('li', $options['contributors_title'], array('class' => 'col-title')),
		);
		foreach($options['contributors_options'] as $id => $name)
		{
			$contributors_lis[] = $this->Html->tag('li', $name, array('id' => 'user_id_'. $id));
		}
		$contributors_ul = $this->Html->tag('ul', implode(' ', $contributors_lis), array('id' => $options['contributors_id'], 'class' => $options['class']));
		$out .= $contributors_ul;
		$out .= $this->Form->input($options['contributors_id'], array(
			'type' => 'hidden',
			'value' => '',
			'id' => $options['contributors_id'].'_hidden',
		));
		
		$out = $this->Html->tag('div', $out, array('class' => $options['class']. '_div'));
		
		$out = $this->Html->tag('label', $options['label']). $out;
		
		$this->Html->css('editors', null, array('inline' => false));
		$this->Js->buffer($jsScript);
		return $out;
	}
	
	public function editorType($type = 0)
	{
		if($type == 1)
			return __('Editor');
		return __('Contributor');
	}
	
	public function dnsAutoLookupLevel($level = 0, $short = false)
	{
		if(!$level) $level = 0;
		
		$options = $this->dnsAutoLookupLevelOptions($short);
		
		if(isset($options[$level]))
		{
			return $options[$level];
		}
		
		return $options[0];
	}
	
	public function dnsAutoLookupLevelOptions($short = false, $plural = false)
	{
		if($short)
		{
			return array(
				0 => __('Disabled'),
				1 => __('Just This'),
				2 => __('This and Results'),
				3 => __('Just This, Once'),
			);
		}
		return array(
			0 => ($plural?__('Don\'t Auto Track'):__('Don\'t Auto Track')),
			1 => ($plural?__('Auto Track Just These'):__('Auto Track Just This')),
			2 => ($plural?__('Auto Track These Plus Their Results'):__('Auto Track This Plus Its Results')),
			3 => ($plural?__('Auto Track Just These, Only Once'):__('Auto Track Just This, Only Once')),
		);
	}
	
	public function whoisAutoLookupLevel($level = 0, $short = false)
	{
		if(!$level) $level = 0;
		
		$options = $this->whoisAutoLookupLevelOptions($short);
		
		if(isset($options[$level]))
		{
			return $options[$level];
		}
		
		return $options[0];
	}
	
	public function whoisAutoLookupLevelOptions($short = false, $plural = false)
	{
		if($short)
		{
			return array(
				0 => __('Disabled'),
				1 => __('Active'),
//				2 => __('This and Results'),
				3 => __('Only Once'),
			);
		}
		return array(
			0 => ($plural?__('Don\'t Auto Track'):__('Don\'t Auto Track')),
			1 => ($plural?__('Auto Track These'):__('Auto Track This')),
//			2 => ($plural?__('Auto Track These Plus Their Results'):__('Auto Track This Plus Its Results')),
			3 => ($plural?__('Auto Track These, Only Once'):__('Auto Track This, Only Once')),
		);
	}
	
	public function vtAutoLookupLevel($level = 0, $short = false)
	{
		if(!$level) $level = 0;
		
		$options = $this->vtAutoLookupLevelOptions($short);
		
		if(isset($options[$level]))
		{
			return $options[$level];
		}
		
		return $options[0];
	}
	
	public function vtAutoLookupLevelOptions($short = false, $plural = false)
	{
		if($short)
		{
			return array(
				0 => __('Disabled'),
				1 => __('Enabled'),
/*
//				1 => __('Just This'),
//				2 => __('This and Results'),
//				3 => __('Just This, Once'),
*/
			);
		}
		return array(
			0 => __('Disabled'),
			1 => __('Enabled'),
/*
			0 => ($plural?__('Don\'t Auto Lookup'):__('Don\'t Auto Lookup')),
			1 => ($plural?__('Auto Lookup Just These'):__('Auto Lookup Just This')),
			2 => ($plural?__('Auto Lookup These Plus Their Results'):__('Auto Lookup This Plus Its Results')),
//			3 => ($plural?__('Auto Lookup Just These, Only Once'):__('Auto Lookup Just This, Only Once')),
*/
		);
	}
	
	public function vtNiceRelatedType($type = false)
	{
		$type = strtolower($type);
		$type = str_replace('_samples', '', $type);
		return Inflector::humanize($type);
	}
	
	public function automaticSwitch($toggle = 0)
	{
	/*
	 * Prints a Yes or a no based on a boolean value
	 */
		if(!$toggle) return __('Manual');
		return __('Automatic');
	}
	
///// Manages the csv fields for Import Managers
	public function editCsvFields($csv_fields = array(), $vector_types = array(), $field_name = 'csv_fields', $model = 'ImportManager')
	{
	/*
	 * Used to write out the csv fields for the add/edit forms
	 */
		$out = false;
		// adding a new one
		if(!$csv_fields)
		{
			$csv_fields = array(
				'' => array(
					'setting_vector_type' => 0,
					'setting_dns' => 0,
					'setting_whois' => 0,
				),
			);
		}
		
		$blockInputId = $field_name. 'Input';
		$blockInputClass = $field_name. 'Input';
		$blocksHolderId = $field_name. 'Holder';
		$blocksHolderClass = $field_name. 'Holder';
		$blockButtonClassClone = $field_name. 'Clone';
		$blockButtonClassRemove = $field_name. 'Remove';
		
		
		$jsScript = "
var regex = /^(.*)\_(\d+)+$/i;
var regex_data_id = /^".$model.Inflector::camelize($field_name)."(\d+)/i;
var regex_data_name = /^data\[".$model."\]\[".$field_name."\]\[(\d+)\]/i;
// ImportManagerCsvFields2CsvField
var cloneIndex = $('.".$blockInputClass."').length;

$('button.".$blockButtonClassClone."').live('click', function()
{
    cloneIndex++;
	$(this).parents('.".$blockInputClass."').clone()
		.appendTo('#".$blocksHolderId."')
		.attr('id', '".$blockInputId."' +  cloneIndex)
		.find('*').each(function()
		{
			var id = this.id || '';
			var match = id.match(regex) || [];
			if (match.length == 3)
			{
				this.id = match[1] + (cloneIndex);
			}
        })
		.find('input, select, label').each(function()
		{
			var name = this.name || '';
			if(name)
			{
				var match = name.match(regex_data_name) || [];
				var newname = this.name.replace(match[1], cloneIndex);
				$(this).attr('name', newname);
			}
			
			var id = this.id || '';
			if(id)
			{
				var match = id.match(regex_data_id) || [];
				var newid = this.id.replace(match[1], cloneIndex);
				$(this).attr('id', newid);
			}
			
			var afor = $(this).attr('for') || '';
			if(afor)
			{
				var match = afor.match(regex_data_id) || [];
				var newfor = $(this).attr('for').replace(match[1], cloneIndex);
				$(this).attr('for', newfor);
			}
		});
    return false;
});

$('button.".$blockButtonClassRemove."').live('click', function()
{
    $(this).parents('.".$blockInputClass."').remove();
});
		";
		
		$csvFormFields = array();
		$cloneIndex=1;
		foreach($csv_fields as $csv_field => $field_options)
		{
			$csvFormFields[$csv_field] = $this->Form->input($model. '.csv_fields.'. $cloneIndex.'.csv_field', array(
				'label' => __('CSV Field Name'),
				'between' => __('The csv field column name that is considered a vector. This is case sensative.'),
				'value' => $csv_field,
			));
			$csvFormFields[$csv_field] .= $this->Form->input($model. '.csv_fields.'. $cloneIndex.'.setting_vector_type', array(
				'label' => __('Vector Group'),
				'empty' => __('[ None ]'),
				'value' => $field_options['setting_vector_type'],
				'options' => $vector_types,
			));
			$csvFormFields[$csv_field] .= $this->Form->input($model. '.csv_fields.'. $cloneIndex.'.setting_dns', array(
				'label' => __('DNS Lookup Level'),
				'value' => $field_options['setting_dns'],
				'options' => $this->dnsAutoLookupLevelOptions(true),
			));
			$csvFormFields[$csv_field] .= $this->Form->input($model. '.csv_fields.'. $cloneIndex.'.setting_whois', array(
				'label' => __('Whois Lookup Level'),
				'value' => $field_options['setting_whois'],
				'options' => $this->whoisAutoLookupLevelOptions(true),
			));
			
			$csvFormFields[$csv_field] .= $this->Form->button('Add New Field', array('type' => 'button', 'class' => $blockButtonClassClone. ' button'));
			$csvFormFields[$csv_field] .= $this->Form->button('Remove Field', array('type' => 'button', 'class' => $blockButtonClassRemove. ' button'));
			
			$csvFormFields[$csv_field] = $this->Html->tag('div', $csvFormFields[$csv_field], array('id' => $blockInputId. $cloneIndex, 'class' => $blockInputClass));
			
			$cloneIndex++;
		}
		
		$out = $this->Html->tag('div', implode("\n", $csvFormFields), array('id' => $blocksHolderId, 'class' => $blocksHolderClass));
		
		$this->Html->css('csv_editor', null, array('inline' => false));
		$this->Js->buffer($jsScript);
		
		return $out;
	}
	
	public function showCsvFields($csv_fields = array(), $short = true)
	{
	/*
	 * Used to display the csv fields.
	 * short will just list out the csv fields separated by commas
	 */
		if(!$csv_fields) return false;
		
		$out = false;
		if($short == true)
		{
			$out = array_keys($csv_fields);
			$out = implode(', ', $out);
		}
		
		return $out;
	}
	
/// Mainly used for imports to highlight the vectors
	public function highlightVectors($content = false, $vectors = array())
	{
		// $vectors is in the format: array('id' => 'vector')
		if(!$content) return $content;
		
		if(!count($vectors))
		{
			return $content;
		}
		
		$cache_key = md5($content). md5(serialize($vectors));
		
		if($new_content = Cache::read($cache_key))
		{
			return $new_content;
		}
		
		$links = array();
		
		$tokens = token_get_all('<?php "'. str_replace('"', '\"', $content).'" ?>');
		
		
		foreach($vectors as $vector_id => $vector)
		{
			$new_content = '';
			
			$links[$vector_id] = $this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id));
			
			foreach($tokens as $i => $token)
			{
				if(!is_array($token)) $token_data = $token;
				else
				{
					if(in_array($token[0], array(370, 368))) continue;
					
					$token_data = $token[1];
					
					if($token[0] == 315)
					{
						$matches = array();
						if(preg_match('/'.preg_quote($vector).'/', $token_data, $matches))
						{
							$token_data = str_replace($vector, '".VID'.$vector_id.'."', $token_data);
						}
					}
				}
				
				$new_content .= $token_data;
			}
			// recreate the tokens
			$tokens = token_get_all('<?php '. $new_content.' ?>');
		}
		
		foreach($links as $id => $link)
		{
			$new_content = str_replace('".VID'.$id.'."', $link, $new_content);
		}
		
		$new_content = trim($new_content, ' "');
		
		Cache::write($cache_key, $new_content);
		
		return $new_content;
	}
	
	
}
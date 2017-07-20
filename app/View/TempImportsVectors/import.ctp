<?php 
// File: app/View/TempImportsVectors/import.ctp


$page_options = array();
if($import['Import']['user_id'] == AuthComponent::user('id'))
{
	$page_options[] = $this->Html->link(__('Assign DNS Tracking to All Vectors'), array('action' => 'assign_dnstracking', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to ONE Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));
}

// content
$th = array(
	'TempVector.vector' => array('content' => __('TempVector'), 'options' => array('sort' => 'TempVector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'TempVector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'TempImportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'TempImportsVector.active')),
	'TempImportsVector.created' => array('content' => __('Added to Import'), 'options' => array('sort' => 'TempImportsVector.created')),
	'TempVector.created' => array('content' => __('Created'), 'options' => array('sort' => 'TempVector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($temp_imports_vectors as $i => $temp_imports_vector)
{
	$active = $this->Wrap->yesNo($temp_imports_vector['TempImportsVector']['active']);
	
	$actions = $this->Html->link(__('View'), array('controller' => 'temp_vectors', 'action' => 'view', $temp_imports_vector['TempVector']['id']));
	
	$active = array(
		$this->Html->link($active, array('action' => 'toggle', 'active', $temp_imports_vector['TempImportsVector']['id']),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
	
	$actions .= $this->Html->link(__('Remove'), array('action' => 'delete', $temp_imports_vector['TempImportsVector']['id']),array('confirm' => 'Are you sure?'));
	
	$dns_auto_lookup = '';
	$geoip = '';
	if(isset($temp_imports_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($temp_imports_vector['Ipaddress']['dns_auto_lookup'], true);
		$geoip = $temp_imports_vector['Geoip']['country_iso'];
	}
	if(isset($temp_imports_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($temp_imports_vector['Hostname']['dns_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($temp_imports_vector['TempVector']['temp_vector'], array('controller' => 'temp_vectors', 'action' => 'view', $temp_imports_vector['TempVector']['id'])),
		$this->Html->link($temp_imports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $temp_imports_vector['VectorType']['id'])),
		$this->Wrap->niceWord($temp_imports_vector['TempVector']['type']),
		$dns_auto_lookup,
		$geoip,
		$active,
//		$this->Wrap->niceTime($temp_imports_vector['TempVector']['modified']),
		$this->Wrap->niceTime($temp_imports_vector['TempImportsVector']['created']),
		$this->Wrap->niceTime($temp_imports_vector['TempVector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $temp_imports_vector['TempImportsVector']['id'],
	);
}

$use_multiselect = false;
if($import['Import']['user_id'] == AuthComponent::user('id'))
{
	$use_multiselect = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Temp Import Vectors'),
	'page_options' => $page_options,
	'search_placeholder' => __('Temp Import Vectors'),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign Group'),
		'inactive' => __('Mark Inactive'),
		'active' => __('Mark Active'),
		'dnstracking' => __('Modify DNS Tracking - All'),
		'multidnstracking' => __('Modify DNS Tracking - Invidual'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'imports',
		'action' => 'view',
		$this->params['pass'][0],
	),
	));
?>
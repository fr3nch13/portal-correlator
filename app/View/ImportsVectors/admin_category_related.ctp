<?php 
// File: app/View/ImportsVectors/admin_category_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'Import.name' => array('content' => __('Import'), 'options' => array('sort' => 'Import.name')),
	'ImportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'ImportsVector.active')),
	'ImportsVector.created' => array('content' => __('Added to Import'), 'options' => array('sort' => 'ImportsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);


$td = array();
foreach ($imports_vectors as $i => $imports_vector)
{
	$active = $this->Wrap->yesNo($imports_vector['ImportsVector']['active']);
	
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $imports_vector['Vector']['id']));
	
	
	$dns_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($imports_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($imports_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($imports_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($imports_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($imports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $imports_vector['Vector']['id'])),
		$this->Html->link($imports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $imports_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($imports_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $imports_vector['Vector']['type'])),
		$dns_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($imports_vector['VectorDetail']['vt_lookup'], true),
		$this->Html->link($imports_vector['Import']['name'], array('controller' => 'imports', 'action' => 'view', $imports_vector['Import']['id'])),
		$active,
//		$this->Wrap->niceTime($imports_vector['Vector']['modified']),
		$this->Wrap->niceTime($imports_vector['ImportsVector']['created']),
		$this->Wrap->niceTime($imports_vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $imports_vector['ImportsVector']['id'],
	);
}

$use_multiselect = true;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Import Vectors'),
	'page_options' => $page_options,
	'search_placeholder' => __('Import Vectors'),
	'th' => $th,
	'td' => $td,
	));
?>
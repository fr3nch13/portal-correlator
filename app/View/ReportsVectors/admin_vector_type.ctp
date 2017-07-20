<?php 
// File: app/View/ReportsVectors/admin_vector_type.ctp


// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'Report.name' => array('content' => __('Report'), 'options' => array('sort' => 'Report.name')),
	//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'ReportsVector.created' => array('content' => __('Added'), 'options' => array('sort' => 'ReportsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($reports_vectors as $i => $reports_vector)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $reports_vector['Vector']['id']));
	
	
	$dns_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($categories_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($reports_vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($reports_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($categories_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($reports_vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($reports_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($reports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $reports_vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($reports_vector['Vector']['type']), array('admin' => false, 'controller' => 'vectors', 'action' => 'type', $reports_vector['Vector']['type'])),
		$dns_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($reports_vector['VectorDetail']['vt_lookup'], true),
		$this->Html->link($reports_vector['Report']['name'], array('controller' => 'reports', 'action' => 'view', $reports_vector['Report']['id'])),
//		$this->Wrap->niceTime($reports_vector['Vector']['modified']),
		$this->Wrap->niceTime($reports_vector['ReportsVector']['created']),
		$this->Wrap->niceTime($reports_vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $reports_vector['ReportsVector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Vectors'),
	'th' => $th,
	'td' => $td,
));
?>
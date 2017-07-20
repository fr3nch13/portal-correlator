<?php 
// File: app/View/ReportsVectors/admin_report_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'Report.name' => array('content' => __('Report'), 'options' => array('sort' => 'Report.name')),
//	'ReportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'ReportsVector.active')),
	//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'ReportsVector.created' => array('content' => __('Added to Report'), 'options' => array('sort' => 'ReportsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'multiselect' => true,
);

$td = array();
foreach ($reports_vectors as $i => $reports_vector)
{
	
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
		$this->Html->link($reports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $reports_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($reports_vector['Vector']['type']), array('admin' => false, 'controller' => 'vectors', 'action' => 'type', $reports_vector['Vector']['type'])),
		$dns_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($reports_vector['VectorDetail']['vt_lookup'], true),
		$this->Html->link($reports_vector['Report']['name'], array('controller' => 'reports', 'action' => 'view', $reports_vector['Report']['id'])),
//		$this->Wrap->yesNo($reports_vector['ReportsVector']['active']),
//		$this->Wrap->niceTime($reports_vector['Vector']['modified']),
		$this->Wrap->niceTime($reports_vector['ReportsVector']['created']),
		$this->Wrap->niceTime($reports_vector['Vector']['created']),
		'multiselect' => $reports_vector['ReportsVector']['id'],
	);
}


echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Report Vectors'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
?>
<?php 
// File: app/View/UploadsVectors/upload.ctp

$page_options = array();
$page_options2 = array();
	$page_options[] = $this->Html->link(__('Add Vectors'), array('action' => 'add', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to One Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign DNS Tracking to All Vectors'), array('action' => 'assign_dnstracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign Hexillion Tracking to All Vectors'), array('action' => 'assign_hexilliontracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign WHOIS Tracking to All Vectors'), array('action' => 'assign_whoistracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign VT Tracking to All Vectors'), array('action' => 'assign_vttracking', $this->params['pass'][0]));
	$use_multiselect = true;

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'hexillion_auto_lookup' => array('content' => __('Hexillion Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'UploadsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'UploadsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'UploadsVector.created' => array('content' => __('Added to File'), 'options' => array('sort' => 'UploadsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($uploads_vectors as $i => $uploads_vector)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $uploads_vector['Vector']['id']));
	$actions .= $this->Html->link(__('Remove'), array('action' => 'delete', $uploads_vector['UploadsVector']['id']),array('confirm' => 'Are you sure?'));
	
	$active = array(
		$this->Html->link($this->Wrap->yesNo($uploads_vector['UploadsVector']['active']), array('action' => 'toggle', 'active', $uploads_vector['UploadsVector']['id']),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
	
	$dns_auto_lookup = '&nbsp;';
	$hexillion_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($uploads_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Ipaddress']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Ipaddress']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($uploads_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Hostname']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Hostname']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$dns_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($uploads_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($uploads_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($uploads_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $uploads_vector['Vector']['id'])),
		$this->Html->link($uploads_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $uploads_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($uploads_vector['Vector']['type']), array('admin' => false, 'controller' => 'vectors', 'action' => 'type', $uploads_vector['Vector']['type'])),
		$dns_auto_lookup,
		$hexillion_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($uploads_vector['VectorDetail']['vt_lookup'], true),
		$active,
		$uploads_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($uploads_vector['UploadsVector']['created']),
		$this->Wrap->niceTime($uploads_vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $uploads_vector['UploadsVector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', _('Upload'), _('Vectors')),
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign Group'),
		'inactive' => __('Mark Inactive'),
		'active' => __('Mark Active'),
		'vttracking' => __('Mark for VirusTotal Tracking'),
		'dnstracking' => __('Modify DNS Tracking - All'),
		'multidnstracking' => __('Modify DNS Tracking - Invidual'),
		'hexilliontracking' => __('Modify Hexillion Tracking - All'),
		'multihexilliontracking' => __('Modify Hexillion Tracking - Invidual'),
		'whoistracking' => __('Modify WHOIS Tracking - All'),
		'multiwhoistracking' => __('Modify WHOIS Tracking - Invidual'),
		'vectortype' => __('Change Type - All'),
		'multivectortype' => __('Change Type - Invidual'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'uploads',
		'action' => 'view',
		$this->params['pass'][0],
	),
));
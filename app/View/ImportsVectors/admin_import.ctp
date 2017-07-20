<?php 
// File: app/View/ImportsVectors/unique.ctp

$page_options = array();
$page_options2 = array();
	$page_options[] = $this->Html->link(__('Assign All Vectors to One Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign DNS Tracking to All Vectors'), array('action' => 'assign_dnstracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign Hexillion Tracking to All Vectors'), array('action' => 'assign_hexilliontracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign WHOIS Tracking to All Vectors'), array('action' => 'assign_whoistracking', $this->params['pass'][0]));
//	$page_options2[] = $this->Html->link(__('Assign VT Tracking to All Vectors'), array('action' => 'assign_vttracking', $this->params['pass'][0]));


// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'hexillion_auto_lookup' => array('content' => __('Hexillion Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'ImportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'ImportsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
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
	
	$dns_auto_lookup = '';
	$hexillion_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '';
	if(isset($imports_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Ipaddress']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Ipaddress']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($imports_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($imports_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Hostname']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($imports_vector['Hostname']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($imports_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($imports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $imports_vector['Vector']['id'])),
		$this->Html->link($imports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $imports_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($imports_vector['Vector']['type']), array('admin' => false, 'controller' => 'vectors', 'action' => 'type', $imports_vector['Vector']['type'])),
		$dns_auto_lookup,
		$hexillion_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($imports_vector['VectorDetail']['vt_lookup'], true),
		$active,
		$imports_vector['Geoip']['country_iso'],
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
	'page_title' => __('Related %s %s', _('Import'), _('Vectors')),
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'search_placeholder' => __('%s %s', _('Import'), _('Vectors')),
	'th' => $th,
	'td' => $td,
));
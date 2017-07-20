<?php 
// File: app/View/Vectors/auto_tracking_dns.ctp

// content
$th = array();
$th['Vector.vector'] = array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector'));
$th['Vector.type'] = array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type'));
$th['dns_auto_lookup'] = array('content' => __('Tracking Level'));
$th['VectorSourceFirst.source_type'] = array('content' => __('First Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type'));
$th['VectorSourceFirst.created'] = array('content' => __('First Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created'));
$th['VectorSourceLast.source_type'] = array('content' => __('Last Source'), 'options' => array('sort' => 'VectorSourceLast.source_type'));
$th['VectorSourceLast.created'] = array('content' => __('Last Source Added'), 'options' => array('sort' => 'VectorSourceLast.created'));
$th['dns_checked'] = array('content' => __('DNS Last Checked'));
$th['dns_updated'] = array('content' => __('DNS Last Updated'));
$th['dns_checked_virustotal'] = array('content' => __('VT Last Checked'));
$th['dns_updated_virustotal'] = array('content' => __('VT Last Updated'));
if(AuthComponent::user('role') == 'admin')
{
	$th['dns_checked_passivetotal'] = array('content' => __('PT Last Checked'));
	$th['dns_updated_passivetotal'] = array('content' => __('PT Last Updated'));
}
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = true;


$td = array();
foreach ($vectors as $i => $vector)
{
	$dns_auto_lookup = 0;
	if($vector['Ipaddress']['id']) $dns_auto_lookup = $vector['Ipaddress']['dns_auto_lookup'];
	elseif($vector['Hostname']['id']) $dns_auto_lookup = $vector['Hostname']['dns_auto_lookup'];
	$dns_checked = 0;
	if($vector['Ipaddress']['id']) $dns_checked = $vector['Ipaddress']['dns_checked'];
	elseif($vector['Hostname']['id']) $dns_checked = $vector['Hostname']['dns_checked'];
	$dns_updated = 0;
	if($vector['Ipaddress']['id']) $dns_updated = $vector['Ipaddress']['dns_updated'];
	elseif($vector['Hostname']['id']) $dns_updated = $vector['Hostname']['dns_updated'];
	
	$dns_checked_virustotal = 0;
	if($vector['Ipaddress']['id']) $dns_checked_virustotal = $vector['Ipaddress']['dns_checked_virustotal'];
	elseif($vector['Hostname']['id']) $dns_checked_virustotal = $vector['Hostname']['dns_checked_virustotal'];
	$dns_updated_virustotal = 0;
	if($vector['Ipaddress']['id']) $dns_updated_virustotal = $vector['Ipaddress']['dns_updated_virustotal'];
	elseif($vector['Hostname']['id']) $dns_updated_virustotal = $vector['Hostname']['dns_updated_virustotal'];
	
	$dns_checked_passivetotal = 0;
	if($vector['Ipaddress']['id']) $dns_checked_passivetotal = $vector['Ipaddress']['dns_checked_passivetotal'];
	elseif($vector['Hostname']['id']) $dns_checked_passivetotal = $vector['Hostname']['dns_checked_passivetotal'];
	$dns_updated_passivetotal = 0;
	if($vector['Ipaddress']['id']) $dns_updated_passivetotal = $vector['Ipaddress']['dns_updated_passivetotal'];
	elseif($vector['Hostname']['id']) $dns_updated_passivetotal = $vector['Hostname']['dns_updated_passivetotal'];
	
	$td[$i] = array();
	$td[$i]['Vector.vector'] = $this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id']));
	$td[$i]['Vector.type'] = $this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type']));
	$td[$i]['dns_auto_lookup'] = $this->Wrap->dnsAutoLookupLevel($dns_auto_lookup, true);
	$td[$i]['VectorSourceFirst.source_type'] = $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']);
	$td[$i]['VectorSourceFirst.created'] = $this->Wrap->niceTime($vector['VectorSourceFirst']['created']);
	$td[$i]['VectorSourceLast.source_type'] = $this->Wrap->niceWord($vector['VectorSourceLast']['source_type']);
	$td[$i]['VectorSourceLast.created'] = $this->Wrap->niceTime($vector['VectorSourceLast']['created']);
	$td[$i]['dns_checked'] = $this->Wrap->niceTime($dns_checked);
	$td[$i]['dns_updated'] = $this->Wrap->niceTime($dns_updated);
	$td[$i]['dns_checked_virustotal'] = $this->Wrap->niceTime($dns_checked_virustotal);
	$td[$i]['dns_updated_virustotal'] = $this->Wrap->niceTime($dns_updated_virustotal);
	if(AuthComponent::user('role') == 'admin')
	{
		$td[$i]['dns_checked_passivetotal'] = $this->Wrap->niceTime($dns_checked_passivetotal);
		$td[$i]['dns_updated_passivetotal'] = $this->Wrap->niceTime($dns_updated_passivetotal);
	}
	$td[$i]['actions'] = array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])).
			$this->Html->link(__('Turn Off'), array('action' => 'auto_tracking_dns_off', $vector['Vector']['id']), array('confirm' => 'Are you sure?')),
			array('class' => 'actions'),
		);
	$td[$i]['multiselect'] = $vector['Vector']['id'];
}

$page_description = __('List of Vectors that have been marked for automatic DNS tracking.'); 
if(AuthComponent::user('role') == 'admin')
{
	$page_description = __('List of Vectors that have been marked for automatic DNS tracking (VirusTotal and PassiveTotal only show up for admins).');
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('DNS Auto Tracking Vectors - All'),
	'page_description' => $page_description,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'dnstracking' => __('Modify DNS Tracking - All'),
		'multidnstracking' => __('Modify DNS Tracking - Invidual'),
		'vectortype' => __('Change Type - All'),
		'multivectortype' => __('Change Type - Invidual'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'vectors',
		'action' => 'auto_tracking_dns',
		'page' => 1,
	),
));
?>
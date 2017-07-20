<?php 
// File: app/View/Vectors/auto_tracking_whois.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'whois_auto_lookup' => array('content' => __('Tracking Level')),
	'VectorSourceFirst.source_type' => array('content' => __('First Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type')),
	'VectorSourceFirst.created' => array('content' => __('First Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created')),
	'VectorSourceLast.source_type' => array('content' => __('Last Source'), 'options' => array('sort' => 'VectorSourceLast.source_type')),
	'VectorSourceLast.created' => array('content' => __('Last Source Added'), 'options' => array('sort' => 'VectorSourceLast.created')),
	'whois_checked' => array('content' => __('Last Checked')),
	'whois_updated' => array('content' => __('Last Updated')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$whois_auto_lookup = 0;
	if($vector['Ipaddress']['id']) $whois_auto_lookup = $vector['Ipaddress']['whois_auto_lookup'];
	elseif($vector['Hostname']['id']) $whois_auto_lookup = $vector['Hostname']['whois_auto_lookup'];
	$whois_checked = 0;
	if($vector['Ipaddress']['id']) $whois_checked = $vector['Ipaddress']['whois_checked'];
	elseif($vector['Hostname']['id']) $whois_checked = $vector['Hostname']['whois_checked'];
	$whois_updated = 0;
	if($vector['Ipaddress']['id']) $whois_updated = $vector['Ipaddress']['whois_updated'];
	elseif($vector['Hostname']['id']) $whois_updated = $vector['Hostname']['whois_updated'];
	
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type'])),
		$this->Wrap->whoisAutoLookupLevel($whois_auto_lookup, true),
		$this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceFirst']['created']),
		$this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceLast']['created']),
		$this->Wrap->niceTime($whois_checked),
		$this->Wrap->niceTime($whois_updated),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])).
			$this->Html->link(__('Turn Off'), array('action' => 'auto_tracking_whois_off', $vector['Vector']['id']), array('confirm' => 'Are you sure?')),
			array('class' => 'actions'),
		),
		'multiselect' => $vector['Vector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('WHOIS Auto Tracking Vectors - All'),
	'page_description' => __('List of Vectors that have been marked for automatic WHOIS tracking.'),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'whoistracking' => __('Modify WHOIS Tracking - All'),
		'multiwhoistracking' => __('Modify WHOIS Tracking - Invidual'),
		'vectortype' => __('Change Type - All'),
		'multivectortype' => __('Change Type - Invidual'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'vectors',
		'action' => 'auto_tracking_whois',
		'page' => 1,
	),
));
?>
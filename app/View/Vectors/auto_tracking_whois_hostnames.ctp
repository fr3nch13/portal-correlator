<?php 
// File: app/View/Vectors/auto_tracking_whois_hostnames.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'Hostname.whois_auto_lookup' => array('content' => __('Tracking Level'), 'options' => array('sort' => 'Hostname.whois_auto_lookup')),
	'VectorSourceFirst.source_type' => array('content' => __('First Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type')),
	'VectorSourceFirst.created' => array('content' => __('First Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created')),
	'VectorSourceLast.source_type' => array('content' => __('Last Source'), 'options' => array('sort' => 'VectorSourceLast.source_type')),
	'VectorSourceLast.created' => array('content' => __('Last Source Added'), 'options' => array('sort' => 'VectorSourceLast.created')),
	'Hostname.whois_checked' => array('content' => __('Last Checked'), 'options' => array('sort' => 'Hostname.whois_checked')),
	'Hostname.whois_updated' => array('content' => __('Last Updated'), 'options' => array('sort' => 'Hostname.whois_updated')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type'])),
		$this->Wrap->whoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup'], true),
		$this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceFirst']['created']),
		$this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceLast']['created']),
		$this->Wrap->niceTime($vector['Hostname']['whois_checked']),
		$this->Wrap->niceTime($vector['Hostname']['whois_updated']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])).
			$this->Html->link(__('Turn Off'), array('action' => 'auto_tracking_whois_off', $vector['Vector']['id']), array('confirm' => 'Are you sure?')),
			array('class' => 'actions'),
		),
		'multiselect' => $vector['Vector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('WHOIS Auto Tracking Vectors - Hostnames'),
	'page_description' => __('List of Hostnames that have been marked for automatic WHOIS tracking.'),
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
		'action' => 'auto_tracking_whois_hostnames',
		'page' => 1,
	),
));
?>
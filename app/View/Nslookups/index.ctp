<?php 
// File: app/View/Nslookups/index.ctp

$page_options = array(
	$this->Html->link(__('Show All'), array(0)),
	$this->Html->link(__('Show Only Local'), array('local')),
	$this->Html->link(__('Show Only Remote'), array('remote')),
);

// content
$th = array(
	'VectorHostname.vector' => array('content' => __('Hostname'), 'options' => array('sort' => 'VectorHostname.vector')),
	'VectorIpaddress.vector' => array('content' => __('Ip Address'), 'options' => array('sort' => 'VectorIpaddress.vector')),
	'Nslookup.ttl' => array('content' => __('Reported TTL'), 'options' => array('sort' => 'Nslookup.ttl')),
//	'Nslookup.ttl_manual' => array('content' => __('Manual TTL'), 'options' => array('sort' => 'Nslookup.ttl_manual')),
//	'Nslookup.ttl_dynamic' => array('content' => __('Dynamic TTL'), 'options' => array('sort' => 'Nslookup.ttl_dynamic')),
	'Nslookup.source' => array('content' => __('Source'), 'options' => array('sort' => 'Nslookup.source')),
	'Nslookup.first_seen' => array('content' => __('First Seen'), 'options' => array('sort' => 'Nslookup.first_seen')),
	'Nslookup.last_seen' => array('content' => __('Last Seen'), 'options' => array('sort' => 'Nslookup.last_seen')),
	'Nslookup.modified' => array('content' => __('Updated'), 'options' => array('sort' => 'Nslookup.modified')),
	'Nslookup.created' => array('content' => __('Created'), 'options' => array('sort' => 'Nslookup.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($nslookups as $i => $nslookup)
{

	$td[$i] = array(
		$this->Html->link($nslookup['VectorHostname']['vector'], array('controller' => 'vectors', 'action' => 'view', $nslookup['VectorHostname']['id'])),
		$this->Html->link($nslookup['VectorIpaddress']['vector'], array('controller' => 'vectors', 'action' => 'view', $nslookup['VectorIpaddress']['id'])),
		$nslookup['Nslookup']['ttl'],
//		$nslookup['Nslookup']['ttl_manual'],
//		$nslookup['Nslookup']['ttl_dynamic'],
		$this->Wrap->sourceUser($nslookup['Nslookup']['source']),
		$this->Wrap->niceTime($nslookup['Nslookup']['first_seen']),
		$this->Wrap->niceTime($nslookup['Nslookup']['last_seen']),
		$this->Wrap->niceTime($nslookup['Nslookup']['modified']),
		$this->Wrap->niceTime($nslookup['Nslookup']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $nslookup['Nslookup']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%sDNS Records', $lookup_type),
	'search_placeholder' => __('%sDNS Records', $lookup_type),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
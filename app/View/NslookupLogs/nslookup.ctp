<?php 
// File: app/View/NslookupLogs/nslookup.ctp

// content
$th = array(
	'NslookupHostname.vector' => array('content' => __('Hostname'), 'options' => array('sort' => 'NslookupHostname.vector')),
	'NslookupIpaddress.vector' => array('content' => __('Ip Address'), 'options' => array('sort' => 'NslookupIpaddress.vector')),
	'Nslookup.ttl' => array('content' => __('TTL'), 'options' => array('sort' => 'Nslookup.ttl')),
	'Nslookup.ttl_dynamic' => array('content' => __('Dynamic TTL'), 'options' => array('sort' => 'Nslookup.ttl_dynamic')),
	'Nslookup.ttl_manual' => array('content' => __('Manual TTL'), 'options' => array('sort' => 'Nslookup.ttl_manual')),
	'Nslookup.source' => array('content' => __('Source'), 'options' => array('sort' => 'Nslookup.source')),
	'Nslookup.created' => array('content' => __('Created'), 'options' => array('sort' => 'Nslookup.created')),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($nslookup_logs as $i => $nslookup_log)
{
	$td[$i] = array(
		$this->Html->link($nslookup_log['NslookupHostname']['vector'], array('controller' => 'vectors', 'action' => 'view', $nslookup_log['NslookupHostname']['id'])),
		$this->Html->link($nslookup_log['NslookupIpaddress']['vector'], array('controller' => 'vectors', 'action' => 'view', $nslookup_log['NslookupIpaddress']['id'])),
		$nslookup_log['NslookupLog']['ttl'],
		$nslookup_log['NslookupLog']['ttl_dynamic'],
		$nslookup_log['NslookupLog']['ttl_manual'],
		$this->Wrap->sourceUser($nslookup_log['NslookupLog']['source']),
		$this->Wrap->niceTime($nslookup_log['NslookupLog']['created']),
/*
		array(
			$this->Html->link(__('View'), array('action' => 'view', $nslookup_log['Nslookup']['id'])),
			array('class' => 'actions'),
		),
*/
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Nslookup History'),
	'search_placeholder' => __('Nslookup History'),
	'th' => $th,
	'td' => $td,
	));
?>
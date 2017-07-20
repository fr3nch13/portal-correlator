<?php 
// File: app/View/WhoisLog/whois.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'WhoisLog.source' => array('content' => __('Source'), 'options' => array('sort' => 'WhoisLog.source')),
	'WhoisLog.createdDate' => array('content' => __('Whois Created'), 'options' => array('sort' => 'WhoisLog.createdDate')),
	'WhoisLog.updatedDate' => array('content' => __('Whois Updated'), 'options' => array('sort' => 'WhoisLog.updatedDate')),
	'WhoisLog.expiresDate' => array('content' => __('Whois Expires'), 'options' => array('sort' => 'WhoisLog.expiresDate')),
//	'WhoisLog.modified' => array('content' => __('Updated'), 'options' => array('sort' => 'WhoisLog.modified')),
	'WhoisLog.created' => array('content' => __('Added'), 'options' => array('sort' => 'WhoisLog.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($whois_logs as $i => $whois_log)
{
	$td[$i] = array(
		$this->Html->link($whois_log['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $whois_log['Vector']['id'])),
		$this->Wrap->sourceUser($whois_log['WhoisLog']['source']),
		$this->Wrap->niceDay($whois_log['WhoisLog']['createdDate']),
		$this->Wrap->niceDay($whois_log['WhoisLog']['updatedDate']),
		$this->Wrap->niceDay($whois_log['WhoisLog']['expiresDate']),
//		$this->Wrap->niceTime($whois_log['WhoisLog']['modified']),
		$this->Wrap->niceTime($whois_log['WhoisLog']['created']),
		array(
			$this->Html->link(__('View Whois Log Details'), array('controller' => 'whois_logs', 'action' => 'view', $whois_log['WhoisLog']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Whois Log Records'),
	'search_placeholder' => __('Whois Log Records'),
	'th' => $th,
	'td' => $td,
	));
?>
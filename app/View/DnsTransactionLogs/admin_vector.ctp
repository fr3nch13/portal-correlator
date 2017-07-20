<?php 
// File: app/View/DnsTransactionLogs/admin_vector.ctp

// content
$th = array(
	'DnsTransactionLog.automatic' => array('content' => __('Manual/Automatic'), 'options' => array('sort' => 'DnsTransactionLog.automatic')),
	'DnsTransactionLog.result_count' => array('content' => __('# Results'), 'options' => array('sort' => 'DnsTransactionLog.result_count')),
	'DnsTransactionLog.sources' => array('content' => __('Sources'), 'options' => array('sort' => 'DnsTransactionLog.sources')),
	'DnsTransactionLog.created' => array('content' => __('Date'), 'options' => array('sort' => 'DnsTransactionLog.created')),
);

$td = array();
foreach ($dns_transaction_logs as $i => $dns_transaction_log)
{
	$td[$i] = array(
		$this->Wrap->automaticSwitch($dns_transaction_log['DnsTransactionLog']['automatic']),
		$dns_transaction_log['DnsTransactionLog']['result_count'],
		$this->Wrap->sourcesUser($dns_transaction_log['DnsTransactionLog']['sources']),
		$this->Wrap->niceTime($dns_transaction_log['DnsTransactionLog']['created']),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('DNS Transaction Logs'),
	'page_description' => __('The transactions logs of when this Vector was directly looked up, either manually or automatically.'),
	'th' => $th,
	'td' => $td,
	));
?>
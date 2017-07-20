<?php 
// File: app/View/WhoisTransactionLogs/admin_vector.ctp

// content
$th = array(
	'WhoisTransactionLog.automatic' => array('content' => __('Manual/Automatic'), 'options' => array('sort' => 'WhoisTransactionLog.automatic')),
	'WhoisTransactionLog.result_count' => array('content' => __('# Results'), 'options' => array('sort' => 'WhoisTransactionLog.result_count')),
	'WhoisTransactionLog.sources' => array('content' => __('Sources'), 'options' => array('sort' => 'WhoisTransactionLog.sources')),
	'WhoisTransactionLog.created' => array('content' => __('Date'), 'options' => array('sort' => 'WhoisTransactionLog.created')),
);

$td = array();
foreach ($whois_transaction_logs as $i => $whois_transaction_log)
{
	$td[$i] = array(
		$this->Wrap->automaticSwitch($whois_transaction_log['WhoisTransactionLog']['automatic']),
		$whois_transaction_log['WhoisTransactionLog']['result_count'],
		$this->Wrap->sourcesUser($whois_transaction_log['WhoisTransactionLog']['sources']),
		$this->Wrap->niceTime($whois_transaction_log['WhoisTransactionLog']['created']),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Whois Transaction Logs'),
	'page_description' => __('The transactions logs of when this Vector was directly looked up, either manually or automatically.'),
	'th' => $th,
	'td' => $td,
	));
?>
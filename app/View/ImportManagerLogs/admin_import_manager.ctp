<?php 
// File: app/View/ImportManagerLogs/admin_import_manager.ctp

// content
$th = array();
	$th['ImportManagerLog.created'] = array('content' => __('Created'), 'options' => array('sort' => 'ImportManagerLog.created'));
	$th['ImportManagerLog.starttime'] = array('content' => __('Start Time'), 'options' => array('sort' => 'ImportManagerLog.starttime'));
	$th['ImportManagerLog.endtime'] = array('content' => __('End Time'), 'options' => array('sort' => 'ImportManagerLog.endtime'));
	$th['ImportManagerLog.success'] = array('content' => __('Success'), 'options' => array('sort' => 'ImportManagerLog.success'));
	$th['ImportManagerLog.num_added'] = array('content' => __('# Added'), 'options' => array('sort' => 'ImportManagerLog.num_added'));
	$th['ImportManagerLog.num_empty'] = array('content' => __('# Empty'), 'options' => array('sort' => 'ImportManagerLog.num_empty'));
	$th['ImportManagerLog.num_duplicate'] = array('content' => __('# Duplicate'), 'options' => array('sort' => 'ImportManagerLog.num_duplicate'));
	$th['ImportManagerLog.num_failed'] = array('content' => __('# Failed'), 'options' => array('sort' => 'ImportManagerLog.num_failed'));
	$th['ImportManagerLog.msg'] = array('content' => __('Last Message'), 'options' => array('sort' => 'ImportManagerLog.msg'));

$td = array();
foreach ($import_manager_logs as $i => $import_manager_log)
{
	$td[$i] = array();
	$td[$i]['ImportManagerLog.created'] = $this->Wrap->niceTime($import_manager_log['ImportManagerLog']['created']);
	$td[$i]['ImportManagerLog.starttime'] = $this->Wrap->niceTime($import_manager_log['ImportManagerLog']['starttime']);
	$td[$i]['ImportManagerLog.endtime'] = $this->Wrap->niceTime($import_manager_log['ImportManagerLog']['endtime']);
	$td[$i]['ImportManagerLog.success'] = $this->Wrap->yesNo($import_manager_log['ImportManagerLog']['success']);
	$td[$i]['ImportManagerLog.num_added'] = $import_manager_log['ImportManagerLog']['num_added'];
	$td[$i]['ImportManagerLog.num_empty'] = $import_manager_log['ImportManagerLog']['num_empty'];
	$td[$i]['ImportManagerLog.num_duplicate'] = $import_manager_log['ImportManagerLog']['num_duplicate'];
	$td[$i]['ImportManagerLog.num_failed'] = $import_manager_log['ImportManagerLog']['num_failed'];
	$td[$i]['ImportManagerLog.msg'] = $import_manager_log['ImportManagerLog']['msg'];
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Logs for an Import Manager'),
	'search_placeholder' => __('Logs'),
	'th' => $th,
	'td' => $td,
	));
?>
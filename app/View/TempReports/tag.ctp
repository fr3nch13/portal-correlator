<?php 
// File: app/View/TempReports/tag.ctp


$page_options = array(
	$this->Html->link(__('Add Report'), array('action' => 'add')),
	$this->Html->link(__('Add Multiple Reports'), array('action' => 'batchadd')),
);

// content
$th = array(
	'TempReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'TempReport.name')),
	'ReportType.name' => array('content' => __('Report Group'), 'options' => array('sort' => 'ReportType.name')),
	'TempReport.mysource' => array('content' => __('User Source'), 'options' => array('sort' => 'TempReport.mysource')),
	'TempReport.public' => array('content' => __('Share State'), 'options' => array('sort' => 'TempReport.public')),
	'TempReport.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'TempReport.modified')),
	'TempReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'TempReport.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($temp_reports as $i => $temp_report)
{
	$td[$i] = array(
		$this->Html->link($temp_report['TempReport']['name'], array('action' => 'view', $temp_report['TempReport']['id'])),
		$this->Html->link($temp_report['ReportType']['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $temp_report['ReportType']['id'])),
		$temp_report['TempReport']['mysource'],
		$this->Wrap->publicState($temp_report['TempReport']['public']),
		$this->Wrap->niceTime($temp_report['TempReport']['modified']),
		$this->Wrap->niceTime($temp_report['TempReport']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $temp_report['TempReport']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $temp_report['TempReport']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_report['TempReport']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('My Temp Reports'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
<?php 
// File: app/View/ReportTypes/manager_index.ctp

$page_options = array(
	$this->Html->link(__('Add Report Group'), array('action' => 'add')),
);

// content
$th = array(
	'ReportType.name' => array('content' => __('Name'), 'options' => array('sort' => 'ReportType.name')),
	'ReportType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'ReportType.holder')),
	'ReportType.active' => array('content' => __('Active'), 'options' => array('sort' => 'ReportType.active')),
	'ReportType.created' => array('content' => __('Created'), 'options' => array('sort' => 'ReportType.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($reportTypes as $i => $reportType)
{
	$td[$i] = array(
		$this->Html->link($reportType['ReportType']['name'], array('action' => 'view', $reportType['ReportType']['id'])),
		array(
			$this->Form->postLink($this->Wrap->yesNo($reportType['ReportType']['holder']),array('action' => 'setdefault', 'holder', $reportType['ReportType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($reportType['ReportType']['active']),array('action' => 'toggle', 'active', $reportType['ReportType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		$this->Wrap->niceTime($reportType['ReportType']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $reportType['ReportType']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $reportType['ReportType']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $reportType['ReportType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Manage Report Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
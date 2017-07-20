<?php 
// File: app/View/ReportTypes/index.ctp

$page_options = array(
//	$this->Html->link(__('Add Report Group'), array('action' => 'add')),
);

// content
$th = array(
	'ReportType.name' => array('content' => __('Name'), 'options' => array('sort' => 'ReportType.name')),
	'ReportType.created' => array('content' => __('Created'), 'options' => array('sort' => 'ReportType.created')),
	'ReportType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'ReportType.holder')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($reportTypes as $i => $reportType)
{
	$td[$i] = array(
		$this->Html->link($reportType['ReportType']['name'], array('action' => 'view', $reportType['ReportType']['id'])),
		$this->Wrap->niceTime($reportType['ReportType']['created']),
		$this->Wrap->yesNo($reportType['ReportType']['holder']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $reportType['ReportType']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
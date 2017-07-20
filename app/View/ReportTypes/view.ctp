<?php 
// File: app/View/ReportTypes/view.ctp
$details = array();
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($reportType['ReportType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($reportType['ReportType']['modified']));


$page_options = array();
// $page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $reportType['ReportType']['id']));
// $page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $reportType['ReportType']['id']),array('confirm' => 'Are you sure?'));

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'reports',
	'title' => __('Active Reports'),
	'url' => array('controller' => 'reports', 'action' => 'report_type', $reportType['ReportType']['id']),
);
$tabs[] = array(
	'key' => 'tempreports',
	'title' => __('My Temp Reports'),
	'url' => array('controller' => 'temp_reports', 'action' => 'report_type', $reportType['ReportType']['id']),
);
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($reportType['ReportType']['desc']),
);

$stats[] = array(
	'id' => 'reports',
	'name' => __('Public Reports'), 
	'value' => $reportType['ReportType']['counts']['Report.public'], 
	'tab' => array('tabs', '1'), // the tab to display
);

$stats[] = array(
	'id' => 'temp_reports',
	'name' => __('My Temp Reports'), 
	'value' => $reportType['ReportType']['counts']['TempReport.mine'], 
	'tab' => array('tabs', '2'), // the tab to display
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Report Group'). ': '. $reportType['ReportType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
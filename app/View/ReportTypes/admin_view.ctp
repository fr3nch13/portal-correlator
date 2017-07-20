<?php 
// File: app/View/ReportTypes/admin_view.ctp
$details = array();
$org_group = $this->Html->link(__('Global'), array('controller' => 'org_groups', 'action' => 'view', '0'));
if(isset($reportType['OrgGroup']['id']) and $reportType['OrgGroup']['id'])
{
	$org_group = $this->Html->link($reportType['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $reportType['OrgGroup']['id']));
}
$details[] = array('name' => __('Org Group'), 'value' => $org_group);
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($reportType['ReportType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($reportType['ReportType']['modified']));


$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $reportType['ReportType']['id']));
$page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $reportType['ReportType']['id']),array('confirm' => 'Are you sure?'));

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'reports',
	'title' => __('Reports'),
	'url' => array('controller' => 'reports', 'action' => 'report_type', $reportType['ReportType']['id']),
);
/*
$tabs[] = array(
	'key' => 'tempreports',
	'title' => __('My Temp Reports'),
	'url' => array('controller' => 'temp_reports', 'action' => 'report_type', $reportType['ReportType']['id']),
);
*/
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($reportType['ReportType']['desc']),
);

$stats[] = array(
	'id' => 'reports',
	'name' => __('Reports'), 
	'value' => $reportType['ReportType']['counts']['Report.all'], 
	'tab' => array('tabs', '1'), // the tab to display
);
/*
$stats[] = array(
	'id' => 'temp_reports',
	'name' => __('My Temp Reports'), 
	'value' => $reportType['ReportType']['counts']['TempReport.all'], 
	'tab' => array('tabs', '2'), // the tab to display
);
*/

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Report Group'). ': '. $reportType['ReportType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
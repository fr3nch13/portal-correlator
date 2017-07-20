<?php 
$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $temp_report['TempReport']['id']));
$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_report['TempReport']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Form->postLink(__('Mark Reviewed'),array('action' => 'reviewed', $temp_report['TempReport']['id']),array('confirm' => 'Are you sure?', 'class' => 'button_red'));

$details_blocks = array();
$details_blocks[1][1] = array(
	'title' => __('Details'),
	'details' => array(),
);
$details_blocks[1][1]['details'][] = array('name' => __('Owner'), 'value' => $this->Html->link($temp_report['User']['name'], array('controller' => 'users', 'action' => 'view', $temp_report['User']['id'])));
$details_blocks[1][1]['details'][] = array('name' => __('Org Group'), 'value' => $temp_report['OrgGroup']['name']);
$details_blocks[1][1]['details'][] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($temp_report['TempReport']['public']));
if($temp_report['ReportType']['org_group_id'] == AuthComponent::user('org_group_id'))
{
	$details_blocks[1][1]['details'][] = array('name' => __('Report Group'), 'value' => $this->Html->link($temp_report['ReportType']['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $temp_report['ReportType']['id'])). '&nbsp;');
}
else
{
	$details_blocks[1][1]['details'][] = array('name' => __('Report Group'), 'value' => $temp_report['ReportType']['name']. '&nbsp;');
}
$details_blocks[1][1]['details'][] = array('name' => __('User Source'), 'value' => $temp_report['TempReport']['mysource']);

$details_blocks[1][2] = array(
	'title' => __('Assessments'),
	'details' => array(),
);
$pathObject = $temp_report;
if(isset($pathObject['AdAccount'])) unset($pathObject['AdAccount']);
$details_blocks[1][2]['details'][] = array('name' => __('SAC'), 'value' => $this->Contacts->makePath($pathObject));
$details_blocks[1][2]['details'][] = array('name' => __('NIH Risk'), 'value' => $this->Html->link($temp_report['AssessmentNihRisk']['name'], array('controller' => 'assessment_nih_risks', 'action' => 'view', $temp_report['AssessmentNihRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Customer Risk'), 'value' => $this->Html->link($temp_report['AssessmentCustRisk']['name'], array('controller' => 'assessment_cust_risks', 'action' => 'view', $temp_report['AssessmentCustRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Targeted/APT'), 'value' => $this->Wrap->yesNoUnknown($temp_report['TempReport']['targeted']));
$details_blocks[1][2]['details'][] = array('name' => __('Compromised Date'), 'value' => $this->Wrap->niceDay($temp_report['TempReport']['compromise_date']));

$details_blocks[1][3] = array(
	'title' => __('Victim'),
	'details' => array(),
);
$details_blocks[1][3]['details'][] = array('name' => __('AD Account'), 'value' => $this->Contacts->linkAdAccount($temp_report));
$details_blocks[1][3]['details'][] = array('name' => __('IP Address'), 'value' => $temp_report['TempReport']['victim_ip']);
$details_blocks[1][3]['details'][] = array('name' => __('MAC Address'), 'value' => $temp_report['TempReport']['victim_mac']);
$details_blocks[1][3]['details'][] = array('name' => __('Asset Tag'), 'value' => $temp_report['TempReport']['victim_asset_tag']);

$details_blocks[1][4] = array(
	'title' => __('Dates'),
	'details' => array(),
);
$details_blocks[1][4]['details'][] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($temp_report['TempReport']['created']));
$details_blocks[1][4]['details'][] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($temp_report['TempReport']['modified']));


$stats = array(
	array(
		'id' => 'vectorsTempReport',
		'name' => __('All %s', __('Vectors')), 
		'ajax_count_url' => array('controller' => 'temp_reports_vectors', 'action' => 'temp_report', $temp_report['TempReport']['id']),
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'TempUploadsPublic',
		'name' => __('All %s', __('Files')),  
		'ajax_count_url' => array('controller' => 'temp_uploads', 'action' => 'temp_report', $temp_report['TempReport']['id']),
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'categoriesSignatures',
		'name' => __('All %s', __('Signatures')),  
		'ajax_count_url' => array('controller' => 'reports_signatures', 'action' => 'temp_report', $temp_report['TempReport']['id']),
		'tab' => array('tabs', '3'), // the tab to display
	),
	array(
		'id' => 'tagsTempReport',
		'name' => __('Tags'),  
		'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'temp_report', $temp_report['TempReport']['id']),
		'tab' => array('tabs', '4'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'vectors',
		'title' => __('All %s', __('Vectors')),
		'url' => array('controller' => 'temp_reports_vectors', 'action' => 'temp_report', $temp_report['TempReport']['id']),
	),
	array(
		'key' => 'uploads',
		'title' =>  __('All %s', __('Files')),
		'url' => array('controller' => 'temp_uploads', 'action' => 'temp_report', $temp_report['TempReport']['id']),
	),
	array(
		'key' => 'signatures',
		'title' => __('All %s', __('Signatures')),
		'url' => array('controller' => 'reports_signatures', 'action' => 'temp_report', $temp_report['TempReport']['id']),
	),
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'temp_report', $temp_report['TempReport']['id']),
	),
	array(
		'key' => 'description',
		'title' => __('Description'),
		'content' => $this->Wrap->descView($temp_report['TempReportsDetail']['desc']),
	),
);

if($temp_report['TempReport']['user_id'] == AuthComponent::user('id'))
{
	$tabs[] = array(
		'key' => 'notes',
		'title' => __('Private Notes'),
		'content' => $this->Wrap->descView($temp_report['TempReportsDetail']['desc_private']),
	);
}

echo $this->element('Utilities.page_view_columns', array(
	'page_title' => __('%s: %s', __('Temp Report'), $temp_report['TempReport']['name']),
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
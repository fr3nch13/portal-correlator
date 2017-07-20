<?php 
$page_options = array();
if($report['Report']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $report['Report']['id']));
	$page_options['delete'] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $report['Report']['id']),array('confirm' => 'Are you sure?'));
}
elseif($is_editor)
{
	$page_options['edit_editor'] = $this->Html->link(__('Edit'), array('action' => 'edit_editor', $report['Report']['id']));
}
elseif($is_contributor)
{
	$page_options['edit_contributor'] = $this->Html->link(__('Edit'), array('action' => 'edit_contributor', $report['Report']['id']));
}

$details_blocks = array();
$details_blocks[1][1] = array(
	'title' => __('Details'),
	'details' => array(),
);
$details_blocks[1][1]['details'][] = array('name' => __('Owner'), 'value' => $this->Html->link($report['User']['name'], array('controller' => 'users', 'action' => 'view', $report['User']['id'])));
$details_blocks[1][1]['details'][] = array('name' => __('Org Group'), 'value' => $report['OrgGroup']['name']);
$details_blocks[1][1]['details'][] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($report['Report']['public']));
if($report['ReportType']['org_group_id'] == AuthComponent::user('org_group_id'))
{
	$details_blocks[1][1]['details'][] = array('name' => __('Report Group'), 'value' => $this->Html->link($report['ReportType']['name'], array('admin' => false, 'controller' => 'report_types', 'action' => 'view', $report['ReportType']['id'])). '&nbsp;');
}
else
{
	$details_blocks[1][1]['details'][] = array('name' => __('Report Group'), 'value' => $report['ReportType']['name']. '&nbsp;');
}
$details_blocks[1][1]['details'][] = array('name' => __('User Source'), 'value' => $report['Report']['mysource']);

$details_blocks[1][2] = array(
	'title' => __('Assessments'),
	'details' => array(),
);
$pathObject = $report;
if(isset($pathObject['AdAccount'])) unset($pathObject['AdAccount']);
$details_blocks[1][2]['details'][] = array('name' => __('SAC'), 'value' => $this->Contacts->makePath($pathObject));
$details_blocks[1][2]['details'][] = array('name' => __('NIH Risk'), 'value' => $this->Html->link($report['AssessmentNihRisk']['name'], array('controller' => 'assessment_nih_risks', 'action' => 'view', $report['AssessmentNihRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Customer Risk'), 'value' => $this->Html->link($report['AssessmentCustRisk']['name'], array('controller' => 'assessment_cust_risks', 'action' => 'view', $report['AssessmentCustRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Targeted/APT'), 'value' => $this->Wrap->yesNoUnknown($report['Report']['targeted']));
$details_blocks[1][2]['details'][] = array('name' => __('Compromised Date'), 'value' => $this->Wrap->niceDay($report['Report']['compromise_date']));

$details_blocks[1][3] = array(
	'title' => __('Victim'),
	'details' => array(),
);
$details_blocks[1][3]['details'][] = array('name' => __('AD Account'), 'value' => $this->Contacts->linkAdAccount($report));
$details_blocks[1][3]['details'][] = array('name' => __('IP Address'), 'value' => $report['Report']['victim_ip']);
$details_blocks[1][3]['details'][] = array('name' => __('MAC Address'), 'value' => $report['Report']['victim_mac']);
$details_blocks[1][3]['details'][] = array('name' => __('Asset Tag'), 'value' => $report['Report']['victim_asset_tag']);

$details_blocks[1][4] = array(
	'title' => __('Dates'),
	'details' => array(),
);
$details_blocks[1][4]['details'][] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($report['Report']['created']));
$details_blocks[1][4]['details'][] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($report['Report']['modified']));
$details_blocks[1][4]['details'][] = array('name' => __('Reviewed'), 'value' => $this->Wrap->niceTime($report['Report']['reviewed']));


$stats = array();
$tabs = array();
//
if($report['Report']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$tabs['all_vectors'] = $stats['all_vectors'] = array(
		'id' => 'all_vectors',
		'name' => __('All Vectors'), 
		'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'report', $report['Report']['id']), 
	);
}
$tabs['active_vectors'] = $stats['active_vectors'] = array(
	'id' => 'active_vectors',
	'name' => __('Active Vectors'), 
	'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'report', $report['Report']['id'], 1), 
);
if($report['Report']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$tabs['inactive_vectors'] = $stats['inactive_vectors'] = array(
		'id' => 'inactive_vectors',
		'name' => __('Inactive Vectors'), 
		'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'report', $report['Report']['id'], 0), 
	);
}
$tabs['vectors_unique'] = $stats['vectors_unique'] = array(
	'id' => 'vectors_unique',
	'name' => __('Unique Vectors'), 
	'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'unique', $report['Report']['id']), 
);	
$tabs['reports'] = $stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Related Reports'), 
	'ajax_url' => array('controller' => 'reports', 'action' => 'report', $report['Report']['id']),
);	
$tabs['reports_vectors'] = $stats['reports_vectors'] = array(
	'id' => 'reports_vectors',
	'name' => __('Related Report Vectors'), 
	'ajax_url' => array('controller' => 'reports_vectors', 'action' => 'report_related', $report['Report']['id']),
);
$tabs['categories'] = $stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Related Categories'), 
	'ajax_url' => array('controller' => 'categories', 'action' => 'report', $report['Report']['id']),
);
$tabs['categories_vectors'] = $stats['categories_vectors'] = array(
	'id' => 'categories_vectors',
	'name' => __('Related Category Vectors'), 
	'ajax_url' => array('controller' => 'categories_vectors', 'action' => 'report_related', $report['Report']['id']), 
);
$tabs['imports'] = $stats['imports'] = array(
	'id' => 'imports',
	'name' => __('Related Imports'), 
	'ajax_url' => array('controller' => 'imports', 'action' => 'report', $report['Report']['id']),
);
$tabs['imports_vectors'] = $stats['imports_vectors'] = array(
	'id' => 'imports_vectors',
	'name' => __('Related Import Vectors'), 
	'ajax_url' => array('controller' => 'imports_vectors', 'action' => 'report_related', $report['Report']['id']),
);
$tabs['fisma_systems'] = $stats['fisma_systems'] = array(
	'id' => 'fisma_systems',
	'name' => __('FISMA Systems'), 
	'ajax_url' => array('controller' => 'fisma_systems', 'action' => 'report', $report['Report']['id']),
);
$tabs['fisma_inventories'] = $stats['fisma_inventories'] = array(
	'id' => 'fisma_inventories',
	'name' => __('FISMA Inventories'), 
	'ajax_url' => array('controller' => 'fisma_inventories', 'action' => 'report', $report['Report']['id']),
);
if($report['Report']['user_id'] == AuthComponent::user('id') or $this->Common->isAdmin())
{
	$tabs['files'] = $stats['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'), 
		'ajax_url' => array('controller' => 'uploads', 'action' => 'report', $report['Report']['id']),
	);
	$tabs['temp_files'] = $stats['temp_files'] = array(
		'id' => 'temp_files',
		'name' => __('Temp Files'), 
		'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'report', $report['Report']['id'], 'admin' => false),
	);
	$tabs['editors'] = $stats['editors'] = array(
		'id' => 'editors',
		'name' => __('Editors'), 
		'ajax_url' => array('controller' => 'reports_editors', 'action' => 'report', $report['Report']['id'], 'admin' => false),
	);
	$tabs['signatures'] = $stats['signatures'] = array(
		'id' => 'signatures',
		'name' => __('Signatures'), 
		'ajax_url' => array('controller' => 'reports_signatures', 'action' => 'report', $report['Report']['id'], 'admin' => false), 
	);
	$tabs['tags'] = $stats['tags'] = array(
		'id' => 'tags',
		'name' => __('Tags'), 
		'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'report', $report['Report']['id']),
	);
}
else
{
	$tabs['files'] = $stats['files'] = array(
		'id' => 'files',
		'name' => __('Attached Files'), 
		'ajax_url' => array('controller' => 'uploads', 'action' => 'report', $report['Report']['id']),
	);
	if($is_editor or $is_contributor or $this->Common->isAdmin())
	{
		$tabs['temp_files'] = $stats['temp_files'] = array(
			'id' => 'temp_files',
			'name' => __('Temp Files'), 
			'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'report', $report['Report']['id'], 'admin' => false),
		);
		$tabs['signatures'] = $stats['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'), 
			'ajax_url' => array('controller' => 'reports_signatures', 'action' => 'report', $report['Report']['id'], 'admin' => false),
		);
		$tabs['tags'] = $stats['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'), 
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'report', $report['Report']['id']),
		);
	}
	else
	{

		$tabs['signatures'] = $stats['signatures'] = array(
			'id' => 'signatures',
			'name' => __('Signatures'), 
			'ajax_url' => array('controller' => 'reports_signatures', 'action' => 'report', $report['Report']['id']),
		);
		$tabs['tags'] = $stats['tags'] = array(
			'id' => 'tags',
			'name' => __('Tags'), 
			'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'report', $report['Report']['id']),
		);
	}
}
$tabs['description'] = array(
	'id' => 'description',
	'name' => __('Description'),
	'content' => $this->Wrap->descView($report['ReportsDetail']['desc']),
);

if($report['Report']['user_id'] == AuthComponent::user('id'))
{
	$tabs['notes'] = array(
		'id' => 'notes',
		'name' => __('Private Notes'),
		'content' => $this->Wrap->descView($report['ReportsDetail']['desc_private']),
	);
}

echo $this->element('Utilities.page_view_columns', array(
	'page_title' => __('%s: %s', __('Report'), $report['Report']['name']),
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
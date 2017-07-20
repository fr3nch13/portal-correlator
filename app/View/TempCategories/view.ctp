<?php 
$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $temp_category['TempCategory']['id']));
$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_category['TempCategory']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Form->postLink(__('Mark Reviewed'),array('action' => 'reviewed', $temp_category['TempCategory']['id']),array('confirm' => 'Are you sure?', 'class' => 'button_red'));

$details_blocks = array();
$details_blocks[1][1] = array(
	'title' => __('Details'),
	'details' => array(),
);
$details_blocks[1][1]['details'][] = array('name' => __('Owner'), 'value' => $this->Html->link($temp_category['User']['name'], array('controller' => 'users', 'action' => 'view', $temp_category['User']['id'])));
$details_blocks[1][1]['details'][] = array('name' => __('Org Group'), 'value' => $temp_category['OrgGroup']['name']);
$details_blocks[1][1]['details'][] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($temp_category['TempCategory']['public']));
if($temp_category['CategoryType']['org_group_id'] == AuthComponent::user('org_group_id'))
{
	$details_blocks[1][1]['details'][] = array('name' => __('Category Group'), 'value' => $this->Html->link($temp_category['CategoryType']['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $temp_category['CategoryType']['id'])). '&nbsp;');
}
else
{
	$details_blocks[1][1]['details'][] = array('name' => __('Category Group'), 'value' => $temp_category['CategoryType']['name']. '&nbsp;');
}
$details_blocks[1][1]['details'][] = array('name' => __('User Source'), 'value' => $temp_category['TempCategory']['mysource']);

$details_blocks[1][2] = array(
	'title' => __('Assessments'),
	'details' => array(),
);
$pathObject = $temp_category;
if(isset($pathObject['AdAccount'])) unset($pathObject['AdAccount']);
$details_blocks[1][2]['details'][] = array('name' => __('SAC'), 'value' => $this->Contacts->makePath($pathObject));
$details_blocks[1][2]['details'][] = array('name' => __('NIH Risk'), 'value' => $this->Html->link($temp_category['AssessmentNihRisk']['name'], array('controller' => 'assessment_nih_risks', 'action' => 'view', $temp_category['AssessmentNihRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Customer Risk'), 'value' => $this->Html->link($temp_category['AssessmentCustRisk']['name'], array('controller' => 'assessment_cust_risks', 'action' => 'view', $temp_category['AssessmentCustRisk']['id'])));
$details_blocks[1][2]['details'][] = array('name' => __('Targeted/APT'), 'value' => $this->Wrap->yesNoUnknown($temp_category['TempCategory']['targeted']));
$details_blocks[1][2]['details'][] = array('name' => __('Compromised Date'), 'value' => $this->Wrap->niceDay($temp_category['TempCategory']['compromise_date']));

$details_blocks[1][3] = array(
	'title' => __('Victim'),
	'details' => array(),
);
$details_blocks[1][3]['details'][] = array('name' => __('AD Account'), 'value' => $this->Contacts->linkAdAccount($temp_category));
$details_blocks[1][3]['details'][] = array('name' => __('IP Address'), 'value' => $temp_category['TempCategory']['victim_ip']);
$details_blocks[1][3]['details'][] = array('name' => __('MAC Address'), 'value' => $temp_category['TempCategory']['victim_mac']);
$details_blocks[1][3]['details'][] = array('name' => __('Asset Tag'), 'value' => $temp_category['TempCategory']['victim_asset_tag']);

$details_blocks[1][4] = array(
	'title' => __('Dates'),
	'details' => array(),
);
$details_blocks[1][4]['details'][] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($temp_category['TempCategory']['created']));
$details_blocks[1][4]['details'][] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($temp_category['TempCategory']['modified']));

$stats = array();
$tabs = array();

$stats['vectors'] = array(
	'id' => 'vectors',
	'name' => __('All %s', __('Vectors')), 
	'ajax_url' => array('controller' => 'temp_categories_vectors', 'action' => 'temp_category', $temp_category['TempCategory']['id']), 
	'tab' => array('tabs', '1'), // the tab to display
);
$tabs['vectors'] = array(
	'id' => 'vectors',
	'name' => __('All %s', __('Vectors')), 
	'ajax_url' => array('controller' => 'temp_categories_vectors', 'action' => 'temp_category', $temp_category['TempCategory']['id']),
);

$stats['files'] = array(
	'id' => 'files',
	'name' => __('All %s', __('Files')), 
	'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'temp_category', $temp_category['TempCategory']['id']), 
	'tab' => array('tabs', '2'), // the tab to display
);
$tabs['files'] = array(
	'id' => 'files',
	'name' =>  __('All %s', __('Files')),
	'ajax_url' => array('controller' => 'temp_uploads', 'action' => 'temp_category', $temp_category['TempCategory']['id']),
);

$stats['signatures'] =array(
	'id' => 'signatures',
	'name' => __('All %s', __('Signatures')), 
	'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'temp_category', $temp_category['TempCategory']['id']), 
	'tab' => array('tabs', '3'), // the tab to display
);
$tabs['signatures'] = array(
	'id' => 'signatures',
	'name' => __('All %s', __('Signatures')), 
	'ajax_url' => array('controller' => 'categories_signatures', 'action' => 'temp_category', $temp_category['TempCategory']['id']),
);

$stats['tags'] =array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'temp_category', $temp_category['TempCategory']['id']),
	'tab' => array('tabs', '4'), // the tab to display
);
$tabs['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'),
	'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'temp_category', $temp_category['TempCategory']['id']),
);

$tabs['description'] = array(
	'id' => 'description',
	'name' => __('Description'),
	'content' => $this->Wrap->descView($temp_category['TempCategoriesDetail']['desc']),
);

if($temp_category['TempCategory']['user_id'] == AuthComponent::user('id'))
{
	$tabs['notes'] = array(
		'id' => 'notes',
		'name' => __('Private Notes'),
		'content' => $this->Wrap->descView($temp_category['TempCategoriesDetail']['desc_private']),
	);
}

echo $this->element('Utilities.page_view_columns', array(
	'page_title' => __('%s: %s', __('Temp Category'), $temp_category['TempCategory']['name']),
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

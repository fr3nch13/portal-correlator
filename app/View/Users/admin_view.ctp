<?php 
// File: app/View/Users/admin_view.ctp

$page_options = array(
//	$this->Form->postLink(__('Toggle Active State'),array('action' => 'toggle', 'active', $user['User']['id']),array('confirm' => 'Are you sure?')),
	$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
//	$this->Form->postLink(__('Delete'),array('action' => 'delete', $user['User']['id']),array('confirm' => 'Are you sure?')),
);

$org_group = 'None';
if(isset($user['OrgGroup']['id']) and $user['OrgGroup']['id'])
{
	$org_group = $this->Html->link($user['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $user['OrgGroup']['id']));
}
$admin_emails = $this->Form->postLink($this->Wrap->yesNo($user['User']['admin_emails']), array('action' => 'toggle', 'admin_emails', $user['User']['id']),array('confirm' => 'Are you sure?'));

$details_left = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('AD Account'), 'value' => $user['User']['adaccount']),
	array('name' => __('Active'), 'value' => $this->Wrap->yesNo($user['User']['active'])),
	array('name' => __('Role'), 'value' => $this->Wrap->userRole($user['User']['role'])),
	array('name' => __('Org Group'), 'value' => $org_group),
	array('name' => __('Admin Emails?'), 'value' => $admin_emails),
);
$details_right = array(
	array('name' => __('Last Login'), 'value' => $this->Wrap->niceTime($user['User']['lastlogin'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($user['User']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($user['User']['modified'])),
);

$stats = array(
	array(
		'id' => 'categoriesUserAll',
		'name' => __('All Categories'), 
		'value' => $user['User']['counts']['Category.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'categoriesUserPublic',
		'name' => __('Shared Categories'), 
		'value' => $user['User']['counts']['Category.public'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'reportsUserAll',
		'name' => __('All Reports'), 
		'value' => $user['User']['counts']['Report.all'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'reportsUserPublic',
		'name' => __('Shared Reports'), 
		'value' => $user['User']['counts']['Report.public'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'uploadsUser',
		'name' => __('All Files'), 
		'value' => $user['User']['counts']['Upload.all'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
	array(
		'id' => 'uploadsUserPublic',
		'name' => __('Shared Files'), 
		'value' => $user['User']['counts']['Upload.public'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
/*
	array(
		'id' => 'tagsUser',
		'name' => __('Tags'), 
		'value' => $user['User']['counts']['Tag.all'], 
		'tab' => array('tabs', '5'), // the tab to display
	),
*/
);

$tabs = array(
	array(
		'key' => 'categories',
		'title' => __('All Categories'),
		'url' => array('controller' => 'categories', 'action' => 'user', $user['User']['id']),
	),
	array(
		'key' => 'reports',
		'title' => __('All Reports'),
		'url' => array('controller' => 'reports', 'action' => 'user', $user['User']['id']),
	),
	array(
		'key' => 'reports',
		'title' => __('All Files'),
		'url' => array('controller' => 'uploads', 'action' => 'user', $user['User']['id']),
	),
	array(
		'key' => 'logins',
		'title' => __('Login History'),
		'url' => array('controller' => 'login_histories', 'action' => 'user', $user['User']['id']),
	),
/*
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('admin' => false, 'plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'user', $user['User']['id']),
	),
*/
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('%s: %s', __('User'), $user['User']['name']),
	'page_options' => $page_options,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs' => $tabs,
));

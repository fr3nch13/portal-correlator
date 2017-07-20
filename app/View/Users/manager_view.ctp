<?php 
// File: app/View/Users/view.ctp

$page_options = array(
//	$this->Form->postLink(__('Toggle Active State'),array('action' => 'toggle', 'active', $user['User']['id']),array('confirm' => 'Are you sure?')),
	$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
);
$details_left = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('AD Account'), 'value' => $user['User']['adaccount']),
	array('name' => __('Active'), 'value' => $this->Wrap->yesNo($user['User']['active'])),
	array('name' => __('Role'), 'value' => $this->Wrap->userRole($user['User']['role'])),
);
$details_right = array(
	array('name' => __('Last Login'), 'value' => $this->Wrap->niceTime($user['User']['lastlogin'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($user['User']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($user['User']['modified'])),
);

$stats = array(
	array(
		'id' => 'categoriesUserPublic',
		'name' => __('Shared Categories'), 
		'value' => $user['User']['counts']['Category.public'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'reportsUserPublic',
		'name' => __('Shared Reports'), 
		'value' => $user['User']['counts']['Report.public'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'uploadsUser',
		'name' => __('Shared Files'), 
		'value' => $user['User']['counts']['Upload.public'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
/*
	array(
		'id' => 'tagsUser',
		'name' => __('Tags'), 
		'value' => $user['User']['counts']['Tagged.all'], 
		'tab' => array('tabs', '4'), // the tab to display
	),
*/
);

$tabs = array(
	array(
		'key' => 'categories',
		'title' => __('Shared Categories'),
		'url' => array('controller' => 'categories', 'action' => 'user', $user['User']['id'], 'manager' => false),
	),
	array(
		'key' => 'reports',
		'title' => __('Shared Reports'),
		'url' => array('controller' => 'reports', 'action' => 'user', $user['User']['id'], 'manager' => false),
	),
	array(
		'key' => 'reports',
		'title' => __('Shared Files'),
		'url' => array('controller' => 'uploads', 'action' => 'user', $user['User']['id'], 'manager' => false),
	),
/*
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'user', $user['User']['id']),
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
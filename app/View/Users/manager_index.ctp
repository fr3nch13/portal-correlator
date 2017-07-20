<?php 
// File: app/View/Users/admin_index.ctp

$page_options = array(
);

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'User.adaccount' => array('content' => __('AD Account'), 'options' => array('sort' => 'User.adaccount')),
	'User.active' => array('content' => __('Active'), 'options' => array('sort' => 'User.active')),
	'User.role' => array('content' => __('Role'), 'options' => array('sort' => 'User.role')),
	'User.lastlogin' => array('content' => __('Last Login'), 'options' => array('sort' => 'User.lastlogin')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($users as $i => $user)
{
	$td[$i] = array(
		$this->Html->link($user['User']['name'], array('controller' => 'users', 'action' => 'view', $user['User']['id'])),
		$this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		$user['User']['adaccount'],
		$this->Wrap->yesNo($user['User']['active']),
		$this->Wrap->userRole($user['User']['role']),
		$this->Wrap->niceTime($user['User']['lastlogin']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Manage Users'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
<?php 
// File: app/View/Users/admin_group.ctp

$page_options = array(
);

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'User.active' => array('content' => __('Active'), 'options' => array('sort' => 'User.active')),
	'User.role' => array('content' => __('Role'), 'options' => array('sort' => 'User.role')),
	'User.lastlogin' => array('content' => __('Last Login'), 'options' => array('sort' => 'User.lastlogin')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($users as $i => $user)
{
	$tmp = array('User' => $user['User']);
	$User = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$td[$i] = array(
		$User,
		$this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		array(
			$this->Form->postLink($this->Wrap->yesNo($user['User']['active']),array('action' => 'toggle', 'active', $user['User']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		$user['User']['role'],
		$this->Wrap->niceTime($user['User']['lastlogin']),
//		$this->Wrap->niceTime($user['User']['created']),
//		$this->Wrap->niceTime($user['User']['modified']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Users in Org Group'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
?>
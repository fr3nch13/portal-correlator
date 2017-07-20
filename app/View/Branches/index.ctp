<?php 

$th = array();
$th['Report.count'] = array('content' => __('# %s', __('Reports')));
$th['Category.count'] = array('content' => __('# %s', __('Categories')));

$td = array();
foreach ($branches as $i => $branch)
{
	$td[$i] = array();
	
	$td[$i]['Report.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'branch', $branch['Branch']['id']), 
		'url' => array('action' => 'view', $branch['Branch']['id'], 'tab' => 'reports'),
	));
	
	$td[$i]['Category.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'branch', $branch['Branch']['id']), 
		'url' => array('action' => 'view', $branch['Branch']['id'], 'tab' => 'categories'),
	));
}

$this->set(compact('page_title', 'page_options', 'th', 'td'));
$this->extend('Contacts.ContactsBranches/index');
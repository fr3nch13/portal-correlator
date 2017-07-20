<?php 

$th = array();
$th['Report.count'] = array('content' => __('# %s', __('Reports')));
$th['Category.count'] = array('content' => __('# %s', __('Categories')));

$td = array();
foreach ($divisions as $i => $division)
{
	$td[$i] = array();
	
	$td[$i]['Report.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'reports'),
	));
	
	$td[$i]['Category.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'categories'),
	));
}

$this->set(compact('page_title', 'page_options', 'th', 'td'));
$this->extend('Contacts.ContactsDivisions/index');
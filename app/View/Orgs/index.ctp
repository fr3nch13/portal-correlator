<?php

$th = array();
$th['Report.count'] = array('content' => __('# %s', __('Reports')));
$th['Category.count'] = array('content' => __('# %s', __('Categories')));

$td = array();
foreach ($orgs as $i => $org)
{
	$td[$i] = array();
	
	$td[$i]['Report.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'reports'),
	));
	
	$td[$i]['Category.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'categories'),
	));
}

$this->set(compact(array('th', 'td')));
$this->extend('Contacts.ContactsOrgs/index');
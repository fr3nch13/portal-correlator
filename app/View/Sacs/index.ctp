<?php 

$th = array();
$th['Report.count'] = array('content' => __('# %s', __('Reports')));
$th['Category.count'] = array('content' => __('# %s', __('Categories')));

$td = array();
foreach ($sacs as $i => $sac)
{
	$td[$i] = array();
	
	$td[$i]['Report.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'sac', $sac['Sac']['id']), 
		'url' => array('action' => 'view', $sac['Sac']['id'], 'tab' => 'reports'),
	));
	
	$td[$i]['Category.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'sac', $sac['Sac']['id']), 
		'url' => array('action' => 'view', $sac['Sac']['id'], 'tab' => 'categories'),
	));
}

$this->set(compact(array('th', 'td')));
$this->extend('Contacts.ContactsSacs/index');
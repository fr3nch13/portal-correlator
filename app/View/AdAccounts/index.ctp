<?php 

$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

$th = array();
$th['Report.count'] = array('content' => __('# %s', __('Reports')));
$th['Category.count'] = array('content' => __('# %s', __('Categories')));

$th = array_merge($th, $_th);

$td = $_td;

foreach ($adAccounts as $i => $adAccount)
{
	$td[$i] = array();
	
	$td[$i]['Report.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'reports', 'action' => 'ad_account', $adAccount['AdAccount']['id']), 
		'url' => array('action' => 'view', $adAccount['AdAccount']['id'], 'tab' => 'reports'),
	));
	
	$td[$i]['Category.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'categories', 'action' => 'ad_account', $adAccount['AdAccount']['id']), 
		'url' => array('action' => 'view', $adAccount['AdAccount']['id'], 'tab' => 'categories'),
	));
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
}

$this->set(compact(array('th', 'td')));

$this->extend('Contacts.ContactsAdAccounts/index');
<?php 

$tabs = array();
$stats = array();

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);

$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsAdAccounts/view');
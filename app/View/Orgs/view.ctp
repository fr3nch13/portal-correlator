<?php

$stats = array();
$tabs = array();

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'org', $org['Org']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'org', $org['Org']['id']),
);

$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'org', $org['Org']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'org', $org['Org']['id']),
);

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsOrgs/view');
<?php

$stats = array();
$tabs = array();

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'branch', $branch['Branch']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'branch', $branch['Branch']['id']),
);

$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'branch', $branch['Branch']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'branch', $branch['Branch']['id']),
);

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsBranches/view');

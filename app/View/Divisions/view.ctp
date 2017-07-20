<?php

$stats = array();
$tabs = array();

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'division', $division['Division']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'division', $division['Division']['id']),
);

$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'division', $division['Division']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'division', $division['Division']['id']),
);

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsDivisions/view');

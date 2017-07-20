<?php 

$stats = array();
$tabs = array();

$stats['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'sac', $sac['Sac']['id']),
);
$tabs['reports'] = array(
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => array('controller' => 'reports', 'action' => 'sac', $sac['Sac']['id']),
);

$stats['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'sac', $sac['Sac']['id']),
);
$tabs['categories'] = array(
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => array('controller' => 'categories', 'action' => 'sac', $sac['Sac']['id']),
);

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsSacs/view');
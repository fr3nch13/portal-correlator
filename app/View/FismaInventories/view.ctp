<?php 

$tabs = [];
$stats = [];

$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'),
	'ajax_url' => ['controller' => 'reports', 'action' => 'fisma_inventory', $record['FismaInventory']['id']],
];

$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'),
	'ajax_url' => ['controller' => 'categories', 'action' => 'fisma_inventory', $record['FismaInventory']['id']],
];

$this->set(compact(array('stats', 'tabs')));
$this->extend('Contacts.ContactsFismaInventories/view');
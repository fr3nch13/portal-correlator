<?php 
/**
 * File: app/View/Users/admin_admin.ctp
 *
 * The dashboard for the admin side of things
 */

$content = array(
	$this->element('Utilities.block', array(
		'title' => __('Users'),
		'items' => array(
			$this->Html->link(__('List'), array('controller' => 'users', 'action' => 'index', 'admin' => true)),
			$this->Html->link(__('Add'), array('controller' => 'users', 'action' => 'add', 'admin' => true)),
		),
	)),
	$this->element('Utilities.block', array(
		'title' => __('Categories'),
		'items' => array(
			$this->Html->link(__('List'), array('controller' => 'categories', 'action' => 'index', 'admin' => true)),
			$this->Html->link(__('Add'), array('controller' => 'categories', 'action' => 'add', 'admin' => true)),
		),
	)),
	$this->element('Utilities.block', array(
		'title' => __('Reports'),
		'items' => array(
			$this->Html->link(__('List'), array('controller' => 'reports', 'action' => 'index', 'admin' => true)),
			$this->Html->link(__('Add'), array('controller' => 'reports', 'action' => 'add', 'admin' => true)),
		),
	)),
	$this->element('Utilities.block', array(
		'title' => __('Vectors'),
		'items' => array(
			$this->Html->link(__('List'), array('controller' => 'vectors', 'action' => 'index', 'admin' => true)),
			$this->Html->link(__('Add'), array('controller' => 'vectors', 'action' => 'add', 'admin' => true)),
		),
	)),
	$this->element('Utilities.block', array(
		'title' => __('Tags'),
		'items' => array(
			$this->Html->link(__('List'), array('controller' => 'tags', 'action' => 'index', 'admin' => true)),
			$this->Html->link(__('Add'), array('controller' => 'tags', 'action' => 'add', 'admin' => true)),
		),
	)),
);

echo $this->element('Utilities.page_generic', array(
	'page_title' => __('Admin Dashboard'),
	'page_content' => implode("\n", $content),
));

?>

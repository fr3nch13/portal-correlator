<?php 
// File: app/View/CategoriesEditors/category.ctp

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'CategoriesEditor.type' => array('content' => __('Edit Level'), 'options' => array('sort' => 'CategoriesEditor.type')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($categories_editors as $i => $categories_editor)
{
	$td[$i] = array(
		$this->Html->link($categories_editor['User']['name'], array('controller' => 'users', 'action' => 'view', $categories_editor['User']['id'])),
		$this->Html->link($categories_editor['User']['email'], 'mailto:'. $categories_editor['User']['email']),
		$this->Local->editorType($categories_editor['CategoriesEditor']['type']),
		array(
			$this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $categories_editor['User']['id'])), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Editors and Contributors'),
	'th' => $th,
	'td' => $td,
	));
?>
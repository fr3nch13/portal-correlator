<?php 
// File: app/View/ReportsEditors/report.ctp

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'ReportsEditor.type' => array('content' => __('Edit Level'), 'options' => array('sort' => 'ReportsEditor.type')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($reports_editors as $i => $reports_editor)
{
	$td[$i] = array(
		$this->Html->link($reports_editor['User']['name'], array('controller' => 'users', 'action' => 'view', $reports_editor['User']['id'])),
		$this->Html->link($reports_editor['User']['email'], 'mailto:'. $reports_editor['User']['email']),
		$this->Local->editorType($reports_editor['ReportsEditor']['type']),
		array(
			$this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $reports_editor['User']['id'])), 
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
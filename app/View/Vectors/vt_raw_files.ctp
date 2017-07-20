<?php 
// File: app/View/Vectors/index.ctp

// content
$th = array(
	'Filename' => array('content' => __('File Name')),
	'Size' => array('content' => __('Size')),
	'Timestamp' => array('content' => __('Timestamp')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($raw_files as $i => $raw_file)
{
	$actions = array();
	$actions[] = $this->Html->link(__('View/Download'), $raw_file['link']);
	
	$td[$i] = array(
		$this->Html->link($raw_file['filename'], $raw_file['link']),
		$this->Wrap->formatBytes($raw_file['size']),
		$this->Wrap->niceTime($raw_file['mtime']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Raw Files'),
	'th' => $th,
	'td' => $td,
	'use_pagination' => false,
	'use_search' => false,
));
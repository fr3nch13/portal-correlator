<?php 
// File: app/View/Dumps/tag.ctp

$page_options = array(
);
// content
$th = array();
//	$th['Dump.type'] = array('content' => __('File Type'), 'options' => array('sort' => 'Dump.type'));
	$th['Dump.name'] = array('content' => __('Name'), 'options' => array('sort' => 'Dump.name'));
	$th['Dump.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Dump.filename'));
//	$th['Dump.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Dump.size'));
//	$th['Dump.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'Dump.md5'));

//	$th['Dump.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Dump.modified'));
	$th['Dump.created'] = array('content' => __('Dumped'), 'options' => array('sort' => 'Dump.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($dumps as $i => $dump)
{
	$td[$i] = array();
//	$td[$i]['Dump.type'] = $this->Wrap->fileIcon($dump['Dump']['type']);
	$td[$i]['Dump.name'] = $this->Html->link($dump['Dump']['name'], array('controller' => 'dumps', 'action' => 'view', $dump['Dump']['id']));
	$td[$i]['Dump.filename'] = $this->Html->link($dump['Dump']['filename'], array('controller' => 'dumps', 'action' => 'view', $dump['Dump']['id']));
//	$td[$i]['Dump.size'] = $this->Wrap->formatBytes($dump['Dump']['size']);
//	$td[$i]['Dump.md5'] = $dump['Dump']['md5'];
	
//	$td[$i]['Dump.modified'] = $this->Wrap->niceTime($dump['Dump']['modified']);
	$td[$i]['Dump.created'] = $this->Wrap->niceTime($dump['Dump']['created']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $dump['Dump']['id']));
	if(trim($dump['Dump']['filename']))
	{
		$actions .= $this->Html->link(__('Download'), array('action' => 'download', $dump['Dump']['id']));
	}
	$actions .= $this->Form->postLink(__('Delete'),array('action' => 'delete', $dump['Dump']['id']),array('confirm' => 'Are you sure?'));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('My Dumps'),
	'page_options' => $page_options,
	'search_placeholder' => __('Dumps'),
	'th' => $th,
	'td' => $td,
	));
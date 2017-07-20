<?php 
// File: app/View/Imports/vector.ctp

// content
$th = array();
	$th['Import.type'] = array('content' => __('Type'), 'options' => array('sort' => 'Import.type'));
	$th['Import.name'] = array('content' => __('Name'), 'options' => array('sort' => 'Import.filename'));
//	$th['Import.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Import.filename'));
	$th['Import.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Import.size'));
	$th['Import.sha1'] = array('content' => __('File sha1'), 'options' => array('sort' => 'Import.sha1'));
	$th['Import.created'] = array('content' => __('Imported'), 'options' => array('sort' => 'Import.created'));
//	$th['Import.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Import.modified'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($imports as $i => $import)
{
	$td[$i] = array();
	$td[$i]['Import.type'] = $this->Wrap->fileIcon($import['Import']['type']);
	$td[$i]['Import.name'] = $this->Html->link($import['Import']['name'], array('controller' => 'imports', 'action' => 'view', $import['Import']['id']));
//	$td[$i]['Import.filename'] = $this->Html->link($import['Import']['filename'], array('controller' => 'imports', 'action' => 'view', $import['Import']['id']));
	$td[$i]['Import.size'] = $this->Wrap->formatBytes($import['Import']['size']);
	$td[$i]['Import.sha1'] = $import['Import']['sha1'];
	$td[$i]['Import.created'] = $this->Wrap->niceTime($import['Import']['created']);
//	$td[$i]['Import.modified'] = $this->Wrap->niceTime($import['Import']['modified']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $import['Import']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $import['Import']['id']));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Imports'),
	'search_placeholder' => __('Imports'),
	'th' => $th,
	'td' => $td,
	));
?>
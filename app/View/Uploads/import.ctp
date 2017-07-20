<?php 
// File: app/View/Uploads/import.ctp

$page_options = array();

// content
$th = array();
	$th['Upload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'Upload.type'));
	$th['Upload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Upload.filename'));
	$th['Upload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource'));
	$th['Upload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Upload.size'));
//	$th['Upload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'Upload.md5'));
	$th['Upload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public'));
//	$th['Upload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified'));
	$th['Upload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'Upload.created'));
	$th['UploadAddedUser.name'] = array('content' => __('Uploaded By'), 'options' => array('sort' => 'UploadAddedUser.name'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($uploads as $i => $upload)
{
	$td[$i] = array();
	$td[$i]['Upload.type'] = $this->Wrap->fileIcon($upload['Upload']['type']);
	$td[$i]['Upload.filename'] = $td[$i]['Upload.filename'] = $this->Html->link($upload['Upload']['filename'], array('action' => 'view', $upload['Upload']['id']));
	$td[$i]['Upload.mysource'] = $upload['Upload']['mysource'];
	$td[$i]['Upload.size'] = $this->Wrap->formatBytes($upload['Upload']['size']);
	//$td[$i]['Upload.md5'] = $upload['Upload']['md5'];
	$td[$i]['Upload.public'] = $this->Wrap->publicState($upload['Upload']['public']);

	
	//$td[$i]['Upload.modified'] = $this->Wrap->niceTime($upload['Upload']['modified']);
	$td[$i]['Upload.created'] = $this->Wrap->niceTime($upload['Upload']['created']);
	$td[$i]['UploadAddedUser.name'] = $this->Html->link($upload['UploadAddedUser']['name'], array('controller' => 'users', 'action' => 'view', $upload['UploadAddedUser']['id']));
	
	$actions = $this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_import_upload', $this->params['pass'][0], $upload['Upload']['id']));
	$actions .= $this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id']));
	
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Category Files'),
	'page_options' => $page_options,
	'search_placeholder' => __('files'),
	'th' => $th,
	'td' => $td,
	));
?>
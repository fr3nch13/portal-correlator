<?php 
// File: app/View/Uploads/mine.ctp

$page_options = array(
	$this->Html->link(__('Add a File'), array('controller' => 'temp_uploads', 'action' => 'add')),
);
// content
$th = array();
	$th['Upload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'Upload.type'));
	$th['Upload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Upload.filename'));
	$th['UploadType.name'] = array('content' => __('File Group'), 'options' => array('sort' => 'UploadType.name'));
	$th['Upload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource'));
	$th['Category.name'] = array('content' => __('Category'), 'options' => array('sort' => 'Category.name'));
	$th['Report.name'] = array('content' => __('Report'), 'options' => array('sort' => 'Report.name'));
//	$th['Upload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Upload.size'));
//	$th['Upload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'Upload.md5'));
	$th['Upload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public'));
	$th['Upload.reviewed'] = array('content' => __('Reviewed'), 'options' => array('sort' => 'Upload.reviewed'));
	$th['Upload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified'));
	$th['Upload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'Upload.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($uploads as $i => $upload)
{
	$td[$i] = array();
	$td[$i]['Upload.type'] = $this->Wrap->fileIcon($upload['Upload']['type']);
	$td[$i]['Upload.filename'] = $this->Html->link($upload['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $upload['Upload']['id']));
	$td[$i]['UploadType.name'] = $this->Html->link($upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $upload['UploadType']['id']));
	$td[$i]['Upload.mysource'] = $upload['Upload']['mysource'];
	$td[$i]['Category.name'] = $this->Html->link($upload['Category']['name'], array('controller' => 'categories', 'action' => 'view', $upload['Category']['id']));
	$td[$i]['Report.name'] = $this->Html->link($upload['Report']['name'], array('controller' => 'reports', 'action' => 'view', $upload['Report']['id']));
//	$td[$i]['Upload.size'] = $this->Wrap->formatBytes($upload['Upload']['size']);
//	$td[$i]['Upload.md5'] = $upload['Upload']['md5'];
	$td[$i]['Upload.public'] = $this->Wrap->publicState($upload['Upload']['public']);
/*
	$td[$i]['Upload.public'] = array(
		$this->Form->postLink($this->Wrap->publicState($upload['Upload']['public']),array('action' => 'toggle', 'public', $upload['Upload']['id'], 'hash' => 'ui-tabs-4'),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
*/
	$td[$i]['Upload.reviewed'] = $this->Wrap->niceTime($upload['Upload']['reviewed']);	
	$td[$i]['Upload.modified'] = $this->Wrap->niceTime($upload['Upload']['modified']);
	$td[$i]['Upload.created'] = $this->Wrap->niceTime($upload['Upload']['created']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id']));
	$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $upload['Upload']['id']));
	$actions .= $this->Form->postLink(__('Delete'),array('action' => 'delete', $upload['Upload']['id']),array('confirm' => 'Are you sure?'));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('My Files'),
	'page_options' => $page_options,
	'search_placeholder' => __('files'),
	'th' => $th,
	'td' => $td,
	));
?>
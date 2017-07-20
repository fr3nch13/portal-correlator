<?php 
// File: app/View/TempUploads/tag.ctp

$page_options = array(
	$this->Html->link(__('Add a File'), array('action' => 'add')),
);
// content
$th = array();
	$th['TempUpload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'TempUpload.type'));
	$th['TempUpload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'TempUpload.filename'));
	$th['UploadType.name'] = array('content' => __('File Group'), 'options' => array('sort' => 'UploadType.name'));
	$th['TempUpload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'TempUpload.mysource'));
	$th['TempCategory.name'] = array('content' => __('Temp Category'), 'options' => array('sort' => 'TempCategory.name'));
	$th['TempReport.name'] = array('content' => __('Temp Report'), 'options' => array('sort' => 'TempReport.name'));
//	$th['TempUpload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'TempUpload.size'));
//	$th['TempUpload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'TempUpload.md5'));
	$th['TempUpload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'TempUpload.public'));
//	$th['TempUpload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'TempUpload.modified'));
	$th['TempUpload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'TempUpload.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($temp_uploads as $i => $temp_upload)
{
	$td[$i] = array();
	$td[$i]['TempUpload.type'] = $this->Wrap->fileIcon($temp_upload['TempUpload']['type']);
	$td[$i]['TempUpload.filename'] = $this->Html->link($temp_upload['TempUpload']['filename'], array('controller' => 'temp_uploads', 'action' => 'view', $temp_upload['TempUpload']['id']));
	$td[$i]['UploadType.name'] = $this->Html->link($temp_upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $temp_upload['UploadType']['id']));
	$td[$i]['TempUpload.mysource'] = $temp_upload['TempUpload']['mysource'];
	$td[$i]['TempCategory.name'] = $this->Html->link($temp_upload['TempCategory']['name'], array('controller' => 'temp_categories', 'action' => 'view', $temp_upload['TempCategory']['id']));
	$td[$i]['TempReport.name'] = $this->Html->link($temp_upload['TempReport']['name'], array('controller' => 'temp_reports', 'action' => 'view', $temp_upload['TempReport']['id']));
//	$td[$i]['TempUpload.size'] = $this->Wrap->formatBytes($temp_upload['TempUpload']['size']);
//	$td[$i]['TempUpload.md5'] = $temp_upload['TempUpload']['md5'];
	$td[$i]['TempUpload.public'] = $this->Wrap->publicState($temp_upload['TempUpload']['public']);
/*
	$td[$i]['TempUpload.public'] = array(
		$this->Form->postLink($this->Wrap->publicState($temp_upload['TempUpload']['public']),array('action' => 'toggle', 'public', $temp_upload['TempUpload']['id'], 'hash' => 'ui-tabs-5'),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
*/	
//	$td[$i]['TempUpload.modified'] = $this->Wrap->niceTime($temp_upload['TempUpload']['modified']);
	$td[$i]['TempUpload.created'] = $this->Wrap->niceTime($temp_upload['TempUpload']['created']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $temp_upload['TempUpload']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $temp_upload['TempUpload']['id']));
	$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $temp_upload['TempUpload']['id']));
	$actions .= $this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_upload['TempUpload']['id']),array('confirm' => 'Are you sure?'));
//	$actions .= $this->Form->postLink(__('Mark Reviewed'),array('action' => 'reviewed', $temp_upload['TempUpload']['id']),array('confirm' => 'Are you sure?', 'class' => 'button_red'));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Temp Files'),
	'page_options' => $page_options,
	'search_placeholder' => __('temp files'),
	'th' => $th,
	'td' => $td,
));
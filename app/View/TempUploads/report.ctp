<?php 
// File: app/View/TempUploads/temp_report.ctp

$page_options = array();
$page_options[] = $this->Html->link(__('Add File'), array('action' => 'add', 'report_id' => $report_id));

// content
$th = array();
	$th['TempUpload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'TempUpload.type'));
	$th['TempUpload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'TempUpload.filename'));
	$th['TempUpload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'TempUpload.mysource'));
	$th['TempUpload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'TempUpload.size'));
//	$th['TempUpload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'TempUpload.md5'));
	$th['TempUpload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'TempUpload.public'));
//	$th['TempUpload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'TempUpload.modified'));
	$th['TempUpload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'TempUpload.created'));
	$th['TempUploadAddedUser.name'] = array('content' => __('Uploaded By'), 'options' => array('sort' => 'TempUploadAddedUser.name'));
	if($report['Report']['user_id'] == AuthComponent::user('id'))
	{
		$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
	}

$td = array();
foreach ($temp_uploads as $i => $temp_upload)
{
	$td[$i] = array();
	$td[$i]['TempUpload.type'] = $this->Wrap->fileIcon($temp_upload['TempUpload']['type']);
	$td[$i]['TempUpload.filename'] = $temp_upload['TempUpload']['filename'];
	if($temp_upload['TempUpload']['user_id'] == AuthComponent::user('id'))
	{
		$td[$i]['TempUpload.filename'] = $this->Html->link($temp_upload['TempUpload']['filename'], array('action' => 'view', $temp_upload['TempUpload']['id']));
	}
	$td[$i]['TempUpload.mysource'] = $temp_upload['TempUpload']['mysource'];
	$td[$i]['TempUpload.size'] = $this->Wrap->formatBytes($temp_upload['TempUpload']['size']);
	//$td[$i]['TempUpload.md5'] = $temp_upload['TempUpload']['md5'];
	$td[$i]['TempUpload.public'] = $this->Wrap->publicState($temp_upload['TempUpload']['public']);
/*
	$td[$i]['TempUpload.public'] = array(
		$this->Form->postLink($this->Wrap->publicState($temp_upload['TempUpload']['public']),array('action' => 'toggle', 'public', $temp_upload['TempUpload']['id'], 'hash' => 'ui-tabs-5'),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
*/
	//$td[$i]['TempUpload.modified'] = $this->Wrap->niceTime($temp_upload['TempUpload']['modified']);
	$td[$i]['TempUpload.created'] = $this->Wrap->niceTime($temp_upload['TempUpload']['created']);
	$td[$i]['TempUploadAddedUser.name'] = $this->Html->link($temp_upload['TempUploadAddedUser']['name'], array('controller' => 'users', 'action' => 'view', $temp_upload['TempUploadAddedUser']['id']));
	
	if($report['Report']['user_id'] == AuthComponent::user('id'))
	{
		$actions = $this->Html->link(__('View'), array('action' => 'view', $temp_upload['TempUpload']['id']));
		$actions .= $this->Html->link(__('Download'), array('action' => 'download', $temp_upload['TempUpload']['id']));
		$actions .= $this->Html->link(__('Delete'),array('action' => 'delete', $temp_upload['TempUpload']['id'], 'hash' => 'ui-tabs-5'),array('confirm' => 'Are you sure?'));
		$actions .= $this->Html->link(__('Reviewed'), array('action' => 'reviewed', $temp_upload['TempUpload']['id'], $report_id), array('confirm' => __('Are you sure?'), 'class' => 'button_red'));
		$td[$i]['actions'] = array(
			$actions,
			array('class' => 'actions'),
		);
	}
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Temp Report Files'),
	'page_options' => $page_options,
	'search_placeholder' => __('Files'),
	'th' => $th,
	'td' => $td,
	));
?>
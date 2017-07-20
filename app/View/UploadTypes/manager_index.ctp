<?php 
// File: app/View/UploadTypes/manager_index.ctp

$page_options = array(
	$this->Html->link(__('Add File Group'), array('action' => 'add')),
);

// content
$th = array(
	'UploadType.name' => array('content' => __('Name'), 'options' => array('sort' => 'UploadType.name')),
	'UploadType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'UploadType.holder')),
	'UploadType.active' => array('content' => __('Active'), 'options' => array('sort' => 'UploadType.active')),
	'UploadType.created' => array('content' => __('Created'), 'options' => array('sort' => 'UploadType.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploadTypes as $i => $uploadType)
{
	$td[$i] = array(
		$this->Html->link($uploadType['UploadType']['name'], array('action' => 'view', $uploadType['UploadType']['id'])),
		array(
			$this->Form->postLink($this->Wrap->yesNo($uploadType['UploadType']['holder']),array('action' => 'setdefault', 'holder', $uploadType['UploadType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($uploadType['UploadType']['active']),array('action' => 'toggle', 'active', $uploadType['UploadType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		$this->Wrap->niceTime($uploadType['UploadType']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $uploadType['UploadType']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $uploadType['UploadType']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $uploadType['UploadType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Manage File Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
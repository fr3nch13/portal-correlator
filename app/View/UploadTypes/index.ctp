<?php 
// File: app/View/UploadTypes/index.ctp

$page_options = array(
//	$this->Html->link(__('Add File Group'), array('action' => 'add')),
);

// content
$th = array(
	'UploadType.name' => array('content' => __('Name'), 'options' => array('sort' => 'UploadType.name')),
	'UploadType.created' => array('content' => __('Created'), 'options' => array('sort' => 'UploadType.created')),
	'UploadType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'UploadType.holder')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploadTypes as $i => $uploadType)
{
	$td[$i] = array(
		$this->Html->link($uploadType['UploadType']['name'], array('action' => 'view', $uploadType['UploadType']['id'])),
		$this->Wrap->niceTime($uploadType['UploadType']['created']),
		$this->Wrap->yesNo($uploadType['UploadType']['holder']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $uploadType['UploadType']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('File Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
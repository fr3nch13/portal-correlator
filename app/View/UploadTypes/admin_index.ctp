<?php 
// File: app/View/UploadTypes/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Add File Group'), array('action' => 'add')),
);

// content
$th = array(
	'UploadType.name' => array('content' => __('Name'), 'options' => array('sort' => 'UploadType.name')),
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'UploadType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'UploadType.holder')),
	'UploadType.active' => array('content' => __('Active'), 'options' => array('sort' => 'UploadType.active')),
	'UploadType.created' => array('content' => __('Created'), 'options' => array('sort' => 'UploadType.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploadTypes as $i => $uploadType)
{
	$org_group = $this->Html->link(__('Global'), array('controller' => 'org_groups', 'action' => 'view', '0'));
	if(isset($uploadType['OrgGroup']['id']) and $uploadType['OrgGroup']['id'])
	{
		$org_group = $this->Html->link($uploadType['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $uploadType['OrgGroup']['id']));
	}
	$td[$i] = array(
		$this->Html->link($uploadType['UploadType']['name'], array('action' => 'view', $uploadType['UploadType']['id'])),
		$org_group,
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
	'page_title' => __('File Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
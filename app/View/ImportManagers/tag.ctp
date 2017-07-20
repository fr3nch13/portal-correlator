<?php 
// File: app/View/ImportManagers/tag.ctp

$page_options = array(
);

// content
$th = array(
	'ImportManager.name' => array('content' => __('Name'), 'options' => array('sort' => 'ImportManager.name')),
//	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'ImportManager.cron' => array('content' => __('Automatic'), 'options' => array('sort' => 'ImportManager.cron')),
	'ImportManager.auto_reviewed' => array('content' => __('Auto Reviewed'), 'options' => array('sort' => 'ImportManager.auto_reviewed')),
	'ImportManager.parser' => array('content' => __('Parser'), 'options' => array('sort' => 'ImportManager.parser')),
	'ImportManager.created' => array('content' => __('Created'), 'options' => array('sort' => 'ImportManager.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($importManagers as $i => $importManager)
{
/*
	$org_group = $this->Html->link(__('Global'), array('controller' => 'org_groups', 'action' => 'view', '0'));
	if(isset($importManager['OrgGroup']['id']) and $importManager['OrgGroup']['id'])
	{
		$org_group = $this->Html->link($importManager['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $importManager['OrgGroup']['id']));
	}
*/
	$td[$i] = array(
		$this->Html->link($importManager['ImportManager']['name'], array('action' => 'view', $importManager['ImportManager']['id'])),
//		$org_group,
		$this->Wrap->yesNo($importManager['ImportManager']['cron']),
		$this->Wrap->yesNo($importManager['ImportManager']['auto_reviewed']),
		$importManager['ImportManager']['parser'],
		$this->Wrap->niceTime($importManager['ImportManager']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $importManager['ImportManager']['id'])), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Import Managers'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
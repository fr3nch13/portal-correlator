<?php 
// File: app/View/CategoryTypes/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Add Category Group'), array('action' => 'add')),
);

// content
$th = array(
	'CategoryType.name' => array('content' => __('Name'), 'options' => array('sort' => 'CategoryType.name')),
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'CategoryType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'CategoryType.holder')),
	'CategoryType.active' => array('content' => __('Active'), 'options' => array('sort' => 'CategoryType.active')),
	'CategoryType.created' => array('content' => __('Created'), 'options' => array('sort' => 'CategoryType.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($categoryTypes as $i => $categoryType)
{
	$org_group = $this->Html->link(__('Global'), array('controller' => 'org_groups', 'action' => 'view', '0'));
	if(isset($categoryType['OrgGroup']['id']) and $categoryType['OrgGroup']['id'])
	{
		$org_group = $this->Html->link($categoryType['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $categoryType['OrgGroup']['id']));
	}
	$td[$i] = array(
		$this->Html->link($categoryType['CategoryType']['name'], array('action' => 'view', $categoryType['CategoryType']['id'])),
		$org_group,
		array(
			$this->Form->postLink($this->Wrap->yesNo($categoryType['CategoryType']['holder']),array('action' => 'setdefault', 'holder', $categoryType['CategoryType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($categoryType['CategoryType']['active']),array('action' => 'toggle', 'active', $categoryType['CategoryType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		$this->Wrap->niceTime($categoryType['CategoryType']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $categoryType['CategoryType']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $categoryType['CategoryType']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $categoryType['CategoryType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Category Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
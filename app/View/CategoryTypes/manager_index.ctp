<?php 
// File: app/View/CategoryTypes/manager_index.ctp

$page_options = array(
	$this->Html->link(__('Add Category Group'), array('action' => 'add')),
);

// content
$th = array(
	'CategoryType.name' => array('content' => __('Name'), 'options' => array('sort' => 'CategoryType.name')),
	'CategoryType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'CategoryType.holder')),
	'CategoryType.active' => array('content' => __('Active'), 'options' => array('sort' => 'CategoryType.active')),
	'CategoryType.created' => array('content' => __('Created'), 'options' => array('sort' => 'CategoryType.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($categoryTypes as $i => $categoryType)
{
	$td[$i] = array(
		$this->Html->link($categoryType['CategoryType']['name'], array('action' => 'view', $categoryType['CategoryType']['id'])),
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
	'page_title' => __('Manage Category Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
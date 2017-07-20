<?php 
// File: app/View/TempCategories/index.ctp


$page_options = array(
	$this->Html->link(__('Add Category'), array('controller' => 'temp_categories', 'action' => 'add')),
);

// content
$th = array(
	'TempCategory.name' => array('content' => __('Name'), 'options' => array('sort' => 'TempCategory.name')),
	'CategoryType.name' => array('content' => __('Category Group'), 'options' => array('sort' => 'CategoryType.name')),
	'Category.mysource' => array('content' => __('User Source'), 'options' => array('sort' => 'Category.mysource')),
	'TempCategory.public' => array('content' => __('Share State'), 'options' => array('sort' => 'TempCategory.public')),
	'TempCategory.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'TempCategory.modified')),
	'TempCategory.created' => array('content' => __('Created'), 'options' => array('sort' => 'TempCategory.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($temp_categories as $i => $temp_category)
{
	$td[$i] = array(
		$this->Html->link($temp_category['TempCategory']['name'], array('action' => 'view', $temp_category['TempCategory']['id'])),
		$this->Html->link($temp_category['CategoryType']['name'], array('admin' => false, 'controller' => 'category_types', 'action' => 'view', $temp_category['CategoryType']['id'])),
		$temp_category['TempCategory']['mysource'],
		$this->Wrap->publicState($temp_category['TempCategory']['public']),
		$this->Wrap->niceTime($temp_category['TempCategory']['modified']),
		$this->Wrap->niceTime($temp_category['TempCategory']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $temp_category['TempCategory']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $temp_category['TempCategory']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_category['TempCategory']['id']),array('confirm' => 'Are you sure?')).
			$this->Form->postLink(__('Reviewed'),array('action' => 'reviewed', $temp_category['TempCategory']['id']),array('confirm' => 'Are you sure?', 'class' => 'button_red')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('My Temp Categories'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
<?php 
// File: app/View/CategoryTypes/index.ctp

$page_options = array(
//	$this->Html->link(__('Add Category Group'), array('action' => 'add')),
);

// content
$th = array(
	'CategoryType.name' => array('content' => __('Name'), 'options' => array('sort' => 'CategoryType.name')),
	'CategoryType.created' => array('content' => __('Created'), 'options' => array('sort' => 'CategoryType.created')),
	'CategoryType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'CategoryType.holder')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($categoryTypes as $i => $categoryType)
{
	$td[$i] = array(
		$this->Html->link($categoryType['CategoryType']['name'], array('action' => 'view', $categoryType['CategoryType']['id'])),
		$this->Wrap->niceTime($categoryType['CategoryType']['created']),
		$this->Wrap->yesNo($categoryType['CategoryType']['holder']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $categoryType['CategoryType']['id'])),
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
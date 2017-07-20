<?php 
// File: app/View/VectorTypes/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Add Vector Group'), array('action' => 'add')),
);

// content
$th = array(
	'VectorType.name' => array('content' => __('Name'), 'options' => array('sort' => 'VectorType.name')),
	'VectorType.created' => array('content' => __('Created'), 'options' => array('sort' => 'VectorType.created')),
	'VectorType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'VectorType.holder')),
	'VectorType.bad' => array('content' => __('Benign'), 'options' => array('sort' => 'VectorType.bad')),
	'VectorType.active' => array('content' => __('Active'), 'options' => array('sort' => 'VectorType.active')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vectorTypes as $i => $vectorType)
{
	$td[$i] = array(
		$this->Html->link($vectorType['VectorType']['name'], array('action' => 'view', $vectorType['VectorType']['id'])),
		$this->Wrap->niceTime($vectorType['VectorType']['created']),
		array(
			$this->Form->postLink($this->Wrap->yesNo($vectorType['VectorType']['holder']),array('action' => 'setdefault', 'holder', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($vectorType['VectorType']['bad']),array('action' => 'toggle', 'bad', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($vectorType['VectorType']['active']),array('action' => 'toggle', 'active', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vectorType['VectorType']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $vectorType['VectorType']['id'])).
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Vector Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
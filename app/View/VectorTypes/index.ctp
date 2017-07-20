<?php 
// File: app/View/VectorTypes/index.ctp

$page_options = array(
//	$this->Html->link(__('Add Vector Group'), array('action' => 'add')),
);

// content
$th = array(
	'VectorType.name' => array('content' => __('Name'), 'options' => array('sort' => 'VectorType.name')),
	'VectorType.created' => array('content' => __('Created'), 'options' => array('sort' => 'VectorType.created')),
	'VectorType.holder' => array('content' => __('Default Holder'), 'options' => array('sort' => 'VectorType.holder')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vectorTypes as $i => $vectorType)
{
	$td[$i] = array(
		$this->Html->link($vectorType['VectorType']['name'], array('action' => 'view', $vectorType['VectorType']['id'])),
		$this->Wrap->niceTime($vectorType['VectorType']['created']),
		$this->Wrap->yesNo($vectorType['VectorType']['holder']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vectorType['VectorType']['id'])),
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
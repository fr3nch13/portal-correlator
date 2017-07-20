<?php

$inputs = [];
$inputs['id'] = [];
$inputs['categories'] = [
	'label' => __('Categories'),
	'type' => 'select',
	'searchable' => true,
	'multiple' => true,
	'options' => $categories,
];
echo $this->element('Utilities.page_form_basic', [
	'inputs' => $inputs,
	'page_title' => __('Add %s to the %s: %s', __('Categories'), __('View'), $combinedView['CombinedView']['name']),
]);
<?php

$inputs = [];
$inputs['id'] = [];
$inputs['reports'] = [
	'label' => __('Reports'),
	'type' => 'select',
	'searchable' => true,
	'multiple' => true,
	'options' => $reports,
];
echo $this->element('Utilities.page_form_basic', [
	'inputs' => $inputs,
	'page_title' => __('Add %s to the %s: %s', __('Reports'), __('View'), $combinedView['CombinedView']['name']),
]);
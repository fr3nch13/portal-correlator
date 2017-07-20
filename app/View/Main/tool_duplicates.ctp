<?php

$inputs = [];
$inputs['text'] = [
	'label' => __('Dump text here. Each line will be compared to the others.'),
	'type' => 'textarea',
	'after' => __('Total Before: %s - Total After: %s', $total_before, $total_after),
];
echo $this->element('Utilities.page_form_basic', [
	'page_title' => __('Filter duplicates'),
	'inputs' => $inputs,
]);
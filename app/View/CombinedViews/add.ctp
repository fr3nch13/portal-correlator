<?php

$inputs = [];
$inputs['name'] = [
	'label' => __('Name'),
];
$inputs['desc'] = [
	'label' => __('Description/Details'),
];

echo $this->element('Utilities.page_form_basic', [
	'inputs' => $inputs,
]);
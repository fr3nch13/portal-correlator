<?php

$inputs = [];
$inputs['id'] = [];
$inputs['name'] = [
	'div' => ['class' => 'twothird'],
];
$inputs['color_code_hex'] = [
	'div' => ['class' => 'third'],
	'label' => __('Assigned Color'),
	'type' => 'color',
];

echo $this->element('Utilities.page_form_basic', [
	'object_title' => __('NIH Risk'),
	'inputs' => $inputs,
]);
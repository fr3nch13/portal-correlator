<?php

$search_options = [
	'categories' => __('Categories'),
	'reports' => __('Reports'),
	'hostnames' => __('Hostnames'),
	'ipaddresses' => __('IP Addresses'),
	'fisma_systems' => __('FISMA Systems'),
	'fisma_inventories' => __('FISMA Inventory'),
];

echo $this->element('Utilities.page_global_search', [
	'page_title' => __('Search'),
	'search_options' => $search_options,
]);
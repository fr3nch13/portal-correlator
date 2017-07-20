<?php

$dashboard_blocks = [
	'vectors_overview' => ['controller' => 'vectors', 'action' => 'db_block_overview', 'plugin' => false],
	'vectors_db_block_vector_type' => ['controller' => 'vectors', 'action' => 'db_block_vector_type', 'plugin' => false],
	'vectors_db_block_type' => ['controller' => 'vectors', 'action' => 'db_block_type', 'plugin' => false],	
];

$tabs = [];

echo $this->element('Utilities.page_dashboard', [
	'page_title' => __('Dashboard: %s', __('Vectors')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
]);
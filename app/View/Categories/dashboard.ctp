<?php

$dashboard_blocks = [
	'categories_overview' => ['controller' => 'categories', 'action' => 'db_block_overview', 'plugin' => false],
	'categories_totals' => ['controller' => 'categories', 'action' => 'db_tab_totals', 'system', 2, 'plugin' => false],
	'categories_db_block_assessment_cust_risk' => ['controller' => 'categories', 'action' => 'db_block_assessment_cust_risk', 'plugin' => false],
	'categories_db_block_assessment_nih_risk' => ['controller' => 'categories', 'action' => 'db_block_assessment_nih_risk', 'plugin' => false],
	'categories_db_block_assessment_cust_risk_trend' => ['controller' => 'categories', 'action' => 'db_block_assessment_cust_risk_trend', 'plugin' => false],
	'categories_db_block_assessment_nih_risk_trend' => ['controller' => 'categories', 'action' => 'db_block_assessment_nih_risk_trend', 'plugin' => false],
	'categories_db_block_category_type' => ['controller' => 'categories', 'action' => 'db_block_category_type', 'plugin' => false],
	'categories_db_block_category_type_trend' => ['controller' => 'categories', 'action' => 'db_block_category_type_trend', 'plugin' => false],
];

$tabs = [];

/*
$tabs['fisma_systems'] = [
	'id' => 'fisma_systems',
	'name' => __('Fisma Systems'), 
	'ajax_url' => ['controller' => 'fisma_systems', 'action' => 'db_tab_index', 'plugin' => false],
];
*/

echo $this->element('Utilities.page_dashboard', [
	'page_title' => __('Dashboard: %s', __('Categories')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
]);
<?php

$dashboard_blocks = [
	'reports_overview' => ['controller' => 'reports', 'action' => 'db_block_overview', 'plugin' => false],
	'reports_totals' => ['controller' => 'reports', 'action' => 'db_tab_totals', 'system', 2, 'plugin' => false],
	'reports_db_block_assessment_cust_risk' => ['controller' => 'reports', 'action' => 'db_block_assessment_cust_risk', 'plugin' => false],
	'reports_db_block_assessment_nih_risk' => ['controller' => 'reports', 'action' => 'db_block_assessment_nih_risk', 'plugin' => false],
	'reports_db_block_assessment_cust_risk_trend' => ['controller' => 'reports', 'action' => 'db_block_assessment_cust_risk_trend', 'plugin' => false],
	'reports_db_block_report_type' => ['controller' => 'reports', 'action' => 'db_block_report_type', 'plugin' => false],
	'reports_db_block_assessment_nih_risk_trend' => ['controller' => 'reports', 'action' => 'db_block_assessment_nih_risk_trend', 'plugin' => false],
	'reports_db_block_report_type_trend' => ['controller' => 'reports', 'action' => 'db_block_report_type_trend', 'plugin' => false],
];

$tabs = [];

$tabs['totals'] = [
	'id' => 'totals',
	'name' => __('Totals'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'db_tab_totals', 'system', 'plugin' => false],
];

echo $this->element('Utilities.page_dashboard', [
	'page_title' => __('Dashboard: %s', __('Reports')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
]);
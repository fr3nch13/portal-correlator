<?php

$dashboard_blocks = array(
	'categories_overview' => array('controller' => 'categories', 'action' => 'db_block_overview', 'plugin' => false),
	'reports_overview' => array('controller' => 'reports', 'action' => 'db_block_overview', 'plugin' => false),
	'vectors_overview' => array('controller' => 'vectors', 'action' => 'db_block_overview', 'plugin' => false),
	'nslookups_overview' => array('controller' => 'nslookups', 'action' => 'db_block_overview', 'plugin' => false),
	'whois_overview' => array('controller' => 'whois', 'action' => 'db_block_overview', 'plugin' => false),
);

$tabs = array();

/*
$tabs[] = array(
	'key' => 'fismaSystems',
	'title' => __('Fisma Systems'), 
	'url' => array('controller' => 'fisma_systems', 'action' => 'db_tab_index', 'plugin' => false),
);
*/

echo $this->element('Utilities.page_dashboard', array(
	'page_title' => __('Dashboard: %s', __('Overview')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
));
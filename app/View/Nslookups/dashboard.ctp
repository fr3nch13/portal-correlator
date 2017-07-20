<?php

$dashboard_blocks = array(
	'nslookups_overview' => array('controller' => 'nslookups', 'action' => 'db_block_overview', 'plugin' => false),
	'nslookups_db_block_sources' => array('controller' => 'nslookups', 'action' => 'db_block_sources', 'plugin' => false),
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
	'page_title' => __('Dashboard: %s', __('DNS Records')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
));
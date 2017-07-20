<?php

$dashboard_blocks = array(
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
	'page_title' => __('Dashboard: %s', __('WHOIS Records')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
));
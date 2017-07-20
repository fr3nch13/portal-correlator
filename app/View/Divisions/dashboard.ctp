<?php

$dashboard_blocks = array(
	'divisions_overview' => array('controller' => 'divisions', 'action' => 'db_block_overview'),
);

echo $this->element('Utilities.page_dashboard', array(
	'page_title' => __('Dashboard: %s', __('Divisions')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
));
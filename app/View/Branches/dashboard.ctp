<?php

$dashboard_blocks = array(
	'db_block_overview' => array('controller' => 'branches', 'action' => 'db_block_overview'),
);

echo $this->element('Utilities.page_dashboard', array(
	'page_title' => __('Dashboard: %s', __('Branches')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
));
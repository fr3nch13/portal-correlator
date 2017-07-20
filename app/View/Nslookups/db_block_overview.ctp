<?php

$stats = (isset($stats)?$stats:array());
$stats = Hash::sort($stats, '{s}.value', 'desc');

$content = $this->element('Utilities.object_dashboard_stats', array(
	'title' => false,
	'details' => $stats,
));

echo $this->element('Utilities.object_dashboard_block', array(
	'title' => $this->Html->link(__('%s - Overview', __('DNS Records')), array('action' => 'dashboard')),
	'content' => $content,
));
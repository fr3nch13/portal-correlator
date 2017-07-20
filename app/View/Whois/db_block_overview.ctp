<?php

$stats = (isset($stats)?$stats:array());

$content = $this->element('Utilities.object_dashboard_stats', array(
	'title' => false,
	'details' => $stats,
));

echo $this->element('Utilities.object_dashboard_block', array(
	'title' => $this->Html->link(__('%s - Overview', __('WHOIS Records')), array('action' => 'dashboard')),
	'content' => $content,
));
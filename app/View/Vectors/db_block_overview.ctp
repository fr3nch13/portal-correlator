<?php

$stats = (isset($stats)?$stats:[]);

$content = $this->element('Utilities.object_dashboard_stats', [
	'title' => false,
	'details' => $stats,
]);

echo $this->element('Utilities.object_dashboard_block', [
	'title' => $this->Html->link(__('%s - Overview', __('Vectors')), ['action' => 'dashboard']),
	'content' => $content,
]);
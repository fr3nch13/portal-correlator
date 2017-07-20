<?php 

$page_options = [];
$page_options['add_categories'] = $this->Html->link(__('Add %s', __('Categories')), ['action' => 'add_categories', $combinedView['CombinedView']['id']]);
$page_options['add_reports'] = $this->Html->link(__('Add %s', __('Reports')), ['action' => 'add_reports', $combinedView['CombinedView']['id']]);
$page_options['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $combinedView['CombinedView']['id']]);
$page_options['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $combinedView['CombinedView']['id']], ['confirm' => __('Are you sure?')]);

$details = [];
$details[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($combinedView['CombinedView']['created'])];
$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($combinedView['CombinedView']['modified'])];

$stats = [];
$tabs = [];

$tabs['vectors'] = $stats['vectors'] = [
	'id' => 'vectors',
	'name' => __('Vectors'), 
	'ajax_url' => ['controller' => 'vectors', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
];
$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'), 
	'ajax_url' => ['controller' => 'categories', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
];
$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
];
$tabs['desc'] = [
	'id' => 'desc',
	'name' => __('Description'), 
	'content' => $combinedView['CombinedView']['desc'],
];

echo $this->element('Utilities.page_view', [
	'page_title' => __('%s: %s', __('Combined View'), $combinedView['CombinedView']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
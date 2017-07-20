<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('View') ), ['action' => 'add']);
}

// content
$th = [
	'CombinedView.name' => ['content' => __('Name'), 'options' => ['sort' => 'CombinedView.name']],
	'Vector.count' => ['content' => __('# %s', __('Vectors'))],
	'Category.count' => ['content' => __('# %s', __('Categories'))],
	'Report.count' => ['content' => __('# %s', __('Reports'))],
	'actions' => ['content' => __('Actions'), 'options' => ['class' => 'actions']],
];

$td = [];
foreach ($combinedViews as $i => $combinedView)
{
	$actions = [];
	$actions['view'] = $this->Html->link(__('View'), ['action' => 'view', $combinedView['CombinedView']['id'] ]);
	$actions['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $combinedView['CombinedView']['id']]);
	$actions['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $combinedView['CombinedView']['id']], ['confirm' => __('Are you sure?')]);
	
	$td[$i] = [];
	$td[$i]['CombinedView.name'] = $this->Html->link($combinedView['CombinedView']['name'], ['action' => 'view', $combinedView['CombinedView']['id'] ]);
	
	$td[$i]['Vector.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'vectors', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
		'url' => ['action' => 'view', $combinedView['CombinedView']['id'], 'tab' => 'vectors'],
	]];
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
		'url' => ['action' => 'view', $combinedView['CombinedView']['id'], 'tab' => 'categories'],
	]];
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'combined_view', $combinedView['CombinedView']['id']],
		'url' => ['action' => 'view', $combinedView['CombinedView']['id'], 'tab' => 'reports'],
	]];
	$td[$i]['actions'] = [
		implode('', $actions), 
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('Combined Views'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
]);
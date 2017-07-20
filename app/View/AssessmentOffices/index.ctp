<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Office') ), ['action' => 'add', 'admin' => true]);
}

// content
$th = [
	'AssessmentOffice.name' => ['content' => __('Name'), 'options' => ['sort' => 'AssessmentOffice.name']],
	'AssessmentOffice.color_code_hex' => ['content' => __('Color'), 'options' => ['sort' => 'AssessmentOffice.color_code_hex']],
	'Category.count' => ['content' => __('# %s', __('Categories'))],
	'Report.count' => ['content' => __('# %s', __('Reports'))],
	'actions' => ['content' => __('Actions'), 'options' => ['class' => 'actions']],
];

$td = [];
foreach ($assessmentOffices as $i => $assessmentOffice)
{
	$actions = [];
	$actions['view'] = $this->Html->link(__('View'), ['action' => 'view', $assessmentOffice['AssessmentOffice']['id'] ]);
	if($this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentOffice['AssessmentOffice']['id'], 'admin' => true]);
		$actions['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentOffice['AssessmentOffice']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
	}
	
	$td[$i] = [];
	$td[$i]['AssessmentOffice.name'] = $this->Html->link($assessmentOffice['AssessmentOffice']['name'], ['action' => 'view', $assessmentOffice['AssessmentOffice']['id'] ]);
	$td[$i]['AssessmentOffice.color_code_hex'] = $this->Common->coloredCell($assessmentOffice['AssessmentOffice'], ['displayField' => 'color_code_hex', 'colorShow' => true]);
	
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'assessment_office', $assessmentOffice['AssessmentOffice']['id']],
		'url' => ['action' => 'view', $assessmentOffice['AssessmentOffice']['id'], '#' => 'ui-tabs-1'),
	));
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'assessment_office', $assessmentOffice['AssessmentOffice']['id']],
		'url' => ['action' => 'view', $assessmentOffice['AssessmentOffice']['id'], '#' => 'ui-tabs-2'),
	));
	$td[$i]['actions'] = [
		implode('', $actions), 
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('%s %s', __('Assessment'), __('Offices')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
]);
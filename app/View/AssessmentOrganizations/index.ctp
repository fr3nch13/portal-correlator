<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Organization') ), ['action' => 'add', 'admin' => true]);
}

// content
$th = [
	'AssessmentOrganization.name' => ['content' => __('Name'), 'options' => ['sort' => 'AssessmentOrganization.name']],
	'AssessmentOrganization.color_code_hex' => ['content' => __('Color'), 'options' => ['sort' => 'AssessmentOrganization.color_code_hex']],
	'Category.count' => ['content' => __('# %s', __('Categories'))],
	'Report.count' => ['content' => __('# %s', __('Reports'))],
	'actions' => ['content' => __('Actions'), 'options' => ['class' => 'actions']],
];

$td = [];
foreach ($assessmentOrganizations as $i => $assessmentOrganization)
{
	$actions = [];
	$actions['view'] = $this->Html->link(__('View'), ['action' => 'view', $assessmentOrganization['AssessmentOrganization']['id'] ]);
	if($this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentOrganization['AssessmentOrganization']['id'], 'admin' => true]);
		$actions['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentOrganization['AssessmentOrganization']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
	}
	
	$td[$i] = [];
	$td[$i]['AssessmentOrganization.name'] = $this->Html->link($assessmentOrganization['AssessmentOrganization']['name'], ['action' => 'view', $assessmentOrganization['AssessmentOrganization']['id'] ]);
	$td[$i]['AssessmentOrganization.color_code_hex'] = $this->Common->coloredCell($assessmentOrganization['AssessmentOrganization'], ['displayField' => 'color_code_hex', 'colorShow' => true]);
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'assessment_organization', $assessmentOrganization['AssessmentOrganization']['id']],
		'url' => ['action' => 'view', $assessmentOrganization['AssessmentOrganization']['id'], 'tab' => 'categories'],
	]];
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'assessment_organization', $assessmentOrganization['AssessmentOrganization']['id']],
		'url' => ['action' => 'view', $assessmentOrganization['AssessmentOrganization']['id'], 'tab' => 'reports'],
	]];
	$td[$i]['actions'] = [
		implode('', $actions), 
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('%s %s', __('Assessment'), __('Organizations')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
]);
<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('NIH Risk') ), ['action' => 'add', 'admin' => true]);
}

// content
$th = [
	'AssessmentNihRisk.name' => ['content' => __('Name'), 'options' => ['sort' => 'AssessmentNihRisk.name']],
	'AssessmentNihRisk.color_code_hex' => ['content' => __('Color'), 'options' => ['sort' => 'AssessmentNihRisk.color_code_hex']],
	'Category.count' => ['content' => __('# %s', __('Categories'))],
	'Report.count' => ['content' => __('# %s', __('Reports'))],
	'actions' => ['content' => __('Actions'), 'options' => ['class' => 'actions']],
];

$td = [];
foreach ($assessmentNihRisks as $i => $assessmentNihRisk)
{
	$actions = [];
	$actions['view'] = $this->Html->link(__('View'), ['action' => 'view', $assessmentNihRisk['AssessmentNihRisk']['id'] ]);
	if($this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentNihRisk['AssessmentNihRisk']['id'], 'admin' => true]);
		$actions['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentNihRisk['AssessmentNihRisk']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
	}
	
	$td[$i] = [];
	$td[$i]['AssessmentNihRisk.name'] = $this->Html->link($assessmentNihRisk['AssessmentNihRisk']['name'], ['action' => 'view', $assessmentNihRisk['AssessmentNihRisk']['id'] ]);
	$td[$i]['AssessmentNihRisk.color_code_hex'] = $this->Common->coloredCell($assessmentNihRisk['AssessmentNihRisk'], ['displayField' => 'color_code_hex', 'colorShow' => true]);
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'assessment_nih_risk', $assessmentNihRisk['AssessmentNihRisk']['id']],
		'url' => ['action' => 'view', $assessmentNihRisk['AssessmentNihRisk']['id'], 'tab' => 'categories'],
	]];
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'assessment_nih_risk', $assessmentNihRisk['AssessmentNihRisk']['id']],
		'url' => ['action' => 'view', $assessmentNihRisk['AssessmentNihRisk']['id'], 'tab' => 'reports'],
	]];
	$td[$i]['actions'] = [
		implode('', $actions), 
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('%s %s', __('Assessment'), __('NIH Risks')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
]);
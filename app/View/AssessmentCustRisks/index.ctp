<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Customer Risk') ), ['action' => 'add', 'admin' => true]);
}

// content
$th = [
	'AssessmentCustRisk.name' => ['content' => __('Name'), 'options' => ['sort' => 'AssessmentCustRisk.name']],
	'AssessmentCustRisk.color_code_hex' => ['content' => __('Color'), 'options' => ['sort' => 'AssessmentCustRisk.color_code_hex']],
	'Category.count' => ['content' => __('# %s', __('Categories'))],
	'Report.count' => ['content' => __('# %s', __('Reports'))],
	'actions' => ['content' => __('Actions'), 'options' => ['class' => 'actions']],
];

$td = [];
foreach ($assessmentCustRisks as $i => $assessmentCustRisk)
{
	$actions = [];
	$actions['view'] = $this->Html->link(__('View'), ['action' => 'view', $assessmentCustRisk['AssessmentCustRisk']['id'] ]);
	if($this->Common->roleCheck('admin'))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentCustRisk['AssessmentCustRisk']['id'], 'admin' => true]);
		$actions['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentCustRisk['AssessmentCustRisk']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
	}
	
	$td[$i] = [];
	$td[$i]['AssessmentCustRisk.name'] = $this->Html->link($assessmentCustRisk['AssessmentCustRisk']['name'], ['action' => 'view', $assessmentCustRisk['AssessmentCustRisk']['id'] ]);
	$td[$i]['AssessmentCustRisk.color_code_hex'] = $this->Common->coloredCell($assessmentCustRisk['AssessmentCustRisk'], ['displayField' => 'color_code_hex', 'colorShow' => true]);
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'assessment_cust_risk', $assessmentCustRisk['AssessmentCustRisk']['id']],
		'url' => ['action' => 'view', $assessmentCustRisk['AssessmentCustRisk']['id'], 'tab' => 'categories'],
	]];
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'assessment_cust_risk', $assessmentCustRisk['AssessmentCustRisk']['id']],
		'url' => ['action' => 'view', $assessmentCustRisk['AssessmentCustRisk']['id'], 'tab' => 'reports'],
	]];
	$td[$i]['actions'] = [
		implode('', $actions), 
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('%s %s', __('Assessment'), __('Customer Risks')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
]);
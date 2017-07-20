<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentCustRisk['AssessmentCustRisk']['id'], 'admin' => true]);
	$page_options['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentCustRisk['AssessmentCustRisk']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
}

$details = [];
$details[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($assessmentCustRisk['AssessmentCustRisk']['created'])];
$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($assessmentCustRisk['AssessmentCustRisk']['modified'])];

$stats = [];
$tabs = [];

$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'), 
	'ajax_url' => ['controller' => 'categories', 'action' => 'assessment_cust_risk', $assessmentCustRisk['AssessmentCustRisk']['id']],
];
$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'assessment_cust_risk', $assessmentCustRisk['AssessmentCustRisk']['id']],
];

echo $this->element('Utilities.page_view', [
	'page_title' => __('%s: %s', __('Customer Risk'), $assessmentCustRisk['AssessmentCustRisk']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
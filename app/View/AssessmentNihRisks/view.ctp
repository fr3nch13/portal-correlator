<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentNihRisk['AssessmentNihRisk']['id'], 'admin' => true]);
	$page_options['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentNihRisk['AssessmentNihRisk']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
}

$details = [];
$details[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($assessmentNihRisk['AssessmentNihRisk']['created'])];
$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($assessmentNihRisk['AssessmentNihRisk']['modified'])];

$stats = [];
$tabs = [];

$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'), 
	'ajax_url' => ['controller' => 'categories', 'action' => 'assessment_nih_risk', $assessmentNihRisk['AssessmentNihRisk']['id']],
];
$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'assessment_nih_risk', $assessmentNihRisk['AssessmentNihRisk']['id']],
];

echo $this->element('Utilities.page_view', [
	'page_title' => __('%s: %s', __('NIH Risk'), $assessmentNihRisk['AssessmentNihRisk']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
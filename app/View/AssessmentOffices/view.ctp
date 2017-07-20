<?php 

$page_options = [];
if($this->Common->roleCheck('admin'))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), ['action' => 'edit', $assessmentOffice['AssessmentOffice']['id'], 'admin' => true]);
	$page_options['delete'] = $this->Html->link(__('Delete'), ['action' => 'delete', $assessmentOffice['AssessmentOffice']['id'], 'admin' => true], ['confirm' => __('Are you sure?')]);
}

$details = [];
$details[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($assessmentOffice['AssessmentOffice']['created'])];
$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($assessmentOffice['AssessmentOffice']['modified'])];

$stats = [];
$tabs = [];

$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'), 
	'ajax_url' => ['controller' => 'categories', 'action' => 'assessment_office', $assessmentOffice['AssessmentOffice']['id']],
];
$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'assessment_office', $assessmentOffice['AssessmentOffice']['id']],
];

echo $this->element('Utilities.page_view', [
	'page_title' => __('%s: %s', __('Office'), $assessmentOffice['AssessmentOffice']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
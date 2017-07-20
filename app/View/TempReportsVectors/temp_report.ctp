<?php 
// File: app/View/TempReportsVectors/temp_report.ctp


$page_options = array();
$page_options[] = $this->Html->link(__('Add Vectors'), array('action' => 'add', $this->params['pass'][0]));
$page_options[] = $this->Html->link(__('Assign All Vectors to One Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));

// content
$th = array(
	'TempReportsVector.id' => array('content' => __('Xref ID'), 'options' => array('sort' => 'TempReportsVector.id')),
	'TempVector.id' => array('content' => __('Vector ID'), 'options' => array('sort' => 'TempVector.id')),
	'TempVector.temp_vector' => array('content' => __('Vector'), 'options' => array('sort' => 'TempVector.temp_vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'TempVector.type')),
//	'TempReportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'TempReportsVector.active')),
//	'TempVector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'TempVector.modified')),
	'TempReportsVector.created' => array('content' => __('Added'), 'options' => array('sort' => 'TempReportsVector.created')),
	'TempVector.created' => array('content' => __('Created'), 'options' => array('sort' => 'TempVector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($temp_reports_vectors as $i => $temp_reports_vector)
{
//	$active = $this->Wrap->yesNo($temp_reports_vector['TempReportsVector']['active']);
	
	$actions = '';
//	$actions = $this->Html->link(__('View'), array('controller' => 'temp_vectors', 'action' => 'view', $temp_reports_vector['TempVector']['id']));
	
	if($temp_reports_vector['TempReport']['user_id'] == AuthComponent::user('id'))
	{
 
		$actions .= $this->Html->link(__('Remove'), array('action' => 'delete', $temp_reports_vector['TempReportsVector']['id']),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$temp_reports_vector['TempReportsVector']['id'],
		$temp_reports_vector['TempVector']['id'],
		$temp_reports_vector['TempVector']['temp_vector'],
		$this->Html->link($temp_reports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $temp_reports_vector['VectorType']['id'])),
		$this->Wrap->niceWord($temp_reports_vector['TempVector']['type']),
		$this->Wrap->niceTime($temp_reports_vector['TempReportsVector']['created']),
		$this->Wrap->niceTime($temp_reports_vector['TempVector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $temp_reports_vector['TempReportsVector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Vectors'),
	'page_options' => $page_options,
	'search_placeholder' => __('Vectors'),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign Group'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'temp_reports',
		'action' => 'view',
		$this->params['pass'][0],
	),
	));
?>
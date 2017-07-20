<?php 
// File: app/View/TempReportsVectors/multiselect_vector_multitypes.ctp

// content
$th = array(
	'TempVector.temp_vector' => array('content' => __('Vector'), 'options' => array('sort' => 'TempVector.temp_vector')),
	'vector_group' => array('content' => __('Select Vector Group'), 'options' => array('class' => 'actions')),
	'VectorType.name' => array('content' => __('Current Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'TempVector.type')),
);

$td = array();
foreach ($temp_reports_vectors as $i => $temp_reports_vector)
{
	$actions = $this->Form->input('TempReportsVector.'.$i.'.id', array('type' => 'hidden', 'value' => $temp_reports_vector['TempReportsVector']['id']));
	$actions .= $this->Form->input('TempReportsVector.'.$i.'.vector_type_id', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $vectorTypes,
	        					'selected' => $temp_reports_vector['TempReportsVector']['vector_type_id'],
	        				));
	
	$td[$i] = array(
		$temp_reports_vector['TempVector']['temp_vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$temp_reports_vector['VectorType']['name'],
		$this->Wrap->niceWord($temp_reports_vector['TempVector']['type']),
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('TempReportsVector', array('url' => array('action' => 'multiselect_vector_multitypes')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Group for Temp Report Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
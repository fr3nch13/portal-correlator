<?php 
// File: app/View/UploadsVectors/multiselect_vector_multitypes.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'vector_group' => array('content' => __('Select Vector Group'), 'options' => array('class' => 'actions')),
	'VectorType.name' => array('content' => __('Current Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
);

$td = array();
foreach ($uploads_vectors as $i => $uploads_vector)
{
	$actions = $this->Form->input('UploadsVector.'.$i.'.id', array('type' => 'hidden', 'value' => $uploads_vector['UploadsVector']['id']));
	$actions .= $this->Form->input('UploadsVector.'.$i.'.vector_type_id', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $vectorTypes,
	        					'selected' => $uploads_vector['UploadsVector']['vector_type_id'],
	        				));
	
	$td[$i] = array(
		$uploads_vector['Vector']['vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$uploads_vector['VectorType']['name'],
		$this->Wrap->niceWord($uploads_vector['Vector']['type']),
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('UploadsVector', array('url' => array('action' => 'multiselect_vector_multitypes')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Group for File Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
<?php 
// File: app/View/TempCategoriesVectors/multiselect_vector_multitypes.ctp

// content
$th = array(
	'TempVector.temp_vector' => array('content' => __('Vector'), 'options' => array('sort' => 'TempVector.temp_vector')),
	'vector_group' => array('content' => __('Select Vector Group'), 'options' => array('class' => 'actions')),
	'VectorType.name' => array('content' => __('Current Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'TempVector.type')),
);

$td = array();
foreach ($temp_categories_vectors as $i => $temp_categories_vector)
{
	$actions = $this->Form->input('TempCategoriesVector.'.$i.'.id', array('type' => 'hidden', 'value' => $temp_categories_vector['TempCategoriesVector']['id']));
	$actions .= $this->Form->input('TempCategoriesVector.'.$i.'.vector_type_id', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $vectorTypes,
	        					'selected' => $temp_categories_vector['TempCategoriesVector']['vector_type_id'],
	        				));
	
	$td[$i] = array(
		$temp_categories_vector['TempVector']['temp_vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$temp_categories_vector['VectorType']['name'],
		$this->Wrap->niceWord($temp_categories_vector['TempVector']['type']),
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('TempCategoriesVector', array('url' => array('action' => 'multiselect_vector_multitypes')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Group for Temp Category Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
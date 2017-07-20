<?php 
// File: app/View/TempCategoriesVectors/assign_vector_multitypes.ctp

// content
$th = array(
	'TempVector.vector' => array('content' => __('TempVector'), 'options' => array('sort' => 'TempVector.vector')),
	'vector_group' => array('content' => __('Select TempVector Group'), 'options' => array('class' => 'actions')),
	'VectorType.name' => array('content' => __('Current TempVector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('TempVector Type'), 'options' => array('sort' => 'TempVector.type')),
);

$td = array();
foreach ($temp_categories_vectors as $i => $temp_categories_vector)
{
	$actions = $this->Form->input($i.'.TempCategoriesVector.id', array('type' => 'hidden', 'value' => $temp_categories_vector['TempCategoriesVector']['id']));
	$actions .= $this->Form->input($i.'.TempCategoriesVector.vector_type_id', array(
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

$before_table = $this->element('Utilities.search', array(
	'placeholder' => $this->params->controller,
	'search_id' => 'externalSearch',
	'method' => 'get',
));
$after_table = false;

if($td)
{
	$before_table .= $this->Form->create('TempCategoriesVector');
	$after_table .= $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Groups for Temp Category Vectors'),
	'use_search' => false,
	'use_pagination' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
));
?>
<?php 
// File: app/View/CategoriesVectors/admin_assign_vector_multitypes.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'vector_group' => array('content' => __('Select Vector Group'), 'options' => array('class' => 'actions')),
	'VectorType.name' => array('content' => __('Current Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
);

$td = array();
foreach ($categories_vectors as $i => $categories_vector)
{
	$actions = $this->Form->input($i.'.CategoriesVector.id', array('type' => 'hidden', 'value' => $categories_vector['CategoriesVector']['id']));
	$actions .= $this->Form->input($i.'.CategoriesVector.vector_type_id', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $vectorTypes,
	        					'selected' => $categories_vector['CategoriesVector']['vector_type_id'],
	        				));
	
	$td[$i] = array(
		$categories_vector['Vector']['vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$categories_vector['VectorType']['name'],
		$this->Wrap->niceWord($categories_vector['Vector']['type']),
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
	$before_table .= $this->Form->create('CategoriesVector');
	$after_table .= $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Groups for Category Vectors'),
	'use_search' => false,
	'use_pagination' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
));
?>
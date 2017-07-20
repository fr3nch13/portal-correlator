<?php 
// File: app/View/CategoriesVectors/admin_multiselect_vector_multitypes.ctp

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
	$actions = $this->Form->input('CategoriesVector.'.$i.'.id', array('type' => 'hidden', 'value' => $categories_vector['CategoriesVector']['id']));
	$actions .= $this->Form->input('CategoriesVector.'.$i.'.vector_type_id', array(
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

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('CategoriesVector', array('url' => array('action' => 'multiselect_vector_multitypes')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Group for Category Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
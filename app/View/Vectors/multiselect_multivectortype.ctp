<?php 
// File: app/View/Vectors/multiselect_multivectortypes.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Current Type'), 'options' => array('sort' => 'Vector.type')),
	'Vector.discovered_type' => array('content' => __('Discovered Type'), 'options' => array('class' => 'actions')),
	'vector_type' => array('content' => __('Select Type'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$actions = $this->Form->input('Vector.'.$i.'.id', array('type' => 'hidden', 'value' => $vector['Vector']['id']));
	$actions .= $this->Form->input('Vector.'.$i.'.current_type', array('type' => 'hidden', 'value' => $vector['Vector']['type']));
	$actions .= $this->Form->input('Vector.'.$i.'.type', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $types,
	        					'selected' => $vector['Vector']['discovered_type'],
	        				));
	$current_type = $vector['Vector']['type'];
	$current_type_nice = Inflector::humanize($current_type);
	$discovered_type = $vector['Vector']['discovered_type'];
	$discovered_type_nice = Inflector::humanize($discovered_type);
	
	if($current_type !== $discovered_type)
	{
		$current_type = array($current_type_nice, array('class' => 'highlight'));
		$discovered_type = array($discovered_type_nice, array('class' => 'highlight'));
	}
	else
	{
		$current_type = $current_type_nice;
		$discovered_type = $discovered_type_nice;
	}
	
	$td[$i] = array(
		$vector['Vector']['vector'],
		$current_type,
		$discovered_type,
		array(
			$actions,
			array('class' => 'actions'),
		),
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('Vector', array('url' => array('action' => 'multiselect_multivectortype')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Detected Type for Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
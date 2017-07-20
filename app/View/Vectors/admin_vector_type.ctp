<?php 
// File: app/View/Vectors/admin_vector_type.ctp


// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'Vector.bad' => array('content' => __('%s State', __('Benign')), 'options' => array('sort' => 'Vector.bad')),
	//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'Vector.created' => array('content' => __('Vector Added'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector['Vector']['id']));
	
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $vector['Vector']['id'])),
		$this->Wrap->niceWord($vector['Vector']['type']),
		$this->Wrap->yesNo($vector['Vector']['bad']),
//		$this->Wrap->niceTime($vector['Vector']['modified']),
		$this->Wrap->niceTime($vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Vectors'),
	'th' => $th,
	'td' => $td,
));
?>
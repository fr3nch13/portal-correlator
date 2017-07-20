<?php 
// File: app/View/Vectors/admin_tag.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Type'), 'options' => array('sort' => 'Vector.type')),
//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id'])),
		$this->Wrap->niceWord($vector['Vector']['type']),
//		$this->Wrap->niceTime($vector['Vector']['modified']),
		$this->Wrap->niceTime($vector['Vector']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Vectors'),
	'th' => $th,
	'td' => $td,
	));
?>
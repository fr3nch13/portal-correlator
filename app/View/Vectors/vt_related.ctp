<?php 
// File: app/View/Vectors/index.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'VectorSourceFirst.source_type' => array('content' => __('First Vector Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type')),
	'VectorSourceFirst.created' => array('content' => __('First Vector Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created')),
	'VectorSourceLast.source_type' => array('content' => __('Last Vector Source'), 'options' => array('sort' => 'VectorSourceLast.source_type')),
	'VectorSourceLast.created' => array('content' => __('Last Vector Source Added'), 'options' => array('sort' => 'VectorSourceLast.created')),
//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$actions = array();
	$vector_link = $this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id']));
	
	if(in_array($vector['Vector']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View'), array('action' => 'vtview', $vector['Vector']['id']));
		$vector_link = $this->Html->link($vector['Vector']['vector'], array('action' => 'vtview', $vector['Vector']['id']));
	}
	$actions[] = $this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id']));
	
	$td[$i] = array(
		$vector_link,
		$this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type'])),
		$this->Html->link($vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $vector['VectorType']['id'])),
		$this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceFirst']['created']),
		$this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceLast']['created']),
//		$this->Wrap->niceTime($vector['Vector']['modified']),
		$this->Wrap->niceTime($vector['Vector']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s', __('Vectors')),
	'th' => $th,
	'td' => $td,
));
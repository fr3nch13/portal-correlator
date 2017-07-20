<?php 
// File: app/View/DumpsVectors/dump.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'DumpsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'DumpsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($dumps_vectors as $i => $dumps_vector)
{
	$active = $this->Wrap->yesNo($dumps_vector['DumpsVector']['active']);
	
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $dumps_vector['Vector']['id']));
	
	if($dumps_vector['Dump']['user_id'] == AuthComponent::user('id'))
	{
		$active = array(
			$this->Form->postLink($active,array('action' => 'toggle', 'active', $dumps_vector['DumpsVector']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
	}
	
	$td[$i] = array(
		$this->Html->link($dumps_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $dumps_vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($dumps_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $dumps_vector['Vector']['type'])),
		$active,
		$dumps_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($dumps_vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Dump Vectors'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
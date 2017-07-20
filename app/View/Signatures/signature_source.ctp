<?php 
// File: app/View/Signatures/signature_source.ctp


$page_options = array();
// $page_options[] = $this->Html->link(__('Add %s', __('Signature')), array('action' => 'add'));

// content
$th = array(
	'Signature.name' => array('content' => __('Signature'), 'options' => array('sort' => 'Signature.name')),
	'Signature.signature_type' => array('content' => __('Type'), 'options' => array('sort' => 'Signature.signature_type')),
	'Signature.active' => array('content' => __('Active'), 'options' => array('sort' => 'Signature.active')),
	'Signature.created' => array('content' => __('Created'), 'options' => array('sort' => 'Signature.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($signatures as $i => $signature)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'signatures', 'action' => 'view', $signature['Signature']['id']));
	
	$active = $this->Wrap->yesNo($signature['Signature']['active']);
	
	if(AuthComponent::user('role') == 'admin')
	{
		$active = array(
			$this->Html->link($active, array('action' => 'toggle', 'active', $signature['Signature']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		
		$actions .= $this->Html->link(__('Remove'),array('action' => 'delete', $signature['Signature']['id'], 'admin' => true),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($signature['Signature']['name'], array('controller' => 'signatures', 'action' => 'view', $signature['Signature']['id'])),
		//$this->Html->link($this->Wrap->getSigTypeMap($signature['Signature']['signature_type']), array('controller' => 'signatures', 'action' => 'type', $signature['Signature']['signature_type'])),
		$this->Wrap->getSigTypeMap($signature['Signature']['signature_type']),
		$active,
		$this->Wrap->niceTime($signature['Signature']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $signature['Signature']['id'],
	);
}

$use_multiselect = true;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s with %s of: %s', __('Signatures'), __('Source'), $signature_source['SignatureSource']['name']),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'inactive' => __('Mark Inactive'),
		'active' => __('Mark Active'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'action' => 'index',
	),
));
?>
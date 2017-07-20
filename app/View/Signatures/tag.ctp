<?php 
// File: app/View/Signatures/tag.ctp


$page_options = array();

// content
$th = array(
	'Signature.name' => array('content' => __('Signature'), 'options' => array('sort' => 'Signature.name')),
	'Signature.signature_type' => array('content' => __('Type'), 'options' => array('sort' => 'Signature.signature_type')),
	'SignatureSource.name' => array('content' => __('Source'), 'options' => array('sort' => 'SignatureSource.name')),
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
		
		$actions .= $this->Html->link(__('Edit'),array('action' => 'edit', $signature['Signature']['id'], 'admin' => true));
		$actions .= $this->Html->link(__('Delete'),array('action' => 'delete', $signature['Signature']['id'], 'admin' => true),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($signature['Signature']['name'], array('controller' => 'signatures', 'action' => 'view', $signature['Signature']['id'])),
		//$this->Html->link($this->Wrap->getSigTypeMap($signature['Signature']['signature_type']), array('controller' => 'signatures', 'action' => 'type', $signature['Signature']['signature_type'])),
		$this->Wrap->getSigTypeMap($signature['Signature']['signature_type']),
		$this->Html->link($signature['SignatureSource']['name'], array('controller' => 'signature_sources', 'action' => 'view', $signature['SignatureSource']['id'])),
		$active,
		$this->Wrap->niceTime($signature['Signature']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $signature['Signature']['id'],
	);
}

$use_multiselect = false;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s', __('Signatures')),
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
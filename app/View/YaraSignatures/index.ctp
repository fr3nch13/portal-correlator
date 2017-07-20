<?php 
// File: app/View/YaraSignatures/index.ctp


$page_options = array();
$page_options[] = $this->Html->link(__('Add %s', __('Signatures')), array('controller' => 'signatures', 'action' => 'add'));

// content
$th = array(
	'Signature.name' => array('content' => __('Signature'), 'options' => array('sort' => 'Signature.name')),
	'SignatureSource.name' => array('content' => __('Source'), 'options' => array('sort' => 'SignatureSource.name')),
	'Signature.active' => array('content' => __('Active'), 'options' => array('sort' => 'Signature.active')),
	'Signature.created' => array('content' => __('Created'), 'options' => array('sort' => 'Signature.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();

foreach ($yara_signatures as $i => $yara_signature)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'signatures', 'action' => 'view', $yara_signature['Signature']['id']));
	
	$active = $this->Wrap->yesNo($yara_signature['Signature']['active']);
	
	if(AuthComponent::user('role') == 'admin')
	{
		$active = array(
			$this->Html->link($active, array('controller' => 'signatures', 'action' => 'toggle', 'active', $yara_signature['Signature']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		
		$actions .= $this->Html->link(__('Edit'),array('controller' => 'signatures', 'action' => 'edit', $yara_signature['Signature']['id'], 'admin' => true));
		$actions .= $this->Html->link(__('Delete'),array('controller' => 'signatures', 'action' => 'delete', $yara_signature['Signature']['id'], 'admin' => true),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($yara_signature['Signature']['name'], array('controller' => 'signatures', 'action' => 'view', $yara_signature['Signature']['id'])),
		$this->Html->link($yara_signature['SignatureSource']['name'], array('controller' => 'signature_sources', 'action' => 'view', $yara_signature['SignatureSource']['id'])),
		$active,
		$this->Wrap->niceTime($yara_signature['Signature']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $yara_signature['Signature']['id'],
	);
}

$use_multiselect = false;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s %s', __('Yara'), __('Signatures')),
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
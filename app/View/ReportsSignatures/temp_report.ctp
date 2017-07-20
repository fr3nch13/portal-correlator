<?php 
// File: app/View/ReportsSignatures/temp_report.ctp


$page_options = array();
if($temp_report['TempReport']['user_id'] == AuthComponent::user('id'))
{
		$page_options[] = $this->Html->link(__('Add %s', __('Signatures')), array('controller' => 'signatures', 'action' => 'add', 'temp_report_id' => $this->params['pass'][0]));
}

// content
$th = array(
	'Signature.name' => array('content' => __('Signature'), 'options' => array('sort' => 'Signature.name')),
	'Signature.signature_type' => array('content' => __('Type'), 'options' => array('sort' => 'Signature.signature_type')),
	'SignatureSource.name' => array('content' => __('Source'), 'options' => array('sort' => 'SignatureSource.name')),
	'ReportsSignature.active' => array('content' => __('Active'), 'options' => array('sort' => 'ReportsSignature.active')),
	'ReportsSignature.created' => array('content' => __('Added to %s', __('Report')), 'options' => array('sort' => 'ReportsSignature.created')),
	'Signature.created' => array('content' => __('Created'), 'options' => array('sort' => 'Signature.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($reports_signatures as $i => $reports_signature)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'signatures', 'action' => 'view', $reports_signature['Signature']['id']));
	
	$active = $this->Wrap->yesNo($reports_signature['ReportsSignature']['active']);
	
	if($reports_signature['TempReport']['user_id'] == AuthComponent::user('id'))
	{
		$active = array(
			$this->Html->link($active, array('action' => 'toggle', 'active', $reports_signature['ReportsSignature']['id'], 'hash' => 'ui-tabs-3'),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		if($reports_signature['TempReport']['user_id'] == AuthComponent::user('id'))
			$actions .= $this->Html->link(__('Remove'),array('action' => 'delete', $reports_signature['ReportsSignature']['id'], 'hash' => 'ui-tabs-3'),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($reports_signature['Signature']['name'], array('controller' => 'signatures', 'action' => 'view', $reports_signature['Signature']['id'])),
		//$this->Html->link($this->Wrap->getSigTypeMap($signature['Signature']['signature_type']), array('controller' => 'signatures', 'action' => 'type', $signature['Signature']['signature_type'])),
		$this->Wrap->getSigTypeMap($reports_signature['Signature']['signature_type']),
		$this->Html->link($reports_signature['SignatureSource']['name'], array('controller' => 'signature_sources', 'action' => 'view', $reports_signature['SignatureSource']['id'])),
		$active,
		$this->Wrap->niceTime($reports_signature['ReportsSignature']['created']),
		$this->Wrap->niceTime($reports_signature['Signature']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $reports_signature['ReportsSignature']['id'],
	);
}

$use_multiselect = false;
if($temp_report['TempReport']['user_id'] == AuthComponent::user('id'))
{
	$use_multiselect = false;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('Report'), __('Signatures')),
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
		'controller' => 'temp_reports',
		'action' => 'view',
		$this->params['pass'][0],
		'hash' => 'ui-tabs-3',
	),
));
?>
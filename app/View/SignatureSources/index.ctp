<?php 
// File: app/View/SignatureSources/index.ctp


$page_options = array();
//$page_options[] = $this->Html->link(__('Add %s', __('Signature Source')), array('action' => 'add'));

// content
$th = array(
	'SignatureSource.name' => array('content' => __('Name'), 'options' => array('sort' => 'SignatureSource.name')),
	'SignatureSource.created' => array('content' => __('Created'), 'options' => array('sort' => 'SignatureSource.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($signature_sources as $i => $signature_source)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'signature_sources', 'action' => 'view', $signature_source['SignatureSource']['id']));
	
	if(AuthComponent::user('role') == 'admin')
	{
		$actions .= $this->Html->link(__('Edit'),array('action' => 'edit', $signature_source['SignatureSource']['id'], 'admin' => true));
		$actions .= $this->Html->link(__('Delete'),array('action' => 'delete', $signature_source['SignatureSource']['id'], 'admin' => true),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($signature_source['SignatureSource']['name'], array('controller' => 'signature_sources', 'action' => 'view', $signature_source['SignatureSource']['id'])),
		$this->Wrap->niceTime($signature_source['SignatureSource']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $signature_source['SignatureSource']['id'],
	);
}

$use_multiselect = false;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s', __('Signature Sources')),
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
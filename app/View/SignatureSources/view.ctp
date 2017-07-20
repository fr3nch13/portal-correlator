<?php 
// File: app/View/SignatureSources/view.ctp

$page_options = array();

if(AuthComponent::user('role') == 'admin')
{
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $signature_source['SignatureSource']['id'], 'admin' => true));
	$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $signature_source['SignatureSource']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
}

$details = array();
$details[] = array('name' => __('Name'), 'value' => $signature_source['SignatureSource']['name']);
$details[] = array('name' => __('Slug'), 'value' => $signature_source['SignatureSource']['slug']);
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($signature_source['SignatureSource']['created']));

$stats = array(
	array(
		'id' => 'Signature',
		'name' => __('Related %s', __('Signatures')), 
		'value' => $signature_source['SignatureSource']['counts']['Signature.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
);


$tabs = array(
	array(
	'key' => 'Signatures',
	'title' => __('Related %s', __('Signatures')), 
	'url' => array('controller' => 'signatures', 'action' => 'signature_source', $signature_source['SignatureSource']['id']),
	),
/*
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'signature_manager', $signature_source['SignatureSource']['id']),
	),
*/
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Signature Source'). ': '. $signature_source['SignatureSource']['name'],
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>

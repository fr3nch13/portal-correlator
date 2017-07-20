<?php 
// File: app/View/Vectors/auto_tracking_vt.ctp

// content
$th = array();
$th['Vector.vector'] = array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector'));
$th['Vector.type'] = array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type'));
$th['VectorDetail.vt_lookup'] = array('content' => __('VT Tracking Level'), 'options' => array('sort' => 'VectorDetail.vt_lookup'));
$th['VectorSourceFirst.source_type'] = array('content' => __('First Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type'));
$th['VectorSourceFirst.created'] = array('content' => __('First Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created'));
$th['VectorSourceLast.source_type'] = array('content' => __('Last Source'), 'options' => array('sort' => 'VectorSourceLast.source_type'));
$th['VectorSourceLast.created'] = array('content' => __('Last Source Added'), 'options' => array('sort' => 'VectorSourceLast.created'));
$th['VectorDetail.vt_checked'] = array('content' => __('VT Last Checked'), 'options' => array('sort' => 'VectorDetail.vt_checked'));
$th['VectorDetail.vt_updated'] = array('content' => __('VT Last Updated'), 'options' => array('sort' => 'VectorDetail.vt_updated'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = true;


$td = array();
foreach ($vectors as $i => $vector)
{	
	$td[$i] = array();
	$td[$i]['Vector.vector'] = $this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id']));
	$td[$i]['Vector.type'] = $this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type']));
	$td[$i]['VectorDetail.vt_lookup'] = $this->Wrap->vtAutoLookupLevel($vector['VectorDetail']['vt_lookup'], true);
	$td[$i]['VectorSourceFirst.source_type'] = $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']);
	$td[$i]['VectorSourceFirst.created'] = $this->Wrap->niceTime($vector['VectorSourceFirst']['created']);
	$td[$i]['VectorSourceLast.source_type'] = $this->Wrap->niceWord($vector['VectorSourceLast']['source_type']);
	$td[$i]['VectorSourceLast.created'] = $this->Wrap->niceTime($vector['VectorSourceLast']['created']);
	$td[$i]['VectorDetail.vt_checked'] = $this->Wrap->niceTime($vector['VectorDetail']['vt_checked']);
	$td[$i]['VectorDetail.vt_updated'] = $this->Wrap->niceTime($vector['VectorDetail']['vt_updated']);
	$td[$i]['actions'] = array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])).
			$this->Html->link(__('Turn Off'), array('action' => 'auto_tracking_vt_off', $vector['Vector']['id']), array('confirm' => 'Are you sure?')),
			array('class' => 'actions'),
		);
	$td[$i]['multiselect'] = $vector['Vector']['id'];
} 

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s Auto Tracking %s - All', __('VirusTotal'), __('Vectors')),
	'page_description' => __('List of %s that have been marked for automatic %s tracking.', __('Vectors'), __('VirusTotal')),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'vttracking' => __('Modify VT Tracking - All'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'vectors',
		'action' => 'auto_tracking_vt',
		'page' => 1,
	),
));
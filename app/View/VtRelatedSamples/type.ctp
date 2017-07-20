<?php 
// File: app/View/VtRelatedSamples/type.ctp

$page_options = array(
	$this->Html->link(__('View All'), array('action' => 'index')),
);

// content
$th = array(
	'VectorLookup.vector' => array('content' => __('Looked up %s', __('Vector')), 'options' => array('sort' => 'VectorLookup.vector')),
	'VectorSample.vector' => array('content' => __('Sample %s', __('Vector')), 'options' => array('sort' => 'VectorSample.vector')),
//	'VtRelatedSample.type' => array('content' => __('Type'), 'options' => array('sort' => 'VtRelatedSample.type')),
	'VtRelatedSample.total' => array('content' => __('Total'), 'options' => array('sort' => 'VtRelatedSample.total')),
	'VtRelatedSample.positives' => array('content' => __('Positives'), 'options' => array('sort' => 'VtRelatedSample.positives')),
	'VtRelatedSample.date' => array('content' => __('Reported Date'), 'options' => array('sort' => 'VtRelatedSample.date')),
	'VtRelatedSample.first_seen' => array('content' => __('First Seen'), 'options' => array('sort' => 'VtRelatedSample.first_seen')),
	'VtRelatedSample.last_seen' => array('content' => __('Last Seen'), 'options' => array('sort' => 'VtRelatedSample.last_seen')),
	'VtRelatedSample.created' => array('content' => __('Added'), 'options' => array('sort' => 'VtRelatedSample.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vt_related_samples as $i => $vt_related_sample)
{
	$td[$i] = array(
		$this->Html->link($vt_related_sample['VectorLookup']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_related_sample['VectorLookup']['id'])),
		$this->Html->link($vt_related_sample['VectorSample']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_related_sample['VectorSample']['id'])),
//		$this->Html->link($this->Wrap->vtNiceRelatedType($vt_related_sample['VtRelatedSample']['type']), array('action' => 'type', $vt_related_sample['VectorSample']['id'])),
		$vt_related_sample['VtRelatedSample']['total'],
		$vt_related_sample['VtRelatedSample']['positives'],
		$this->Wrap->niceTime($vt_related_sample['VtRelatedSample']['date']),
		$this->Wrap->niceTime($vt_related_sample['VtRelatedSample']['first_seen']),
		$this->Wrap->niceTime($vt_related_sample['VtRelatedSample']['last_seen']),
		$this->Wrap->niceTime($vt_related_sample['VtRelatedSample']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s by %s: %s', __('VirusTotal'), __('Related Samples'), __('Type'), $this->Wrap->vtNiceRelatedType($type)),
	'search_placeholder' => __('Related Samples'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
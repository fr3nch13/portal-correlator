<?php 
// File: app/View/VtDetectedUrls/index.ctp

$page_options = array(
);

// content
$th = array(
	'VectorLookup.vector' => array('content' => __('Looked up %s', __('Vector')), 'options' => array('sort' => 'VectorLookup.vector')),
	'VectorUrl.vector' => array('content' => __('Url %s', __('Vector')), 'options' => array('sort' => 'VectorUrl.vector')),
	'VtDetectedUrl.total' => array('content' => __('Total'), 'options' => array('sort' => 'VtRelatedSample.total')),
	'VtDetectedUrl.positives' => array('content' => __('Positives'), 'options' => array('sort' => 'VtRelatedSample.positives')),
	'VtDetectedUrl.scan_date' => array('content' => __('Scan Date'), 'options' => array('sort' => 'VtDetectedUrl.scan_date')),
	'VtDetectedUrl.first_seen' => array('content' => __('First Seen'), 'options' => array('sort' => 'VtDetectedUrl.first_seen')),
	'VtDetectedUrl.last_seen' => array('content' => __('Last Seen'), 'options' => array('sort' => 'VtDetectedUrl.last_seen')),
	'VtDetectedUrl.created' => array('content' => __('Added'), 'options' => array('sort' => 'VtDetectedUrl.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);
$td = array();
foreach ($vt_detected_urls as $i => $vt_detected_url)
{
	$actions = array();
	$VectorLookup_link = $this->Html->link($vt_detected_url['VectorLookup']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_detected_url['VectorLookup']['id']));
	$VectorUrl_link = $this->Html->link($vt_detected_url['VectorUrl']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_detected_url['VectorUrl']['id']));
	
	if(in_array($vt_detected_url['VectorLookup']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT Lookup View'), array('controller' => 'vectors', 'action' => 'vtview', $vt_detected_url['VectorLookup']['id']));
		$VectorLookup_link = $this->Html->link($vt_detected_url['VectorLookup']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $vt_detected_url['VectorLookup']['id']));
	}
	$actions[] = $this->Html->link(__('Lookup View'), array('controller' => 'vectors', 'action' => 'view', $vt_detected_url['VectorLookup']['id']));
	
	if(in_array($vt_detected_url['VectorUrl']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT Url View'), array('controller' => 'vectors', 'action' => 'vtview', $vt_detected_url['VectorUrl']['id']));
		$VectorUrl_link = $this->Html->link($vt_detected_url['VectorUrl']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $vt_detected_url['VectorUrl']['id']));
	}
	$actions[] = $this->Html->link(__('Url View'), array('controller' => 'vectors', 'action' => 'view', $vt_detected_url['VectorUrl']['id']));
	
	$td[$i] = array(
		$VectorLookup_link,
		$VectorUrl_link,
		$vt_detected_url['VtDetectedUrl']['total'],
		$vt_detected_url['VtDetectedUrl']['positives'],
		$this->Wrap->niceTime($vt_detected_url['VtDetectedUrl']['scan_date']),
		$this->Wrap->niceTime($vt_detected_url['VtDetectedUrl']['first_seen']),
		$this->Wrap->niceTime($vt_detected_url['VtDetectedUrl']['last_seen']),
		$this->Wrap->niceTime($vt_detected_url['VtDetectedUrl']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('VirusTotal'), __('Detected Urls')),
	'search_placeholder' => __('Detected Urls'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
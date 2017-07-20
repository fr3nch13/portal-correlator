<?php 
// File: app/View/VectorSources/admin_vector.ctp

$page_options = array();

$th = array();
$th['VectorSource.source_type'] = array('content' => __('Source Type'), 'options' => array('sort' => 'VectorSource.source_type'));
$th['VectorSource.source'] = array('content' => __('Source'), 'options' => array('sort' => 'VectorSource.source'));
$th['VectorSource.sub_source'] = array('content' => __('Sub Source'), 'options' => array('sort' => 'VectorSource.sub_source'));
$th['VectorSource.count'] = array('content' => __('Count'), 'options' => array('sort' => 'VectorSource.count'));
$th['VectorSource.created'] = array('content' => __('Source Added'), 'options' => array('sort' => 'VectorSource.created'));

// content
$td = array();
foreach ($vector_sources as $i => $vector_source)
{
	$td[$i] = array();
	$td[$i]['VectorSource.source_type'] = $this->Wrap->niceWord($vector_source['VectorSource']['source_type']);
	$td[$i]['VectorSource.source'] = $this->Wrap->sourceUser($vector_source['VectorSource']['source']);
	$td[$i]['VectorSource.sub_source'] = $vector_source['VectorSource']['sub_source'];
	$td[$i]['VectorSource.count'] = $vector_source['VectorSource']['count'];
	$td[$i]['VectorSource.created'] = $this->Wrap->niceTime($vector_source['VectorSource']['created']);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Vector Sources'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
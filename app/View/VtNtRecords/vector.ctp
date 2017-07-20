<?php 
// File: app/View/VtNtRecords/vector.ctp

$page_options = array(
);

// content
$th = array(
//	'VectorLookup.vector' => array('content' => __('Looked up %s', __('Vector')), 'options' => array('sort' => 'VectorLookup.vector')),
	'VtNtRecord.protocol' => array('content' => __('Protocol'), 'options' => array('sort' => 'VtNtRecord.protocol')),
	'VectorSrc.vector' => array('content' => __('Source %s', __('Vector')), 'options' => array('sort' => 'VectorSrc.vector')),
	'VtNtRecord.src_port' => array('content' => __('Source %s', __('Port')), 'options' => array('sort' => 'VtNtRecord.src_port')),
	'VectorDst.vector' => array('content' => __('Dest %s', __('Vector')), 'options' => array('sort' => 'VectorDst.vector')),
	'VtNtRecord.dst_port' => array('content' => __('Dest %s', __('Port')), 'options' => array('sort' => 'VtNtRecord.dst_port')),
	'VtNtRecord.first_seen' => array('content' => __('First Seen'), 'options' => array('sort' => 'VtNtRecord.first_seen')),
	'VtNtRecord.last_seen' => array('content' => __('Last Seen'), 'options' => array('sort' => 'VtNtRecord.last_seen')),
	'VtNtRecord.created' => array('content' => __('Added'), 'options' => array('sort' => 'VtNtRecord.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($vt_nt_records as $i => $vt_nt_record)
{
	$actions = array();
	$VectorSrc_link = $this->Html->link($vt_nt_record['VectorSrc']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_nt_record['VectorSrc']['id']));
	$VectorDst_link = $this->Html->link($vt_nt_record['VectorDst']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_nt_record['VectorDst']['id']));
	
	if(in_array($vt_nt_record['VectorSrc']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View Src'), array('controller' => 'vectors', 'action' => 'vtview', $vt_nt_record['VectorSrc']['id']));
		$VectorSrc_link = $this->Html->link($vt_nt_record['VectorSrc']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $vt_nt_record['VectorSrc']['id']));
	}
	if(in_array($vt_nt_record['VectorSrc']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View Dst'), array('controller' => 'vectors', 'action' => 'vtview', $vt_nt_record['VectorDst']['id']));
		$VectorDst_link = $this->Html->link($vt_nt_record['VectorDst']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $vt_nt_record['VectorDst']['id']));
	}
	$actions[] = $this->Html->link(__('View Src'), array('controller' => 'vectors', 'action' => 'view', $vt_nt_record['VectorSrc']['id']));
	$actions[] = $this->Html->link(__('View Dst'), array('controller' => 'vectors', 'action' => 'view', $vt_nt_record['VectorDst']['id']));
	
	$td[$i] = array(
//		$this->Html->link($vt_nt_record['VectorLookup']['vector'], array('controller' => 'vectors', 'action' => 'view', $vt_nt_record['VectorLookup']['id'])),
		$this->Html->link($this->Wrap->niceWord($vt_nt_record['VtNtRecord']['protocol']), array('action' => 'protocol', $vt_nt_record['VtNtRecord']['id'])),
		$VectorSrc_link,
		$this->Html->link($vt_nt_record['VtNtRecord']['src_port'], array('action' => 'src_port', $vt_nt_record['VtNtRecord']['id'])),
		$VectorDst_link,
		$this->Html->link($vt_nt_record['VtNtRecord']['dst_port'], array('action' => 'dst_port', $vt_nt_record['VtNtRecord']['id'])),
		$this->Wrap->niceTime($vt_nt_record['VtNtRecord']['first_seen']),
		$this->Wrap->niceTime($vt_nt_record['VtNtRecord']['last_seen']),
		$this->Wrap->niceTime($vt_nt_record['VtNtRecord']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('VirusTotal'), __('Network Records')),
	'search_placeholder' => __('Network Records'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
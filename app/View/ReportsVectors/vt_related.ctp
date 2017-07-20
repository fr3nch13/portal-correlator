<?php 
// File: app/View/ReportsVectors/vt_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'Report.name' => array('content' => __('Report'), 'options' => array('sort' => 'Report.name')),
//	'ReportsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'ReportsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'ReportsVector.created' => array('content' => __('Added to Report'), 'options' => array('sort' => 'ReportsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($reports_vectors as $i => $reports_vector)
{
	$actions = array();
	$vector_link = $this->Html->link($reports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $reports_vector['Vector']['id']));
	
	if(in_array($reports_vector['Vector']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View'), array('controller' => 'vectors', 'action' => 'vtview', $reports_vector['Vector']['id']));
		$vector_link = $this->Html->link($reports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $reports_vector['Vector']['id']));
	}
	
	$actions[] = $this->Html->link(__('View'), array('action' => 'view', $reports_vector['Vector']['id']));
	$td[$i] = array(
		$vector_link,
		$this->Html->link($reports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $reports_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($reports_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $reports_vector['Vector']['type'])),
		$this->Html->link($reports_vector['Report']['name'], array('controller' => 'reports', 'action' => 'view', $reports_vector['Report']['id'])),
//		$this->Wrap->yesNo($reports_vector['ReportsVector']['active']),
		$reports_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($reports_vector['ReportsVector']['created']),
		$this->Wrap->niceTime($reports_vector['Vector']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', _('Report'), _('Vectors')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
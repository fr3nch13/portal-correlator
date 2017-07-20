<?php 
// File: app/View/ImportsVectors/vt_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'Import.name' => array('content' => __('Import'), 'options' => array('sort' => 'Import.name')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'ImportsVector.created' => array('content' => __('Added to Import'), 'options' => array('sort' => 'ImportsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($imports_vectors as $i => $imports_vector)
{
	$actions = array();
	$vector_link = $this->Html->link($imports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $imports_vector['Vector']['id']));
	
	if(in_array($imports_vector['Vector']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View'), array('controller' => 'vectors', 'action' => 'vtview', $imports_vector['Vector']['id']));
		$vector_link = $this->Html->link($imports_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $imports_vector['Vector']['id']));
	}
	
	$actions[] = $this->Html->link(__('View'), array('action' => 'view', $imports_vector['Vector']['id']));
	$td[$i] = array(
		$vector_link,
		$this->Html->link($imports_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $imports_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($imports_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $imports_vector['Vector']['type'])),
		$this->Html->link($imports_vector['Import']['name'], array('controller' => 'imports', 'action' => 'view', $imports_vector['Import']['id'])),
		$imports_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($imports_vector['ImportsVector']['created']),
		$this->Wrap->niceTime($imports_vector['Vector']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', _('Import'), _('Vectors')),
	'page_options' => $page_options,
	'search_placeholder' => __('%s %s', _('Import'), _('Vectors')),
	'th' => $th,
	'td' => $td,
));
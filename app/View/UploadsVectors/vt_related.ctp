<?php 
// File: app/View/UploadsVectors/vt_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'Upload.name' => array('content' => __('Upload'), 'options' => array('sort' => 'Upload.name')),
//	'UploadsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'UploadsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'UploadsVector.created' => array('content' => __('Added to Upload'), 'options' => array('sort' => 'UploadsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($uploads_vectors as $i => $uploads_vector)
{
	$actions = array();
	$vector_link = $this->Html->link($uploads_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $uploads_vector['Vector']['id']));
	
	if(in_array($uploads_vector['Vector']['type'], $vtTypeList))
	{
		$actions[] = $this->Html->link(__('VT View'), array('controller' => 'vectors', 'action' => 'vtview', $uploads_vector['Vector']['id']));
		$vector_link = $this->Html->link($uploads_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'vtview', $uploads_vector['Vector']['id']));
	}
	
	$actions[] = $this->Html->link(__('View'), array('action' => 'view', $uploads_vector['Vector']['id']));
	$td[$i] = array(
		$vector_link,
		$this->Html->link($uploads_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $uploads_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($uploads_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $uploads_vector['Vector']['type'])),
		$this->Html->link($uploads_vector['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $uploads_vector['Upload']['id'])),
//		$this->Wrap->yesNo($uploads_vector['UploadsVector']['active']),
		$uploads_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($uploads_vector['UploadsVector']['created']),
		$this->Wrap->niceTime($uploads_vector['Vector']['created']),
		array(
			implode("\n", $actions),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', _('Upload'), _('Vectors')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
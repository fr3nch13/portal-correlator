<?php 
// File: app/View/VectorTypes/view.ctp
$details = array();
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($vectorType['VectorType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($vectorType['VectorType']['modified']));


$page_options = array();

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'Vectors',
	'title' => __('Vectors'),
	'url' => array('controller' => 'vectors', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
$tabs[] = array(
	'key' => 'CategoriesVector',
	'title' => __('Category Vectors'),
	'url' => array('controller' => 'categories_vectors', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
$tabs[] = array(
	'key' => 'ReportsVector',
	'title' => __('Report Vectors'),
	'url' => array('controller' => 'reports_vectors', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
$tabs[] = array(
	'key' => 'UploadsVector',
	'title' => __('File Vectors'),
	'url' => array('controller' => 'uploads_vectors', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
/*
$tabs[] = array(
	'key' => 'CategoriesVector',
	'title' => __('All Vectors'),
	'url' => array('controller' => 'vectors_types', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
*/
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($vectorType['VectorType']['desc']),
);

$stats[] = array(
	'id' => 'Vectors',
	'name' => __('Vectors'), 
	'value' => $vectorType['VectorType']['counts']['Vector.all'], 
	'tab' => array('tabs', '1'), // the tab to display
);

$stats[] = array(
	'id' => 'CategoriesVector',
	'name' => __('Category Vectors'), 
	'value' => $vectorType['VectorType']['counts']['CategoriesVector.all'], 
	'tab' => array('tabs', '2'), // the tab to display
);

$stats[] = array(
	'id' => 'ReportsVector',
	'name' => __('Report Vectors'), 
	'value' => $vectorType['VectorType']['counts']['ReportsVector.all'], 
	'tab' => array('tabs', '3'), // the tab to display
);

$stats[] = array(
	'id' => 'UploadsVector',
	'name' => __('File Vectors'), 
	'value' => $vectorType['VectorType']['counts']['UploadsVector.all'], 
	'tab' => array('tabs', '4'), // the tab to display
);


echo $this->element('Utilities.page_view', array(
	'page_title' => __('Vector Group'). ': '. $vectorType['VectorType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
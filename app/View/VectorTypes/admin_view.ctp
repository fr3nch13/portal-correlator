<?php 
// File: app/View/VectorTypes/admin_view.ctp
$page_options = array();
$page_options[] = $this->Form->postLink(__('Make %s', __('Default Holder')),array('action' => 'setdefault', 'holder', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Form->postLink(__('Toggle %s State', __('Benign')),array('action' => 'toggle', 'bad', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Form->postLink(__('Toggle %s State', __('Active')),array('action' => 'toggle', 'active', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $vectorType['VectorType']['id']));
$page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $vectorType['VectorType']['id']),array('confirm' => 'Are you sure?'));

$details = array();
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($vectorType['VectorType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($vectorType['VectorType']['modified']));
$details[] = array('name' => __('Default Holder'), 'value' => $this->Wrap->yesNo($vectorType['VectorType']['holder']));
$details[] = array('name' => __('Active'), 'value' => $this->Wrap->yesNo($vectorType['VectorType']['active']));
$details[] = array('name' => __('Benign'), 'value' => $this->Wrap->yesNo($vectorType['VectorType']['bad']));

$stats = array();
$tabs = array();



$stats[] = array(
	'id' => 'Vectors',
	'name' => __('Vectors'), 
	'value' => $vectorType['VectorType']['counts']['Vector.all'], 
	'tab' => array('tabs', '1'), // the tab to display
);
$stats[] = array(
	'id' => 'Vectors',
	'name' => __('Active Vectors'), 
	'value' => $vectorType['VectorType']['counts']['Vector.good'], 
	'tab' => array('tabs', '2'), // the tab to display
);
$stats[] = array(
	'id' => 'Vectors',
	'name' => __('Benign Vectors'), 
	'value' => $vectorType['VectorType']['counts']['Vector.bad'], 
	'tab' => array('tabs', '3'), // the tab to display
);
$stats[] = array(
	'id' => 'CategoriesVector',
	'name' => __('Category Vectors'), 
	'value' => $vectorType['VectorType']['counts']['CategoriesVector.all'], 
	'tab' => array('tabs', '4'), // the tab to display
);

$stats[] = array(
	'id' => 'ReportsVector',
	'name' => __('Report Vectors'), 
	'value' => $vectorType['VectorType']['counts']['ReportsVector.all'], 
	'tab' => array('tabs', '5'), // the tab to display
);

$stats[] = array(
	'id' => 'UploadsVector',
	'name' => __('File Vectors'), 
	'value' => $vectorType['VectorType']['counts']['UploadsVector.all'], 
	'tab' => array('tabs', '6'), // the tab to display
);

//
$tabs[] = array(
	'key' => 'Vectors',
	'title' => __('All Vectors'),
	'url' => array('controller' => 'vectors', 'action' => 'vector_type', $vectorType['VectorType']['id']),
);
$tabs[] = array(
	'key' => 'Vectors',
	'title' => __('Active Vectors'),
	'url' => array('controller' => 'vectors', 'action' => 'vector_type_good', $vectorType['VectorType']['id']),
);
$tabs[] = array(
	'key' => 'Vectors',
	'title' => __('Benign Vectors'),
	'url' => array('controller' => 'vectors', 'action' => 'vector_type_bad', $vectorType['VectorType']['id']),
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
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($vectorType['VectorType']['desc']),
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
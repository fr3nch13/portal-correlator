<?php 
// File: app/View/Imports/admin_view.ctp
$page_options = array();
if(!$import['Import']['reviewed'])
{
	$page_options[] = $this->Form->postLink(__('Mark Reviewed'),array('action' => 'reviewed', $import['Import']['id']),array('confirm' => 'Are you sure?', 'class' => 'button_red'));
}
$page_options[] = $this->Html->link(__('Download'), array('action' => 'download', $import['Import']['id']));
//$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $import['Import']['id']));
//$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $import['Import']['id']),array('confirm' => 'Are you sure?'));

$details_left = array();
$details_left[] = array('name' => __('File Name'), 'value' => $import['Import']['filename']);
$details_left[] = array('name' => __('File Type'), 'value' => $import['Import']['type']);
$details_left[] = array('name' => __('Mime Type'), 'value' => $import['Import']['mimetype']);
$details_left[] = array('name' => __('Size'), 'value' => $this->Wrap->formatBytes($import['Import']['size']));

$details_right = array();

$details_right[] = array('name' => __('Manager'), 'value' => $this->Html->link($import['ImportManager']['name'], array('controller' => 'import_managers', 'action' => 'view', $import['ImportManager']['id'])));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($import['Import']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($import['Import']['modified']));
$details_right[] = array('name' => __('Reviewed'), 'value' => $this->Wrap->niceTime($import['Import']['reviewed']));


$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'import_vectors',
	'name' => __('Vectors'), 
	'ajax_count_url' => array('controller' => 'imports_vectors', 'action' => 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'import_vectors',
	'title' => __('Vectors'),
	'url' => array('controller' => 'imports_vectors', 'action' => 'import', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'temp_import_vectors',
	'name' => __('Temp %s', __('Vectors')), 
	'ajax_count_url' => array('controller' => 'temp_imports_vectors', 'action' => 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'temp_import_vectors',
	'title' => __('Temp %s', __('Vectors')),
	'url' => array('controller' => 'temp_imports_vectors', 'action' => 'import', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'vectors_unique',
	'name' => __('Unique %s', __('Vectors')), 
	'ajax_count_url' => array('controller' => 'imports_vectors', 'action' => 'unique', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'vectors_unique',
	'title' => __('Unique %s', __('Vectors')),
	'url' => array('controller' => 'imports_vectors', 'action' => 'unique', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'categories_related',
	'name' => __('Related %s', __('Categories')), 
	'ajax_count_url' => array('controller' => 'categories', 'action' => 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_related',
	'title' => __('Related %s', __('Categories')),
	'url' => array('controller' => 'categories', 'action' => 'import', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'categories_vectors_related',
	'name' => __('Related %s %s', __('Category'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'categories_vectors', 'action' => 'import_related', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_vectors_related',
	'title' => __('Related %s %s', __('Category'), __('Vectors')),
	'url' => array('controller' => 'categories_vectors', 'action' => 'import_related', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'reports_related',
	'name' => __('Related %s', __('Reports')), 
	'ajax_count_url' => array('controller' => 'reports', 'action' => 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_related',
	'title' => __('Related %s', __('Reports')),
	'url' => array('controller' => 'reports', 'action' => 'import', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'reports_vectors_related',
	'name' => __('Related %s %s', __('Report'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'reports_vectors', 'action' => 'import_related', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_vectors_related',
	'title' => __('Related %s %s', __('Report'), __('Vectors')),
	'url' => array('controller' => 'reports_vectors', 'action' => 'import_related', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'imports_related',
	'name' => __('Related %s', __('Imports')), 
	'ajax_count_url' => array('controller' => 'imports', 'action' => 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'imports_related',
	'title' => __('Related %s', __('Imports')),
	'url' => array('controller' => 'imports', 'action' => 'import', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'imports_vectors_related',
	'name' => __('Related %s %s', __('Import'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'imports_vectors', 'action' => 'import_related', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'imports_vectors_related',
	'title' => __('Related %s %s', __('Import'), __('Vectors')),
	'url' => array('controller' => 'imports_vectors', 'action' => 'import_related', $import['Import']['id']),
);

$stats[] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'import', $import['Import']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'import', $import['Import']['id']),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Import'). ': '. $import['Import']['name'],
	'page_options' => $page_options,
	'details_left_title' => __('Details'),
	'details_left' => $details_left,
	'details_right_title' => '&nbsp;',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
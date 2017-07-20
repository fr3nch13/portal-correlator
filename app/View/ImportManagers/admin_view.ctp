<?php 
// File: app/View/ImportManagers/view.ctp
//		'added_user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'key' => 'index'),
//		'org_group_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'key' => 'index'),


$details_left = array();
$details_left[] = array('name' => __('Key'), 'value' => $import_manager['ImportManager']['key']);
$details_left[] = array('name' => __('Parser'), 'value' => $import_manager['ImportManager']['parser']);
$details_left[] = array('name' => __('Location'), 'value' => $import_manager['ImportManager']['location']);
$details_left[] = array('name' => __('Automatically Run?'), 'value' => $this->Wrap->yesNo($import_manager['ImportManager']['cron']));
$details_left[] = array('name' => __('Auto Review?'), 'value' => $this->Wrap->yesNo($import_manager['ImportManager']['auto_reviewed']));
$details_left[] = array('name' => __('Active'), 'value' => $this->Wrap->yesNo($import_manager['ImportManager']['active']));
//$details_left[] = array('name' => __('Active'), 'value' => $this->Wrap->yesNo($import_manager['ImportManager']['active']));

$details_right = array();
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($import_manager['ImportManager']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($import_manager['ImportManager']['modified']));
$details_right[] = array('name' => __('CSV Fields'), 'value' => $this->Wrap->showCsvFields($import_manager['ImportManager']['csv_fields']));
$details_right[] = array('name' => __('Local Path'), 'value' => $import_manager['ImportManager']['local_path']);
//$details_right[] = array('name' => __('URL'), 'value' => $import_manager['ImportManager']['url']);

$page_options = array(
);
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $import_manager['ImportManager']['id']));
$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $import_manager['ImportManager']['id']),array('confirm' => 'Are you sure?'));

$stats = array(
	array(
		'id' => 'imports',
		'name' => __('Imports'), 
		'value' => $import_manager['ImportManager']['counts']['Import.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'import_manager_logs',
		'name' => __('Imports Logs'), 
		'value' => $import_manager['ImportManager']['counts']['ImportManagerLog.all'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'tagsImportManager',
		'name' => __('Tags'), 
		'value' => $import_manager['ImportManager']['counts']['Tagged.all'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'imports',
		'title' => __('Imports'),
		'url' => array('controller' => 'imports', 'action' => 'import_manager', $import_manager['ImportManager']['id']),
	),
	array(
		'key' => 'notes',
		'title' => __('Description'),
		'content' => $this->Wrap->descView($import_manager['ImportManager']['desc']),
	),
	array(
		'key' => 'imports',
		'title' => __('Import Logs'),
		'url' => array('controller' => 'import_manager_logs', 'action' => 'import_manager', $import_manager['ImportManager']['id']),
	),
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'import_manager', $import_manager['ImportManager']['id']),
	),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Import Manager'). ': '. $import_manager['ImportManager']['name'],
	'page_options' => $page_options,
	'details_left_title' => __('Details'),
	'details_left' => $details_left,
	'details_right_title' => '&nbsp;',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>

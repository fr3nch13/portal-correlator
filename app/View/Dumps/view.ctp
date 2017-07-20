<?php 
// File: app/View/Dumps/view.ctp
$details = array();
$details[] = array('name' => __('Owner'), 'value' => $this->Html->link($dump['User']['name'], array('controller' => 'users', 'action' => 'view', $dump['User']['id'])));
$details[] = array('name' => __('File Name'), 'value' => $dump['Dump']['filename']);
$details[] = array('name' => __('MD5'), 'value' => $this->Html->link($dump['Dump']['md5'], array('controller' => 'dumps', 'action' => 'index', 'q' => $dump['Dump']['md5'])) );
$details[] = array('name' => __('Type'), 'value' => $dump['Dump']['type']);
$details[] = array('name' => __('Mime Type'), 'value' => $dump['Dump']['mimetype']);
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($dump['Dump']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($dump['Dump']['modified']));

if(trim($dump['Dump']['filename']))
{
	$page_options = array(
		$this->Html->link(__('Download'), array('action' => 'download', $dump['Dump']['id'])),
	);
}
if($dump['Dump']['user_id'] == AuthComponent::user('id'))
{
	$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $dump['Dump']['id']),array('confirm' => 'Are you sure?'));
			
}
$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'dump_vectors',
	'name' => __('Vectors'), 
	'ajax_count_url' => array('controller' => 'dumps_vectors', 'action' => 'dump', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'dump_vectors',
	'title' => __('Vectors'),
	'url' => array('controller' => 'dumps_vectors', 'action' => 'dump', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'vectors_unique',
	'name' => __('Unique %s', __('Vectors')), 
	'ajax_count_url' => array('controller' => 'dumps_vectors', 'action' => 'unique', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'vectors_unique',
	'title' => __('Unique %s', __('Vectors')),
	'url' => array('controller' => 'dumps_vectors', 'action' => 'unique', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'categories_related',
	'name' => __('Related %s', __('Categories')), 
	'ajax_count_url' => array('controller' => 'categories', 'action' => 'dump', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_related',
	'title' => __('Related %s', __('Categories')),
	'url' => array('controller' => 'categories', 'action' => 'dump', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'categories_vectors_related',
	'name' => __('Related %s %s', __('Category'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'categories_vectors', 'action' => 'dump_related', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_vectors_related',
	'title' => __('Related %s %s', __('Category'), __('Vectors')),
	'url' => array('controller' => 'categories_vectors', 'action' => 'dump_related', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'reports_related',
	'name' => __('Related %s', __('Reports')), 
	'ajax_count_url' => array('controller' => 'reports', 'action' => 'dump', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_related',
	'title' => __('Related %s', __('Reports')),
	'url' => array('controller' => 'reports', 'action' => 'dump', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'reports_vectors_related',
	'name' => __('Related %s %s', __('Report'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'reports_vectors', 'action' => 'dump_related', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_vectors_related',
	'title' => __('Related %s %s', __('Report'), __('Vectors')),
	'url' => array('controller' => 'reports_vectors', 'action' => 'dump_related', $dump['Dump']['id']),
);

$stats[] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'dump', $dump['Dump']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'dump', $dump['Dump']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Dump'). ': '. $dump['Dump']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
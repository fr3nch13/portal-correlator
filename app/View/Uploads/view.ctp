<?php 
// File: app/View/Uploads/view.ctp
$details_left = array();
$details_left[] = array('name' => __('Owner'), 'value' => $this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])));
$details_left[] = array('name' => __('File Group'), 'value' => $this->Html->link($upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $upload['UploadType']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Category'), 'value' => $this->Html->link($upload['Category']['name'], array('controller' => 'categories', 'action' => 'view', $upload['Category']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Report'), 'value' => $this->Html->link($upload['Report']['name'], array('controller' => 'reports', 'action' => 'view', $upload['Report']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($upload['Upload']['public']));
$details_left[] = array('name' => __('User Source'), 'value' => $upload['Upload']['mysource']);

$details_right = array();
$details_right[] = array('name' => __('MD5'), 'value' => $this->Html->link($upload['Upload']['md5'], array('controller' => 'uploads', 'action' => 'index', 'q' => base64_encode(str_replace(array('+', '/'), array('-', '_'), $upload['Upload']['md5'])) )) );

$details_right[] = array('name' => __('Type'), 'value' => $upload['Upload']['type']);
$details_right[] = array('name' => __('Mime Type'), 'value' => $upload['Upload']['mimetype']);
$details_right[] = array('name' => __('Size'), 'value' => $this->Wrap->formatBytes($upload['Upload']['size']));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($upload['Upload']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($upload['Upload']['modified']));
$details_right[] = array('name' => __('Reviewed'), 'value' => $this->Wrap->niceTime($upload['Upload']['reviewed']));

$page_options = array(
	$this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id'])),
);
if($upload['Upload']['user_id'] == AuthComponent::user('id'))
{
//	$page_options[] = $this->Html->link(__('Toggle Public State'),array('action' => 'toggle', 'public', $upload['Upload']['id']),array('confirm' => 'Are you sure?'));
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $upload['Upload']['id']));
	$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $upload['Upload']['id']),array('confirm' => 'Are you sure?'));
			
}

$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'upload_vectors',
	'name' => __('Vectors'), 
	'ajax_count_url' => array('controller' => 'uploads_vectors', 'action' => 'upload', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'upload_vectors',
	'title' => __('Vectors'),
	'url' => array('controller' => 'uploads_vectors', 'action' => 'upload', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'categories_related',
	'name' => __('Related %s', __('Categories')), 
	'ajax_count_url' => array('controller' => 'categories', 'action' => 'upload', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_related',
	'title' => __('Related %s', __('Categories')),
	'url' => array('controller' => 'categories', 'action' => 'upload', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'categories_vectors_related',
	'name' => __('Related %s %s', __('Category'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'categories_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'categories_vectors_related',
	'title' => __('Related %s %s', __('Category'), __('Vectors')),
	'url' => array('controller' => 'categories_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'reports_related',
	'name' => __('Related %s', __('Reports')), 
	'ajax_count_url' => array('controller' => 'reports', 'action' => 'upload', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_related',
	'title' => __('Related %s', __('Reports')),
	'url' => array('controller' => 'reports', 'action' => 'upload', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'reports_vectors_related',
	'name' => __('Related %s %s', __('Report'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'reports_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'reports_vectors_related',
	'title' => __('Related %s %s', __('Report'), __('Vectors')),
	'url' => array('controller' => 'reports_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'uploads_related',
	'name' => __('Related %s', __('Files')), 
	'ajax_count_url' => array('controller' => 'uploads', 'action' => 'upload', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'uploads_related',
	'title' => __('Related %s', __('Files')),
	'url' => array('controller' => 'uploads', 'action' => 'upload', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'uploads_vectors_related',
	'name' => __('Related %s %s', __('File'), __('Vectors')), 
	'ajax_count_url' => array('controller' => 'uploads_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)),
);
$tabs[] = array(
	'key' => 'uploads_vectors_related',
	'title' => __('Related %s %s', __('File'), __('Vectors')),
	'url' => array('controller' => 'uploads_vectors', 'action' => 'upload_related', $upload['Upload']['id']),
);

$stats[] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'upload', $upload['Upload']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'upload', $upload['Upload']['id']),
);

if($upload['Upload']['user_id'] == AuthComponent::user('id'))
{
	$tabs[] = array(
		'key' => 'notes',
		'title' => __('Private Notes'),
		'content' => $this->Wrap->descView($upload['Upload']['desc_private']),
	);
}

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('File'). ': '. $upload['Upload']['filename'],
	'page_options' => $page_options,
	'details_left_title' => __('Details'),
	'details_left' => $details_left,
	'details_right_title' => '&nbsp;',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
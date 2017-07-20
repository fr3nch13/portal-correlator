<?php 
// File: app/View/TempUploads/view.ctp
$details_left = array();
$details_left[] = array('name' => __('Owner'), 'value' => $this->Html->link($temp_upload['User']['name'], array('controller' => 'users', 'action' => 'view', $temp_upload['User']['id'])));
$details_left[] = array('name' => __('File Group'), 'value' => $this->Html->link($temp_upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $temp_upload['UploadType']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Active Category'), 'value' => $this->Html->link($temp_upload['Category']['name'], array('controller' => 'categories', 'action' => 'view', $temp_upload['Category']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Active Report'), 'value' => $this->Html->link($temp_upload['Report']['name'], array('controller' => 'reports', 'action' => 'view', $temp_upload['Report']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Temp Category'), 'value' => $this->Html->link($temp_upload['TempCategory']['name'], array('controller' => 'temp_categories', 'action' => 'view', $temp_upload['TempCategory']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Temp Report'), 'value' => $this->Html->link($temp_upload['TempReport']['name'], array('controller' => 'temp_reports', 'action' => 'view', $temp_upload['TempReport']['id'])). '&nbsp;');
$details_left[] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($temp_upload['TempUpload']['public']));
$details_left[] = array('name' => __('User Source'), 'value' => $temp_upload['TempUpload']['mysource']);

$details_right = array();
$details_right[] = array('name' => __('MD5'), 'value' => $this->Html->link($temp_upload['TempUpload']['md5'], array('controller' => 'uploads', 'action' => 'index', 'q' => $temp_upload['TempUpload']['md5'])) );
$details_right[] = array('name' => __('Type'), 'value' => $temp_upload['TempUpload']['type']);
$details_right[] = array('name' => __('Mime Type'), 'value' => $temp_upload['TempUpload']['mimetype']);
$details_right[] = array('name' => __('Size'), 'value' => $this->Wrap->formatBytes($temp_upload['TempUpload']['size']));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($temp_upload['TempUpload']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($temp_upload['TempUpload']['modified']));

$page_options = array(
	$this->Html->link(__('Download'), array('action' => 'download', $temp_upload['TempUpload']['id'])),
);

//$page_options[] = $this->Html->link(__('Toggle Public State'),array('action' => 'toggle', 'public', $temp_upload['TempUpload']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $temp_upload['TempUpload']['id']));
$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $temp_upload['TempUpload']['id']),array('confirm' => 'Are you sure?'));
$page_options[] = $this->Form->postLink(__('Mark Reviewed'),array('action' => 'reviewed', $temp_upload['TempUpload']['id'], $temp_upload['TempUpload']['category_id'], $temp_upload['TempUpload']['report_id']),array('confirm' => 'Are you sure?', 'class' => 'button_red'));

$stats = array(
	array(
		'id' => 'vectorsTempUpload',
		'name' => __('Active Vectors'), 
		'value' => $temp_upload['TempUpload']['counts']['TempUploadsVector.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'tagsTempUpload',
		'name' => __('Tags'), 
		'value' => $temp_upload['TempUpload']['counts']['Tagged.all'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'vectors',
		'title' => __('All Vectors'),
		'url' => array('controller' => 'temp_uploads_vectors', 'action' => 'temp_upload', $temp_upload['TempUpload']['id']),
	),
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'temp_upload', $temp_upload['TempUpload']['id']),
	),
);

if($temp_upload['TempUpload']['user_id'] == AuthComponent::user('id'))
{
	$tabs[] = array(
		'key' => 'notes',
		'title' => __('Private Notes'),
		'content' => $this->Wrap->descView($temp_upload['TempUpload']['desc_private']),
	);
}

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Temp File'). ': '. $temp_upload['TempUpload']['filename'],
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

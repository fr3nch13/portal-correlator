<?php 
// File: app/View/UploadTypes/view.ctp
$details = array();
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($uploadType['UploadType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($uploadType['UploadType']['modified']));


$page_options = array();
// $page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $uploadType['UploadType']['id']));
// $page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $uploadType['UploadType']['id']),array('confirm' => 'Are you sure?'));

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'uploads',
	'title' => __('Files'),
	'url' => array('controller' => 'uploads', 'action' => 'upload_type', $uploadType['UploadType']['id']),
);
$tabs[] = array(
	'key' => 'tempuploads',
	'title' => __('My Temp Files'),
	'url' => array('controller' => 'temp_uploads', 'action' => 'upload_type', $uploadType['UploadType']['id']),
);
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($uploadType['UploadType']['desc']),
);

$stats[] = array(
	'id' => 'uploads',
	'name' => __('Files'), 
	'value' => $uploadType['UploadType']['counts']['Upload.public'], 
	'tab' => array('tabs', '1'), // the tab to display
);

$stats[] = array(
	'id' => 'temp_uploads',
	'name' => __('My Temp Files'), 
	'value' => $uploadType['UploadType']['counts']['TempUpload.mine'], 
	'tab' => array('tabs', '2'), // the tab to display
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('File Group'). ': '. $uploadType['UploadType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
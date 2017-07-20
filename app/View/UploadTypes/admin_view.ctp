<?php 
// File: app/View/UploadTypes/admin_view.ctp
$details = array();
$org_group = $this->Html->link(__('Global'), array('controller' => 'org_groups', 'action' => 'view', '0'));
if(isset($uploadType['OrgGroup']['id']) and $uploadType['OrgGroup']['id'])
{
	$org_group = $this->Html->link($uploadType['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $uploadType['OrgGroup']['id']));
}
$details[] = array('name' => __('Org Group'), 'value' => $org_group);
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($uploadType['UploadType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($uploadType['UploadType']['modified']));


$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $uploadType['UploadType']['id']));
$page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $uploadType['UploadType']['id']),array('confirm' => 'Are you sure?'));

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'uploads',
	'title' => __('Files'),
	'url' => array('controller' => 'uploads', 'action' => 'upload_type', $uploadType['UploadType']['id']),
);
/*
$tabs[] = array(
	'key' => 'tempuploads',
	'title' => __('My Temp Files'),
	'url' => array('controller' => 'temp_uploads', 'action' => 'upload_type', $uploadType['UploadType']['id']),
);
*/
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($uploadType['UploadType']['desc']),
);

$stats[] = array(
	'id' => 'uploads',
	'name' => __('Files'), 
	'value' => $uploadType['UploadType']['counts']['Upload.all'], 
	'tab' => array('tabs', '1'), // the tab to display
);
/*
$stats[] = array(
	'id' => 'temp_uploads',
	'name' => __('My Temp Files'), 
	'value' => $uploadType['UploadType']['counts']['TempUpload.all'], 
	'tab' => array('tabs', '2'), // the tab to display
);
*/
echo $this->element('Utilities.page_view', array(
	'page_title' => __('File Group'). ': '. $uploadType['UploadType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
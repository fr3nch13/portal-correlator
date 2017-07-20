<?php 
// File: app/View/Uploads/index.ctp

// content
$th = array();
	$th['Upload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'Upload.type'));
	$th['Upload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Upload.filename'));
	$th['UploadType.name'] = array('content' => __('File Group'), 'options' => array('sort' => 'UploadType.name'));
	$th['Upload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource'));
	$th['Category.name'] = array('content' => __('Category'), 'options' => array('sort' => 'Category.name'));
	$th['Report.name'] = array('content' => __('Report'), 'options' => array('sort' => 'Report.name'));
	$th['User.name'] = array('content' => __('Owner'), 'options' => array('sort' => 'User.name'));
	$th['OrgGroup.name'] = array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name'));
	$th['Upload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public'));
//	$th['Upload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Upload.size'));
//	$th['Upload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'Upload.md5'));
//	$th['Upload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public'));

//	$th['Upload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified'));
	$th['Upload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'Upload.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($uploads as $i => $upload)
{
	$upload_type = $upload['UploadType']['name'];
	if($upload['UploadType']['org_group_id'] == AuthComponent::user('org_group_id'))
	{
		$upload_type = $this->Html->link($upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $upload['UploadType']['id']));
	}
	
	$org_group = __('None');
	if(isset($upload['OrgGroup']['id']) and $upload['OrgGroup']['id'])
	{
		$org_group = $upload['OrgGroup']['name'];
	}
	
	$td[$i] = array();
	$td[$i]['Upload.type'] = $this->Wrap->fileIcon($upload['Upload']['type']);
	$td[$i]['Upload.filename'] = $this->Html->link($upload['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $upload['Upload']['id']));
	$td[$i]['UploadType.name'] = $upload_type;
	$td[$i]['Upload.mysource'] = $upload['Upload']['mysource'];
	$td[$i]['Category.name'] = $this->Html->link($upload['Category']['name'], array('controller' => 'categories', 'action' => 'view', $upload['Category']['id']));
	$td[$i]['Report.name'] = $this->Html->link($upload['Report']['name'], array('controller' => 'reports', 'action' => 'view', $upload['Report']['id']));
	$td[$i]['User.name'] = $this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id']));
	$td[$i]['OrgGroup.name'] = $org_group;
	$td[$i]['Upload.public'] = $this->Wrap->publicState($upload['Upload']['public']);
	
//	$td[$i]['Upload.size'] = $this->Wrap->formatBytes($upload['Upload']['size']);
//	$td[$i]['Upload.md5'] = $upload['Upload']['md5'];
	
//	$td[$i]['Upload.public'] = array(
//		$this->Wrap->publicState($upload['Upload']['public']), 
//		array('class' => 'actions'),
//	);
	
//	$td[$i]['Upload.modified'] = $this->Wrap->niceTime($upload['Upload']['modified']);
	$td[$i]['Upload.created'] = $this->Wrap->niceTime($upload['Upload']['created']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id']));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All Available Files'),
	'search_placeholder' => __('files'),
	'th' => $th,
	'td' => $td,
	));
?>
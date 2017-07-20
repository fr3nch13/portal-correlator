<?php 
// File: app/View/Uploads/admin_vector.ctp

// content
$th = array();
	$th['Upload.type'] = array('content' => __('File Type'), 'options' => array('sort' => 'Upload.type'));
	$th['Upload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Upload.filename'));
	$th['Upload.mysource'] = array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource'));
	$th['Category.name'] = array('content' => __('Category'));
	$th['Report.name'] = array('content' => __('Report'));
	$th['OrgGroup.name'] = array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name'));
	$th['User.name'] = array('content' => __('Owner'));
//	$th['Upload.size'] = array('content' => __('File Size'), 'options' => array('sort' => 'Upload.size'));
//	$th['Upload.md5'] = array('content' => __('File MD5'), 'options' => array('sort' => 'Upload.md5'));
//	$th['Upload.public'] = array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public'));

//	$th['Upload.modified'] = array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified'));
	$th['Upload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'Upload.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($uploads as $i => $upload)
{
	$org_group = $this->Html->link(__('None'), array('controller' => 'org_groups', 'action' => 'view', '0'));
	if(isset($upload['OrgGroup']['id']) and $upload['OrgGroup']['id'])
	{
		$org_group = $this->Html->link($upload['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $upload['OrgGroup']['id']));
	}
	$td[$i] = array();
	$td[$i]['Upload.type'] = $this->Wrap->fileIcon($upload['Upload']['type']);
	$td[$i]['Upload.filename'] = $this->Html->link($upload['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $upload['Upload']['id']));
	$td[$i]['Upload.mysource'] = $upload['Upload']['mysource'];
	$td[$i]['Category.name'] = '';
	if(isset($upload['Upload']['Category']['name']))
	{
		$td[$i]['Category.name'] = $this->Html->link($upload['Upload']['Category']['name'], array('controller' => 'categories', 'action' => 'view', $upload['Upload']['Category']['id']));
	}
	$td[$i]['Report.name'] = '';
	
	if(isset($upload['Upload']['Report']['name']))
	{
		$td[$i]['Report.name'] = $this->Html->link($upload['Upload']['Report']['name'], array('controller' => 'reports', 'action' => 'view', $upload['Upload']['Report']['id']));
	}
	$td[$i]['OrgGroup.name'] = $org_group;
	$td[$i]['User.name'] = $this->Html->link($upload['Upload']['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['Upload']['User']['id']));
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
	'page_title' => __('Files'),
//	'page_options' => $page_options,
	'search_placeholder' => __('Files'),
	'th' => $th,
	'td' => $td,
	));
?>
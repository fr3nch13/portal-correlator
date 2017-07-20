<?php 
// File: app/View/Uploads/dump.ctp

// content
$th = array(
	'Upload.filename' => array('content' => __('Name'), 'options' => array('sort' => 'Upload.filename')),
	'Upload.mysource' => array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'Category.name' => array('content' => __('Category'), 'options' => array('sort' => 'Category.name')),
	'Report.name' => array('content' => __('Report'), 'options' => array('sort' => 'Report.name')),
	'Upload.public' => array('content' => __('Share State'), 'options' => array('sort' => 'Report.public')),
//	'Upload.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified')),
	'Upload.created' => array('content' => __('Created'), 'options' => array('sort' => 'Upload.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploads as $i => $upload)
{
	$td[$i] = array(
		$this->Html->link($upload['Upload']['filename'], array('action' => 'view', $upload['Upload']['id'])),
		$this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])),
		$upload['Upload']['mysource'],
		$this->Html->link($upload['Category']['name'], array('controller' => 'categories', 'action' => 'view', $upload['Category']['id'])),
		$this->Html->link($upload['Report']['name'], array('controller' => 'reports', 'action' => 'view', $upload['Report']['id'])),
		$this->Wrap->publicState($upload['Upload']['public']),
//		$this->Wrap->niceTime($upload['Upload']['modified']),
		$this->Wrap->niceTime($upload['Upload']['created']),
		array(
			$this->Html->link(__('Compare'), array('controller' => 'vectors', 'action' => 'compare_upload_dump', $upload['Upload']['id'], $this->params['pass'][0])).
			$this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id'])).
			$this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Files'),
	'th' => $th,
	'td' => $td,
	));
?>
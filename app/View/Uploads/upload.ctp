<?php 
// File: app/View/Uploads/upload.ctp

// content
$th = array(
	'Upload.type' => array('content' => __('Type'), 'options' => array('sort' => 'Upload.type')),
	'Upload.filename' => array('content' => __('Name'), 'options' => array('sort' => 'Upload.filename')),
	'Upload.mysource' => array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'Upload.public' => array('content' => __('Share State'), 'options' => array('sort' => 'Report.public')),
//	'Upload.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified')),
	'Upload.created' => array('content' => __('Created'), 'options' => array('sort' => 'Upload.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploads as $i => $upload)
{
	$td[$i] = array(
		$this->Wrap->fileIcon($upload['Upload']['type']),
		$this->Html->link($upload['Upload']['filename'], array('action' => 'view', $upload['Upload']['id'])),
		$upload['Upload']['mysource'],
		$this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])),
		$this->Wrap->publicState($upload['Upload']['public']),
//		$this->Wrap->niceTime($upload['Upload']['modified']),
		$this->Wrap->niceTime($upload['Upload']['created']),
		array(
			$this->Html->link(__('Compare'), array('action' => 'compare', $this->params['pass'][0], $upload['Upload']['id'])).
			$this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id'])).
			$this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related Files'),
	'search_placeholder' => __('files'),
	'th' => $th,
	'td' => $td,
	));
?>
<?php 
// File: app/View/Uploads/admin_upload.ctp

// content
$th = array(
	'Upload.type' => array('content' => __('Type'), 'options' => array('sort' => 'Upload.type')),
	'Upload.filename' => array('content' => __('Name'), 'options' => array('sort' => 'Upload.filename')),
	'Upload.mysource' => array('content' => __('User Source'), 'options' => array('sort' => 'Upload.mysource')),
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'Upload.public' => array('content' => __('Share State'), 'options' => array('sort' => 'Upload.public')),
//	'Upload.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Upload.modified')),
	'Upload.created' => array('content' => __('Created'), 'options' => array('sort' => 'Upload.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($uploads as $i => $upload)
{
	$org_group = $this->Html->link(__('None'), array('controller' => 'org_groups', 'action' => 'view', '0'));
	if(isset($upload['OrgGroup']['id']) and $upload['OrgGroup']['id'])
	{
		$org_group = $this->Html->link($upload['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $upload['OrgGroup']['id']));
	}
	$td[$i] = array(
		$this->Wrap->fileIcon($upload['Upload']['type']),
		$this->Html->link($upload['Upload']['filename'], array('action' => 'view', $upload['Upload']['id'])),
		$upload['Upload']['mysource'],
		$org_group,
		$this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])),
		$this->Wrap->publicState($upload['Upload']['public']),
/*		array(
			$this->Form->postLink($this->Wrap->publicState($upload['Upload']['public']),array('action' => 'toggle', 'public', $upload['Upload']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
*/
//		$this->Wrap->niceTime($upload['Upload']['modified']),
		$this->Wrap->niceTime($upload['Upload']['created']),
		array(
			$this->Html->link(__('Compare'), array('action' => 'compare', $this->params['pass'][0], $upload['Upload']['id'])).
			$this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id'])).
			$this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id'])),
//			$this->Form->postLink(__('Delete'),array('action' => 'delete', $upload['Upload']['id']),array('confirm' => 'Are you sure?')),
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
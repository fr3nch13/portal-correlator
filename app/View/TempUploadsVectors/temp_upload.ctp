<?php 
// File: app/View/TempUploadsVectors/temp_upload.ctp


$page_options = array();
$page_options[] = $this->Html->link(__('Add Vectors'), array('action' => 'add', $this->params['pass'][0]));
$page_options[] = $this->Html->link(__('Assign All Vectors to One Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));

// content
$th = array(
	'TempVector.temp_vector' => array('content' => __('Vector'), 'options' => array('sort' => 'TempVector.temp_vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'TempVector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'TempVector.type')),
//	'TempUploadsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'TempUploadsVector.active')),
	//	'TempVector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'TempVector.modified')),
	'TempUploadsVector.created' => array('content' => __('Added'), 'options' => array('sort' => 'TempUploadsVector.created')),
	'TempVector.created' => array('content' => __('Created'), 'options' => array('sort' => 'TempVector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($temp_uploads_vectors as $i => $temp_uploads_vector)
{
//	$active = $this->Wrap->yesNo($temp_uploads_vector['TempUploadsVector']['active']);
	
	$actions = '';
//	$actions = $this->Html->link(__('View'), array('controller' => 'temp_vectors', 'action' => 'view', $temp_uploads_vector['TempVector']['id']));

/*	
	$active = array(
		$this->Form->postLink($active,array('action' => 'toggle', 'active', $temp_uploads_vector['TempUploadsVector']['id']),array('confirm' => 'Are you sure?')), 
		array('class' => 'actions'),
	);
*/
	$actions .= $this->Html->link(__('Remove'), array('action' => 'delete', $temp_uploads_vector['TempUploadsVector']['id']),array('confirm' => 'Are you sure?'));
	
	$td[$i] = array(
//		$this->Html->link($temp_uploads_vector['TempVector']['temp_vector'], array('controller' => 'temp_vectors', 'action' => 'view', $temp_uploads_vector['TempVector']['id'])),
		$temp_uploads_vector['TempVector']['temp_vector'],
		$this->Html->link($temp_uploads_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $temp_uploads_vector['VectorType']['id'])),
		$this->Wrap->niceWord($temp_uploads_vector['TempVector']['type']),
//		$active,
//		$this->Wrap->niceTime($temp_uploads_vector['TempVector']['modified']),
		$this->Wrap->niceTime($temp_uploads_vector['TempUploadsVector']['created']),
		$this->Wrap->niceTime($temp_uploads_vector['TempVector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $temp_uploads_vector['TempUploadsVector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('File Vectors'),
	'page_options' => $page_options,
	'search_placeholder' => __('Vectors'),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign Group'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'temp_uploads',
		'action' => 'view',
		$this->params['pass'][0],
	),
	));
?>
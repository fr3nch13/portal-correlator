<?php 
// File: app/View/Uploads/view.ctp

$details_left = array();
$details_left[] = array('name' => __('Owner'), 'value' => $this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])));
$details_left[] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($upload['Upload']['public']));
if($upload['UploadType']['org_group_id'] == AuthComponent::user('org_group_id'))
{
	$details_left[] = array('name' => __('File Group'), 'value' => $this->Html->link($upload['UploadType']['name'], array('admin' => false, 'controller' => 'upload_types', 'action' => 'view', $upload['UploadType']['id'])). '&nbsp;');
}
else
{
	$details_left[] = array('name' => __('File Group'), 'value' => $upload['UploadType']['name']. '&nbsp;');
}
$details_left[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($upload['Upload']['created']));
$details_left[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($upload['Upload']['modified']));

$details_right = array();
$details_right[] = array('name' => __('Owner'), 'value' => $this->Html->link($dump['User']['name'], array('controller' => 'users', 'action' => 'view', $dump['User']['id'])));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($dump['Dump']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($dump['Dump']['modified']));


$th = array(
	'Vector.vector' => array('content' => __('Vector')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

// track all vectors together
$all_vectors = array();
$unique_together = array();

// similar vectors
$similar_vectors = '';
$td = array();
foreach($comparisons['similar_vectors'] as $vector_id => $vector)
{
	$all_vectors[$vector] = $td[] = array(
		$this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id)),
		array(
			$this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector_id)),
			array('class' => 'actions'),
		),
	);
}

$similar_vectors = $this->element('Utilities.page_index', array(
	'page_title' => __('Similar Vectors'),
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
));

// unique for left upload
$unique_upload = '';
$td = array();
foreach($comparisons['upload']['unique_vectors'] as $vector_id => $vector)
{
	$unique_together[$vector] = $all_vectors[$vector] = $td[] = array(
		$this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id)),
		array(
			$this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector_id)),
			array('class' => 'actions'),
		),
	);
}

$unique_upload = $this->element('Utilities.page_index', array(
	'page_title' => __('Unique Vectors for File'),
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
));

$unique_dump = '';
$td = array();
foreach($comparisons['dump']['unique_vectors'] as $vector_id => $vector)
{
	$unique_together[$vector] = $all_vectors[$vector] = $td[] = array(
		$this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id)),
		array(
			$this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector_id)),
			array('class' => 'actions'),
		),
	);
}
$unique_dump = $this->element('Utilities.page_index', array(
	'page_title' => __('Unique Vectors for Dump'),
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
));

sort($all_vectors);
$all_vectors_count = count($all_vectors);
$all_vectors_html = $this->element('Utilities.page_index', array(
	'page_title' => __('All Combined Vectors'),
	'th' => $th,
	'td' => $all_vectors,
	'use_search' => false,
	'use_pagination' => false,
));

sort($unique_together);
$unique_together_count = count($unique_together);
$unique_together_html = $this->element('Utilities.page_index', array(
	'page_title' => __('Combined Unique Vectors'),
	'th' => $th,
	'td' => $unique_together,
	'use_search' => false,
	'use_pagination' => false,
));

$stats = array(
	array(
		'id' => 'similarPercent',
		'name' => __('Similar Vectors %'), 
		'value' => $comparisons['similar_percent']. '%', 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'similarSsdeepPercent',
		'name' => __('Similar Ssdeep %'), 
		'value' => $comparisons['ssdeep_percent']. '%', 
	),
	array(
		'id' => 'similarVectors',
		'name' => __('Similar'), 
		'value' => count($comparisons['similar_vectors']), 
		'tab' => array('tabs', 'similar'), // the tab to display
	),
	array(
		'id' => 'allVectors',
		'name' => __('All'), 
		'value' => $all_vectors_count, 
		'tab' => array('tabs', 'all'), // the tab to display
	),
	array(
		'id' => 'vectorsLeftUnique',
		'name' => __('Unique for File'), 
		'value' => count($comparisons['upload']['unique_vectors']), 
		'tab' => array('tabs', 'vectors_1'), // the tab to display
	),
	array(
		'id' => 'vectorsRightUnique',
		'name' => __('Unique for Dump'), 
		'value' => count($comparisons['dump']['unique_vectors']), 
		'tab' => array('tabs', 'vectors_2'), // the tab to display
	),
	array(
		'id' => 'UniqueTogether',
		'name' => __('Combined Unique Vectors'), 
		'value' => $unique_together_count, 
		'tab' => array('tabs', 'unique_together'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'similar',
		'title' => __('Similar Vectors'),
		'content' => $similar_vectors,
	),
	array(
		'key' => 'all',
		'title' => __('All Combined Vectors'),
		'content' => $all_vectors_html,
	),
	array(
		'key' => 'vectors_1',
		'title' => __('Unique Vectors for File'),
		'content' => $unique_upload,
	),
	array(
		'key' => 'vectors_2',
		'title' => __('Unique Vectors for Dump'),
		'content' => $unique_dump,
	),
	array(
		'key' => 'unique_together',
		'title' => __('Combined Unique Vectors'),
		'content' => $unique_together_html,
	),
);


echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Compare a File and a Dump'),
	//'page_options' => $page_options,
	'details_left_title' => __('File'). ': '. $this->Html->link($upload['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $upload['Upload']['id'])),
	'details_left' => $details_left,
	'details_right_title' => __('Dump'). ': '. $this->Html->link($dump['Dump']['name'], array('controller' => 'dumps', 'action' => 'view', $dump['Dump']['id'])),
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>

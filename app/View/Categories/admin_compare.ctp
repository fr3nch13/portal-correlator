<?php 
// File: app/View/Categories/admin_compare.ctp

$details_left = array();
$org_group_1 = $this->Html->link(__('None'), array('controller' => 'org_groups', 'action' => 'view', '0'));
if(isset($category_1['OrgGroup']['id']) and $category_1['OrgGroup']['id'])
{
	$org_group_1 = $this->Html->link($category_1['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $category_1['OrgGroup']['id']));
}
$details_left[] = array('name' => __('Org Group'), 'value' => $org_group_1);
$details_left[] = array('name' => __('Owner'), 'value' => $this->Html->link($category_1['User']['name'], array('controller' => 'users', 'action' => 'view', $category_1['User']['id'])));
$details_left[] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($category_1['Category']['public']));
$details_left[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($category_1['Category']['created']));
$details_left[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($category_1['Category']['modified']));

$details_right = array();
$org_group_2 = $this->Html->link(__('None'), array('controller' => 'org_groups', 'action' => 'view', '0'));
if(isset($category_2['OrgGroup']['id']) and $category_2['OrgGroup']['id'])
{
	$org_group_2 = $this->Html->link($category_2['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $category_2['OrgGroup']['id']));
}
$details_right[] = array('name' => __('Org Group'), 'value' => $org_group_2);
$details_right[] = array('name' => __('Owner'), 'value' => $this->Html->link($category_2['User']['name'], array('controller' => 'users', 'action' => 'view', $category_2['User']['id'])));
$details_right[] = array('name' => __('Share State'), 'value' => $this->Wrap->publicState($category_2['Category']['public']));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($category_2['Category']['created']));
$details_right[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($category_2['Category']['modified']));


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

// unique for left category
$unique_left = '';
$td = array();
foreach($comparisons['category_1']['unique_vectors'] as $vector_id => $vector)
{
	$unique_together[$vector] = $all_vectors[$vector] = $td[] = array(
		$this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id)),
		array(
			$this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector_id)),
			array('class' => 'actions'),
		),
	);
}

$unique_left = $this->element('Utilities.page_index', array(
	'page_title' => __('Unique Vectors for Left'),
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
));

$unique_right = '';
$td = array();
foreach($comparisons['category_2']['unique_vectors'] as $vector_id => $vector)
{
	$unique_together[$vector] = $all_vectors[$vector] = $td[] = array(
		$this->Html->link($vector, array('controller' => 'vectors', 'action' => 'view', $vector_id)),
		array(
			$this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $vector_id)),
			array('class' => 'actions'),
		),
	);
}

$unique_right = $this->element('Utilities.page_index', array(
	'page_title' => __('Unique Vectors for Right'),
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
		'name' => __('Similar Vectors'), 
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
		'name' => __('Unique Vectors Left'), 
		'value' => count($comparisons['category_1']['unique_vectors']), 
		'tab' => array('tabs', 'vectors_1'), // the tab to display
	),
	array(
		'id' => 'vectorsRightUnique',
		'name' => __('Unique Vectors Right'), 
		'value' => count($comparisons['category_2']['unique_vectors']), 
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
		'title' => __('Unique Vectors for Left'),
		'content' => $unique_left,
	),
	array(
		'key' => 'vectors_2',
		'title' => __('Unique Vectors for Right'),
		'content' => $unique_right,
	),
	array(
		'key' => 'unique_together',
		'title' => __('Combined Unique Vectors'),
		'content' => $unique_together_html,
	),
);


echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Compare Categories'),
	//'page_options' => $page_options,
	'details_left_title' => $this->Html->link($category_1['Category']['name'], array('action' => 'view', $category_1['Category']['id'])). ' ('.__('Left').')',
	'details_left' => $details_left,
	'details_right_title' => $this->Html->link($category_2['Category']['name'], array('action' => 'view', $category_2['Category']['id'])). ' ('.__('Right').')',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>

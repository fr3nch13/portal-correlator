<?php 
// File: app/View/OrgGroups/index.ctp


$page_options = array(
);

// content
$th = array(
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();

foreach ($org_groups as $i => $org_group)
{
	$td[$i] = array(
		$this->Html->link($org_group['OrgGroup']['name'], array('action' => 'view', $org_group['OrgGroup']['id'])),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $org_group['OrgGroup']['id'])), 
			array('class' => 'actions'),
		),
	);
}

// add the global one at the top
$global = array(
	$this->Html->link(__('Global'), array('action' => 'view', 0)),
	array(
		$this->Html->link(__('View'), array('action' => 'view', 0)), 
		array('class' => 'actions'),
	),
);
array_unshift($td, $global);

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Org Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>
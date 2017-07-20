<?php 
// File: app/View/CategoryTypes/admin_view.ctp
$details = array();

$details[] = array('name' => __('Default Holder'), 'value' => $this->Wrap->yesNo($categoryType['CategoryType']['holder']));
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($categoryType['CategoryType']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($categoryType['CategoryType']['modified']));


$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $categoryType['CategoryType']['id']));
$page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $categoryType['CategoryType']['id']),array('confirm' => 'Are you sure?'));

$stats = array();
$tabs = array();

//
$tabs[] = array(
	'key' => 'categories',
	'title' => __('Categories'),
	'url' => array('controller' => 'categories', 'action' => 'category_type', $categoryType['CategoryType']['id'], 'manager' => false),
);
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($categoryType['CategoryType']['desc']),
);

$stats[] = array(
	'id' => 'categories',
	'name' => __('Categories'), 
	'value' => $categoryType['CategoryType']['counts']['Category.public'], 
	'tab' => array('tabs', '1'), // the tab to display
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Category Group'). ': '. $categoryType['CategoryType']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
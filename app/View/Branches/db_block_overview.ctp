<?php

$stats = array(
	'total' => array('name' => __('Branches'), 'value' => count($branches)),
);

$orgs = array();
$shortnames = array();
$directors = array();
foreach($branches as $i => $branch)
{
	$org = $branch['Branch']['org'];
	$shortname = $branch['Branch']['shortname'];
	$director = trim(strtolower($branch['Branch']['email']));
	
	if(!isset($orgs[$org]))
		$orgs[$org] = 0;
	$orgs[$org]++;
	
	if(!isset($shortnames[$shortname]))
		$shortnames[$shortname] = 0;
	$shortnames[$shortname]++;
	
	if(!isset($directors[$director]))
		$directors[$director] = 0;
	$directors[$director]++;
}

$stats['shortnames'] = array('name' => __('Branches'), 'value' => count($shortnames));
$stats['orgs'] = array('name' => __('Orgs/ICs'), 'value' => count($orgs));
$stats['directors'] = array('name' => __('Directors'), 'value' => count($directors));

$content = $this->element('Utilities.object_dashboard_stats', array(
	'title' => false,
	'details' => $stats,
));
echo $this->element('Utilities.object_dashboard_block', array(
	'title' => $this->Html->link(__('%s - Overview', __('Branches')), array('action' => 'dashboard')),
	'content' => $content,
));
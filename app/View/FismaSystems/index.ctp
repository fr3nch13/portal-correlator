<?php 

$_th = (isset($th)?$th:[]);
$_td = (isset($td)?$td:[]);

$th = [];
$th['Report.count'] = ['content' => __('# %s', __('Reports'))];
$th['Category.count'] = ['content' => __('# %s', __('Categories'))];

$th = array_merge($th, $_th);

$td = $_td;

foreach ($records as $i => $record)
{
	$td[$i] = [];
	
	$td[$i]['Report.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'reports', 'action' => 'fisma_system', $record['FismaSystem']['id']],
		'url' => ['action' => 'view', $record['FismaSystem']['id'], 'tab' => 'reports'],
	]];
	
	$td[$i]['Category.count'] = ['.', [
		'ajax_count_url' => ['controller' => 'categories', 'action' => 'fisma_system', $record['FismaSystem']['id']],
		'url' => ['action' => 'view', $record['FismaSystem']['id'], 'tab' => 'categories'],
	]];
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
}

$this->set(compact(['th', 'td']));

$this->extend('Contacts.ContactsFismaSystems/index');
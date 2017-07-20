<?php 
$sep = "\n--------------------\n";

if(isset($report))
{
	echo "\n\n";
	echo __('Report');
	echo $sep;
	echo __('Name: %s', $report['Report']['name']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'reports', 'action' => 'view', $report['Report']['id']), true)). "\n";
}

if(isset($editor))
{
	echo "\n\n";
	echo __('Edited By');
	echo $sep;
	echo __('Name: %s', $editor['name']). "\n";
//	echo __('Email: %s', $editor['email']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'users', 'action' => 'view', $editor['id']), true)). "\n";
}
?>
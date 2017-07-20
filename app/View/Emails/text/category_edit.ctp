<?php 
$sep = "\n--------------------\n";

if(isset($category))
{
	echo "\n\n";
	echo __('Category');
	echo $sep;
	echo __('Name: %s', $category['Category']['name']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'categories', 'action' => 'view', $category['Category']['id']), true)). "\n";
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
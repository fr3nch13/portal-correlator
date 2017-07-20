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

if(isset($report))
{
	echo "\n\n";
	echo __('Report');
	echo $sep;
	echo __('Name: %s', $report['Report']['name']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'reports', 'action' => 'view', $report['Report']['id']), true)). "\n";
}

if(isset($temp_upload))
{
	echo "\n\n";
	echo __('File Details');
	echo $sep;
	echo __('File Name: %s', $temp_upload['TempUpload']['filename']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'temp_uploads', 'action' => 'view', $temp_upload['TempUpload']['id']), true)). "\n";
}

if(isset($editor))
{
	echo "\n\n";
	echo __('Added By');
	echo $sep;
	echo __('Name: %s', $editor['name']). "\n";
//	echo __('Email: %s', $editor['email']). "\n";
	echo __('URL: %s', Router::url(array('controller' => 'users', 'action' => 'view', $editor['id']), true)). "\n";
}
?>
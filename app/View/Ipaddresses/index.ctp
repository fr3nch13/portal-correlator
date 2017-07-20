<?php 

$th = [];
$th['Vector.vector'] = ['content' => __('Vector'), 'options' => ['sort' => 'Vector.vector']];
$th['Vector.type'] = ['content' => __('Vector Type'), 'options' => ['sort' => 'Vector.type']];
$th['Vector.created'] = ['content' => __('Created'), 'options' => ['sort' => 'Vector.created']];
$th['actions'] = ['content' => __('Actions'), 'options' => ['class' => 'actions']];

$td = [];
foreach ($ipaddresses as $i => $ipaddress)
{
	$td[$i] = [];
	$td[$i]['Vector.vector'] = $this->Html->link($ipaddress['Vector']['vector'], ['controller' => 'vectors', 'action' => 'view', $ipaddress['Vector']['id']]);
	$td[$i]['Vector.type'] = $this->Html->link($this->Wrap->niceWord($ipaddress['Vector']['type']), ['controller' => 'vectors', 'action' => 'type', $ipaddress['Vector']['type']]);
	$td[$i]['Vector.created'] = $this->Wrap->niceTime($ipaddress['Vector']['created']);
	$td[$i]['actions'] = [
		$this->Html->link(__('View'), ['controller' => 'vectors', 'action' => 'view', $ipaddress['Vector']['id']]),
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('IP Addresses'),
	'th' => $th,
	'td' => $td,
]);
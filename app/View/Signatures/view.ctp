<?php 

$page_options = [];

$active = $this->Wrap->yesNo($signature['Signature']['active']);

if($this->Common->roleCheck(['admin']))
{
	$page_options[] = $this->Html->link(__('Edit'), ['action' => 'edit', $signature['Signature']['id'], 'admin' => true]);
	$page_options[] = $this->Html->confirmLink(__('Delete'), ['action' => 'delete', $signature['Signature']['id'], 'admin' => true]);
	
	$active = $this->Html->confirmLink($active, ['action' => 'toggle', 'active', $signature['Signature']['id']]);
}

$details = [];
$details[] = ['name' => __('Name'), 'value' => $signature['Signature']['name']];
$details[] = ['name' => __('Type'), 'value' => $this->Html->link($this->Wrap->getSigTypeMap($signature['Signature']['signature_type']), ['action' => 'type', $signature['Signature']['signature_type']])];
$details[] = ['name' => __('Source'), 'value' => $this->Html->link($signature['SignatureSource']['name'], ['controller' => 'signature_sources', 'action' => 'view', $signature['SignatureSource']['id']])];
// $details[] = ['name' => __('Hash'), 'value' => $signature['Signature']['sig_hash']];
$details[] = ['name' => __('Active'), 'value' => $active];
$details[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($signature['Signature']['created'])];
$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($signature['Signature']['modified'])];


$stats = $tabs = [];

$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Related %s', __('Reports')),
	'ajax_url' => ['controller' => 'reports', 'action' => 'signature', $signature['Signature']['id']],
];
$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Related %s', __('Categories')),
	'ajax_url' => ['controller' => 'categories', 'action' => 'signature', $signature['Signature']['id']],
];
$tabs['signature'] = [
	'id' => 'signature',
	'name' => __('Original Signature'),
	'content' => $this->Wrap->descView($signature['Signature']['signature']),
];
if($signature['Signature']['signature_type'] == 'yara' and isset($signature['YaraSignature']['id']))
{
	$tabs['compiled'] = [
		'id' => 'compiled',
		'name' => __('Compiled Signature'),
		'ajax_url' => ['controller' => 'yara_signatures', 'action' => 'compiled', $signature['YaraSignature']['id']],
	];
}
elseif($signature['Signature']['signature_type'] == 'snort' and isset($signature['SnortSignature']['id']))
{
	$tabs['compiled'] = [
		'id' => 'compiled',
		'name' => __('Compiled Signature'),
		'ajax_url' => ['controller' => 'snort_signatures', 'action' => 'compiled', $signature['YaraSignature']['id']],
	];
}
$tabs['description'] = [
	'id' => 'description',
	'name' => __('Description'),
	'content' => $this->Wrap->descView($signature['Signature']['desc']),
];
$tabs['tags'] = $stats['tags'] = [
	'id' => 'tags',
	'name' => __('Tags'),
	'ajax_url' => ['plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'signature', $signature['Signature']['id']],
];

echo $this->element('Utilities.page_view', [
	'page_title' => __('%s: %s', __('Signature'), $signature['Signature']['name']),
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
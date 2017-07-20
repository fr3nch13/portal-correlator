<?php 
// File: app/View/CategoriesSignatures/category.ctp


$page_options = array();
if($category['Category']['user_id'] == AuthComponent::user('id'))
{
	$page_options[] = $this->Html->link(__('Add %s', __('Signatures')), array('controller' => 'signatures', 'action' => 'add', 'category_id' => $this->params['pass'][0]));
}

// content
$th = array(
	'Signature.name' => array('content' => __('Signature'), 'options' => array('sort' => 'Signature.name')),
	'Signature.signature_type' => array('content' => __('Type'), 'options' => array('sort' => 'Signature.signature_type')),
	'SignatureSource.name' => array('content' => __('Source'), 'options' => array('sort' => 'SignatureSource.name')),
	'CategoriesSignature.active' => array('content' => __('Active'), 'options' => array('sort' => 'CategoriesSignature.active')),
	'CategoriesSignature.created' => array('content' => __('Added to %s', __('Category')), 'options' => array('sort' => 'CategoriesSignature.created')),
	'Signature.created' => array('content' => __('Created'), 'options' => array('sort' => 'Signature.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($categories_signatures as $i => $categories_signature)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'signatures', 'action' => 'view', $categories_signature['Signature']['id']));
	
	$active = $this->Wrap->yesNo($categories_signature['CategoriesSignature']['active']);
	
	if($categories_signature['Category']['user_id'] == AuthComponent::user('id'))
	{
		$active = array(
			$this->Html->link($active, array('action' => 'toggle', 'active', $categories_signature['CategoriesSignature']['id'], 'hash' => 'ui-tabs-12'),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		if($categories_signature['Category']['user_id'] == AuthComponent::user('id'))
			$actions .= $this->Html->link(__('Remove'),array('action' => 'delete', $categories_signature['CategoriesSignature']['id'], 'hash' => 'ui-tabs-12'),array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array(
		$this->Html->link($categories_signature['Signature']['name'], array('controller' => 'signatures', 'action' => 'view', $categories_signature['Signature']['id'])),
		//$this->Html->link($this->Wrap->getSigTypeMap($signature['Signature']['signature_type']), array('controller' => 'signatures', 'action' => 'type', $signature['Signature']['signature_type'])),
		$this->Wrap->getSigTypeMap($categories_signature['Signature']['signature_type']),
		$this->Html->link($categories_signature['SignatureSource']['name'], array('controller' => 'signature_sources', 'action' => 'view', $categories_signature['SignatureSource']['id'])),
		$active,
		$this->Wrap->niceTime($categories_signature['CategoriesSignature']['created']),
		$this->Wrap->niceTime($categories_signature['Signature']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $categories_signature['CategoriesSignature']['id'],
	);
}

$use_multiselect = false;
if($category['Category']['user_id'] == AuthComponent::user('id'))
{
	$use_multiselect = false;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('Category'), __('Signatures')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'inactive' => __('Mark Inactive'),
		'active' => __('Mark Active'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'categories',
		'action' => 'view',
		$this->params['pass'][0],
		'hash' => 'ui-tabs-3',
	),
));
?>
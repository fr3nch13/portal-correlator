<?php 
// File: app/View/CategoriesVectors/category.ctp


$page_options = array();
$page_options2 = array();
$use_multiselect = false;
if($category['Category']['user_id'] == AuthComponent::user('id') or $is_editor)
{
	$page_options[] = $this->Html->link(__('Add Vectors'), array('action' => 'add', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to One Group'), array('action' => 'assign_vector_type', $this->params['pass'][0]));
	$page_options[] = $this->Html->link(__('Assign All Vectors to MANY Groups'), array('action' => 'assign_vector_multitypes', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign DNS Tracking to All Vectors'), array('action' => 'assign_dnstracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign Hexillion Tracking to All Vectors'), array('action' => 'assign_hexilliontracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign WHOIS Tracking to All Vectors'), array('action' => 'assign_whoistracking', $this->params['pass'][0]));
	$page_options2[] = $this->Html->link(__('Assign VT Tracking to All Vectors'), array('action' => 'assign_vttracking', $this->params['pass'][0]));
	$use_multiselect = true;
}
elseif($is_contributor)
{
	$page_options[] = $this->Html->link(__('Add Vectors'), array('action' => 'add', $this->params['pass'][0]));
}

// content
$th = array(
	'CategoriesVector.id' => array('content' => __('Xref ID'), 'options' => array('sort' => 'CategoriesVector.id')),
	'Vector.id' => array('content' => __('Vector ID'), 'options' => array('sort' => 'Vector.id')),
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'hexillion_auto_lookup' => array('content' => __('Hexillion Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'CategoriesVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'CategoriesVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'CategoriesVector.created' => array('content' => __('Added to Category'), 'options' => array('sort' => 'CategoriesVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($categories_vectors as $i => $categories_vector)
{
	$actions = $this->Html->link(__('View'), array('controller' => 'vectors', 'action' => 'view', $categories_vector['Vector']['id']));
	
	$active = $this->Wrap->yesNo($categories_vector['CategoriesVector']['active']);
	
	if($categories_vector['Category']['user_id'] == AuthComponent::user('id') or $is_editor)
	{
		$active = array(
			$this->Html->link($active,array('action' => 'toggle', 'active', $categories_vector['CategoriesVector']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		if($categories_vector['Category']['user_id'] == AuthComponent::user('id'))
			$actions .= $this->Html->link(__('Remove'),array('action' => 'delete', $categories_vector['CategoriesVector']['id']),array('confirm' => 'Are you sure?'));
	}
	
	$dns_auto_lookup = '&nbsp;';
	$hexillion_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($categories_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Ipaddress']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Ipaddress']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($categories_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($categories_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Hostname']['dns_auto_lookup'], true);
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Hostname']['hexillion_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($categories_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$categories_vector['CategoriesVector']['id'],
		$categories_vector['Vector']['id'],
		$this->Html->link($categories_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $categories_vector['Vector']['id'])),
		$this->Html->link($categories_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $categories_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($categories_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $categories_vector['Vector']['type'])),
		$dns_auto_lookup,
		$hexillion_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($categories_vector['VectorDetail']['vt_lookup'], true),
		$active,
		$categories_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($categories_vector['CategoriesVector']['created']),
		$this->Wrap->niceTime($categories_vector['Vector']['created']),
		array(
			$actions,
			array('class' => 'actions'),
		),
		'multiselect' => $categories_vector['CategoriesVector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', __('Category'), __('Vectors')),
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign One Group'),
		'inactive' => __('Mark Inactive'),
		'active' => __('Mark Active'),
		'vttracking' => __('Mark for VirusTotal Tracking'),
		'dnstracking' => __('Modify DNS Tracking - All'),
		'multidnstracking' => __('Modify DNS Tracking - Invidual'),
		'hexilliontracking' => __('Modify Hexillion Tracking - All'),
		'multihexilliontracking' => __('Modify Hexillion Tracking - Invidual'),
		'whoistracking' => __('Modify WHOIS Tracking - All'),
		'multiwhoistracking' => __('Modify WHOIS Tracking - Invidual'),
		'vectortype' => __('Change Type - All'),
		'multivectortype' => __('Change Type - Invidual'),
		'delete' => __('Remove'),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'categories',
		'action' => 'view',
		$this->params['pass'][0],
	)
));
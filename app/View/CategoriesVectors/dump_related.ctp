<?php 
// File: app/View/CategoriesVectors/dump_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'Category.name' => array('content' => __('Category'), 'options' => array('sort' => 'Category.name')),
//	'CategoriesVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'CategoriesVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'CategoriesVector.created' => array('content' => __('Added to Category'), 'options' => array('sort' => 'CategoriesVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'multiselect' => true,
);

$td = array();
foreach ($categories_vectors as $i => $categories_vector)
{
	$dns_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($categories_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($categories_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($categories_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($categories_vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($categories_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($categories_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $categories_vector['Vector']['id'])),
		$this->Html->link($categories_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $categories_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($categories_vector['Vector']['type']), array('controller' => 'vectors', 'action' => 'type', $categories_vector['Vector']['type'])),
		$dns_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($categories_vector['VectorDetail']['vt_lookup'], true),
		$this->Html->link($categories_vector['Category']['name'], array('controller' => 'categories', 'action' => 'view', $categories_vector['Category']['id'])),
//		$this->Wrap->yesNo($categories_vector['CategoriesVector']['active']),
		$categories_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($categories_vector['CategoriesVector']['created']),
		$this->Wrap->niceTime($categories_vector['Vector']['created']),
	);
}


echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', __('Category'), __('Vectors')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
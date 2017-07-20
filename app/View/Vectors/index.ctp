<?php 

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
//	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
//	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
//	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'VectorSourceFirst.source_type' => array('content' => __('First Vector Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type')),
	'VectorSourceFirst.created' => array('content' => __('First Vector Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created')),
	'VectorSourceLast.source_type' => array('content' => __('Last Vector Source'), 'options' => array('sort' => 'VectorSourceLast.source_type')),
	'VectorSourceLast.created' => array('content' => __('Last Vector Source Added'), 'options' => array('sort' => 'VectorSourceLast.created')),
//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($vectors as $i => $vector)
{
/*
	$dns_auto_lookup = '';
	$whois_auto_lookup = '';
//	$geoip = '';
	if(isset($vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup'], true);
//		$geoip = $vector['Geoip']['country_iso'];
	}
	if(isset($vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup'], true);
	}
*/
	
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type'])),
//		$dns_auto_lookup,
//		$whois_auto_lookup,
//		$geoip,
		$this->Html->link($vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $vector['VectorType']['id'])),
		$this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceFirst']['created']),
		$this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceLast']['created']),
//		$this->Wrap->niceTime($vector['Vector']['modified']),
		$this->Wrap->niceTime($vector['Vector']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])),
			array('class' => 'actions'),
		),
		'multiselect' => $vector['Vector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Vectors'),
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => true,
	'multiselect_options' => array(
		'multitype' => __('Assign Many Groups'),
		'type' => __('Assign Group'),
		'dnstracking' => __('Modify DNS Tracking - All'),
		'multidnstracking' => __('Modify DNS Tracking - Invidual'),
		'whoistracking' => __('Modify WHOIS Tracking - All'),
		'multiwhoistracking' => __('Modify WHOIS Tracking - Invidual'),
		'vectortype' => __('Change Type - All'),
		'multivectortype' => __('Change Type - Invidual'),
	),
	'multiselect_referer' => array(
		'admin' => false,
		'controller' => 'vectors',
		'action' => 'index',
//		$this->params['pass'][0],
	),
));
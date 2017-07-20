<?php 
// File: app/View/Vectors/ipaddresses.ctp

$page_options = array(
	$this->Html->link(__('Show All'), array(0)),
	$this->Html->link(__('Show Only Local'), array('local')),
	$this->Html->link(__('Show Only Remote'), array('remote')),
);

// content
$th = array(
	'Vector.vector' => array('content' => __('Ip Address'), 'options' => array('sort' => 'Vector.vector')),
	'Vector.type' => array('content' => __('Type'), 'options' => array('sort' => 'Vector.type')),
	'Ipaddress.dns_auto_lookup' => array('content' => __('DNS Tracking'), 'options' => array('sort' => 'Ipaddress.dns_auto_lookup')),
	'Ipaddress.whois_auto_lookup' => array('content' => __('WHOIS Tracking'), 'options' => array('sort' => 'Ipaddress.whois_auto_lookup')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'VectorSourceFirst.source_type' => array('content' => __('First Vector Source'), 'options' => array('sort' => 'VectorSourceFirst.source_type')),
	'VectorSourceFirst.created' => array('content' => __('First Vector Source Added'), 'options' => array('sort' => 'VectorSourceFirst.created')),
	'VectorSourceLast.source_type' => array('content' => __('Last Vector Source'), 'options' => array('sort' => 'VectorSourceLast.source_type')),
	'VectorSourceLast.created' => array('content' => __('Last Vector Source Added'), 'options' => array('sort' => 'VectorSourceLast.created')),
//	'Vector.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Vector.modified')),
	'Ipaddress.dns_checked' => array('content' => __('DNS Last Checked'), 'options' => array('sort' => 'Ipaddress.dns_checked')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
//	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$td[$i] = array(
		$this->Html->link($vector['Vector']['vector'], array('action' => 'view', $vector['Vector']['id'])),
		$this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type'])),
		$this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'], true),
		$this->Wrap->whoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup'], true),
		$this->Html->link($vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $vector['VectorType']['id'])),
		$this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceFirst']['created']),
		$this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
		$this->Wrap->niceTime($vector['VectorSourceLast']['created']),
//		$this->Wrap->niceTime($vector['Vector']['modified']),
		$this->Wrap->niceTime($vector['Ipaddress']['dns_checked']),
		$this->Wrap->niceTime($vector['Vector']['created']),
//		$vector['Geoip']['country_iso'],
		array(
			$this->Html->link(__('View'), array('action' => 'view', $vector['Vector']['id'])),
			array('class' => 'actions'),
		),
		'multiselect' => $vector['Vector']['id'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%sIp Addresses', $lookup_type),
	'search_placeholder' => __('%sIp Addresses', $lookup_type),
	'page_options' => $page_options,
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
		'action' => 'ipaddresses',
		$this->params['pass'][0],
	),
));
?>
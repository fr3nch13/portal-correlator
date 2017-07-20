<?php 
// File: app/View/UploadsVectors/import_related.ctp


$page_options = array();

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'VectorType.name' => array('content' => __('Vector Group'), 'options' => array('sort' => 'VectorType.name')),
	'Vector.type' => array('content' => __('Vector Type'), 'options' => array('sort' => 'Vector.type')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
	'whois_auto_lookup' => array('content' => __('WHOIS Tracking')),
	'VectorDetail.vt_lookup' => array('content' => __('VT Tracking')),
	'Upload.filename' => array('content' => __('File'), 'options' => array('sort' => 'Upload.filename')),
	'UploadsVector.active' => array('content' => __('Active'), 'options' => array('sort' => 'UploadsVector.active')),
	'Geoip.country_iso' => array('content' => __('Country'), 'options' => array('sort' => 'Geoip.country_iso')),
	'UploadsVector.created' => array('content' => __('Added to Upload'), 'options' => array('sort' => 'UploadsVector.created')),
	'Vector.created' => array('content' => __('Created'), 'options' => array('sort' => 'Vector.created')),
	'multiselect' => true,
);

$td = array();
foreach ($uploads_vectors as $i => $uploads_vector)
{
	
	$dns_auto_lookup = '&nbsp;';
	$whois_auto_lookup = '&nbsp;';
	if(isset($uploads_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Ipaddress']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($uploads_vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($uploads_vector['Hostname']['dns_auto_lookup'], true);
		$whois_auto_lookup = $this->Wrap->whoisAutoLookupLevel($uploads_vector['Hostname']['whois_auto_lookup'], true);
	}
	
	$td[$i] = array(
		$this->Html->link($uploads_vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $uploads_vector['Vector']['id'])),
		$this->Html->link($uploads_vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $uploads_vector['VectorType']['id'])),
		$this->Html->link($this->Wrap->niceWord($uploads_vector['Vector']['type']), array('admin' => false, 'controller' => 'vectors', 'action' => 'type', $uploads_vector['Vector']['type'])),
		$dns_auto_lookup,
		$whois_auto_lookup,
		$this->Wrap->vtAutoLookupLevel($uploads_vector['VectorDetail']['vt_lookup'], true),
		$this->Html->link($uploads_vector['Upload']['filename'], array('controller' => 'uploads', 'action' => 'view', $uploads_vector['Upload']['id'])),
		$this->Wrap->yesNo($uploads_vector['UploadsVector']['active']),
		$uploads_vector['Geoip']['country_iso'],
		$this->Wrap->niceTime($uploads_vector['UploadsVector']['created']),
		$this->Wrap->niceTime($uploads_vector['Vector']['created']),
	);
}


echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s %s', _('Upload'), _('Vectors')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
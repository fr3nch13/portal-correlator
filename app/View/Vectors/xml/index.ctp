<?php 
// File: app/View/Vectors/xml/index.ctp

$data = array();
foreach ($vectors as $i => $vector)
{
	$dns_auto_lookup_value = '';
	$dns_auto_lookup_name = '';
	$geoip = '';
	if(isset($vector['Ipaddress']['id']))
	{
		$dns_auto_lookup_value = $vector['Ipaddress']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'], true);
		$geoip = $vector['Geoip']['country_iso'];
	}
	if(isset($vector['Hostname']['id']))
	{
		$dns_auto_lookup_value = $vector['Hostname']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['dns_auto_lookup'], true);
	}
	
    $data[] = array(
    	'Vector.vector' => $vector['Vector']['vector'],
    	'Vector.id' => $vector['Vector']['id'],
    	'Vector.type' => $this->Wrap->niceWord($vector['Vector']['type']),
    	'Vector.uri' => $this->Html->url(array('action' => 'view', $vector['Vector']['id'])),
    	'dns_auto_lookup_value' => $dns_auto_lookup_value,
    	'dns_auto_lookup_name' => $dns_auto_lookup_name,
    	'Geoip.country_iso' => $geoip,
    	'VectorType.name' => $vector['VectorType']['name'],
    	'VectorType.id' => $vector['VectorType']['id'],
    	'VectorSourceFirst.source_type' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
    	'VectorSourceFirst.created' => $vector['VectorSourceFirst']['created'],
    	'VectorSourceLast.source_type' => $this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
    	'VectorSourceLast.created' => $vector['VectorSourceLast']['created'],
    	'Vector.created' => $vector['Vector']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
?>
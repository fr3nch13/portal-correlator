<?php 
// File: app/View/Vectors/json/auto_tracking_whois.ctp

$data = array();
foreach ($vectors as $i => $vector)
{
	$whois_auto_lookup_value = '';
	$whois_auto_lookup_name = '';
	if(isset($vector['Ipaddress']['id']))
	{
		$whois_auto_lookup_value = $vector['Ipaddress']['whois_auto_lookup'];
		$whois_auto_lookup_name = $this->Wrap->whoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup'], true);
	}
	if(isset($vector['Hostname']['id']))
	{
		$whois_auto_lookup_value = $vector['Hostname']['whois_auto_lookup'];
		$whois_auto_lookup_name = $this->Wrap->whoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup'], true);
	}
	
    $data[$vector['Vector']['id']] = array(
    	'Vector.vector' => $vector['Vector']['vector'],
    	'Vector.id' => $vector['Vector']['id'],
    	'Vector.type' => $this->Wrap->niceWord($vector['Vector']['type']),
    	'Vector.uri' => $this->Html->url(array('action' => 'view', $vector['Vector']['id'])),
    	'whois_auto_lookup_value' => $whois_auto_lookup_value,
    	'whois_auto_lookup_name' => $whois_auto_lookup_name,
    	'VectorType.name' => $vector['VectorType']['name'],
    	'VectorType.id' => $vector['VectorType']['id'],
    	'VectorSourceFirst.source_type' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
    	'VectorSourceFirst.created' => $vector['VectorSourceFirst']['created'],
    	'VectorSourceLast.source_type' => $this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
    	'VectorSourceLast.created' => $vector['VectorSourceLast']['created'],
    	'Vector.created' => $vector['Vector']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), 'json', 'Vector');
?>


<?php 
// File: app/View/Vectors/xml/ipaddresses.ctp

$data = array();
foreach ($vectors as $i => $vector)
{
    $data[] = array(
    	'@id' => $vector['Vector']['id'],
    	'Vector.vector' => $vector['Vector']['vector'],
    	'Vector.id' => $vector['Vector']['id'],
    	'Vector.uri' => $this->Html->url(array('action' => 'view', $vector['Vector']['id'])),
    	'Ipaddress.dns_auto_lookup_value' => $vector['Ipaddress']['dns_auto_lookup'],
    	'Ipaddress.dns_auto_lookup_name' => $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'], true),
    	'VectorSourceFirst.source_type' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type']),
    	'VectorSourceFirst.created' => $vector['VectorSourceFirst']['created'],
    	'VectorSourceLast.source_type' => $this->Wrap->niceWord($vector['VectorSourceLast']['source_type']),
    	'VectorSourceLast.created' => $vector['VectorSourceLast']['created'],
    	'Vector.created' => $vector['Vector']['created'],
    	
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), 'xml', 'Vector');
?>
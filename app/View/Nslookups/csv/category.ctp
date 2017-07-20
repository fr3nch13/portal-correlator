<?php 
// File: app/View/Nslookups/csv/category.ctp

$data = array();
foreach ($nslookups as $i => $nslookup)
{
    $data[] = array(
		'VectorHostname.name' => $nslookup['VectorHostname']['vector'],
		'VectorHostname.id' => $nslookup['VectorHostname']['id'],
		'VectorHostname.uri' => $this->Html->url(array('controller' => 'vectors', 'action' => 'view', $nslookup['VectorHostname']['id'])),
		'VectorIpaddress.name' => $nslookup['VectorIpaddress']['vector'],
		'VectorIpaddress.id' => $nslookup['VectorIpaddress']['id'],
		'VectorIpaddress.uri' => $this->Html->url(array('controller' => 'vectors', 'action' => 'view', $nslookup['VectorIpaddress']['id'])),
		'Dnsrecords.ttl' => $nslookup['Nslookup']['ttl'],
		'Dnsrecords.source' => $this->Wrap->sourceUser($nslookup['Nslookup']['source']),
		'Dnsrecords.first_seen' => $nslookup['Nslookup']['first_seen'],
		'Dnsrecords.last_seen' => $nslookup['Nslookup']['last_seen'],
		'Dnsrecords.created' => $nslookup['Nslookup']['created'],
		'Dnsrecords.modified' => $nslookup['Nslookup']['modified'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], 'Dnsrecord');
?>
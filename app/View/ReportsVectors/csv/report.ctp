<?php 
// File: app/View/ReportsVectors/csv/report.ctp

$data = array();
foreach ($reports_vectors as $i => $reports_vector)
{
	$dns_auto_lookup_value = '';
	$dns_auto_lookup_name = '';
	if(isset($reports_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup_value = $reports_vector['Ipaddress']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($reports_vector['Ipaddress']['dns_auto_lookup'], true);
	}
	if(isset($reports_vector['Hostname']['id']))
	{
		$dns_auto_lookup_value = $reports_vector['Hostname']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($reports_vector['Hostname']['dns_auto_lookup'], true);
	}
	
    $data[] = array(
		'Vector.vector' => $reports_vector['Vector']['vector'],
		'Vector.id' => $reports_vector['Vector']['id'],
		'Vector.type' => $this->Wrap->niceWord($reports_vector['Vector']['type']),
		'Vector.uri' => $this->Html->url(array('controller' => 'vectors', 'action' => 'view', $reports_vector['Vector']['id'])),
		'dns_auto_lookup_value' => $dns_auto_lookup_value,
		'dns_auto_lookup_name' => $dns_auto_lookup_name,
		'VectorType.name' => $reports_vector['VectorType']['name'],
		'VectorType.id' => $reports_vector['VectorType']['id'],
		'ReportsVector.active' => $reports_vector['ReportsVector']['active'],
		'ReportsVector.created' => $reports_vector['ReportsVector']['created'],
		'Vector.created' => $reports_vector['Vector']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
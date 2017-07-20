<?php 
// File: app/View/CategoriesVectors/xml/unique.ctp

$data = array();
foreach ($categories_vectors as $i => $categories_vector)
{
	$dns_auto_lookup_value = '';
	$dns_auto_lookup_name = '';
	if(isset($categories_vector['Ipaddress']['id']))
	{
		$dns_auto_lookup_value = $categories_vector['Ipaddress']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($categories_vector['Ipaddress']['dns_auto_lookup'], true);
	}
	if(isset($categories_vector['Hostname']['id']))
	{
		$dns_auto_lookup_value = $categories_vector['Hostname']['dns_auto_lookup'];
		$dns_auto_lookup_name = $this->Wrap->dnsAutoLookupLevel($categories_vector['Hostname']['dns_auto_lookup'], true);
	}
	
    $data[] = array(
		'Vector.vector' => $categories_vector['Vector']['vector'],
		'Vector.id' => $categories_vector['Vector']['id'],
		'Vector.type' => $this->Wrap->niceWord($categories_vector['Vector']['type']),
		'Vector.uri' => $this->Html->url(array('controller' => 'vectors', 'action' => 'view', $categories_vector['Vector']['id'])),
		'dns_auto_lookup_value' => $dns_auto_lookup_value,
		'dns_auto_lookup_name' => $dns_auto_lookup_name,
		'VectorType.name' => $categories_vector['VectorType']['name'],
		'VectorType.id' => $categories_vector['VectorType']['id'],
		'Category.name' => $categories_vector['Category']['name'],
		'Category.id' => $categories_vector['Category']['id'],
		'CategoriesVector.active' => $categories_vector['CategoriesVector']['active'],
		'CategoriesVector.created' => $categories_vector['CategoriesVector']['created'],
		'Vector.created' => $categories_vector['Vector']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
?>
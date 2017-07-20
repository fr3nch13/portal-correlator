<?php 
// File: app/View/VectorTypes/csv/index.ctp

$data = array();
foreach ($vectorTypes as $i => $vectorType)
{
    $data[] = array(
		'VectorType.name' => $vectorType['VectorType']['name'],
		'VectorType.id' => $vectorType['VectorType']['id'],
		'VectorType.uri' => $this->Html->url(array('action' => 'view', $vectorType['VectorType']['id'])),
		'VectorType.holder_name' => $this->Wrap->yesNo($vectorType['VectorType']['holder']),
		'VectorType.holder_value' => $vectorType['VectorType']['holder'],
		'VectorType.created' => $vectorType['VectorType']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
?>
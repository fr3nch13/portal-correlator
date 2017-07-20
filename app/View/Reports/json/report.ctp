<?php 
// File: app/View/Reports/json/report.ctp

$data = array();
foreach ($reports as $i => $report)
{
    $data[] = array(
		'Report.name' => $report['Report']['name'],
		'Report.id' => $report['Report']['id'],
		'Report.uri' => $this->Html->url(array('controller' => 'reports', 'action' => 'view', $report['Report']['id'])),
		'Report.mysource' => $report['Report']['mysource'],
		'User.name' => $report['User']['name'],
		'User.id' => $report['User']['id'],
		'Report.public_name' => $this->Wrap->publicState($report['Report']['public']),
		'Report.public_value' => $report['Report']['public'],
		'Report.created' => $report['Report']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
?>
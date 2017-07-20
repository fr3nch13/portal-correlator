<?php 
// File: app/View/Categories/json/category.ctp

$data = array();
foreach ($categories as $i => $category)
{
    $data[] = array(
		'Category.name' => $category['Category']['name'],
		'Category.id' => $category['Category']['id'],
		'Category.uri' => $this->Html->url(array('controller' => 'categories', 'action' => 'view', $category['Category']['id'])),
		'Category.mysource' => $category['Category']['mysource'],
		'User.name' => $category['User']['name'],
		'User.id' => $category['User']['id'],
		'Category.public_name' => $this->Wrap->publicState($category['Category']['public']),
		'Category.public_value' => $category['Category']['public'],
		'Category.created' => $category['Category']['created'],
    );
}

echo $this->Exporter->view($data, array('count' => count($data)), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
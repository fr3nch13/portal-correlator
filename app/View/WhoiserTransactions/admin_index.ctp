<?php 
// File: app/View/WhoiserTransactions/index.ctp


// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'WhoiserTransaction.status' => array('content' => __('Status'), 'options' => array('sort' => 'WhoiserTransaction.status')),
	'User.name' => array('content' => __('Submitting User'), 'options' => array('sort' => 'User.name')),
	'WhoiserTransaction.last_checked' => array('content' => __('Last Checked'), 'options' => array('sort' => 'WhoiserTransaction.last_checked')),
	'WhoiserTransaction.last_changed' => array('content' => __('Last Changed'), 'options' => array('sort' => 'WhoiserTransaction.last_changed')),
	'WhoiserTransaction.created' => array('content' => __('Created'), 'options' => array('sort' => 'WhoiserTransaction.created')),
);

$td = array();
foreach ($whoiser_transactions as $i => $whoiser_transaction)
{
	$status = $whoiser_transaction['WhoiserTransaction']['status'];
	
	$td[$i] = array(
		$this->Html->link($whoiser_transaction['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $whoiser_transaction['Vector']['id'])),
		$compile_states[$status],
		$this->Html->link($whoiser_transaction['User']['name'], array('controller' => 'users', 'action' => 'view', $whoiser_transaction['User']['id'])),
		$this->Wrap->niceTime($whoiser_transaction['WhoiserTransaction']['last_checked']),
		$this->Wrap->niceTime($whoiser_transaction['WhoiserTransaction']['last_changed']),
		$this->Wrap->niceTime($whoiser_transaction['WhoiserTransaction']['created']),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Whoiser Transactions'),
	'page_description' => __('The transactions logs of when a Search was submitted to Whoiser.'),
	'th' => $th,
	'td' => $td,
	));
		
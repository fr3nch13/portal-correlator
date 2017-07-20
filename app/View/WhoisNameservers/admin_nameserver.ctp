<?php 
// File: app/View/Whois/index.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'Whois.createdDate' => array('content' => __('Whois Created'), 'options' => array('sort' => 'Whois.createdDate')),
	'Whois.updatedDate' => array('content' => __('Whois Updated'), 'options' => array('sort' => 'Whois.updatedDate')),
	'Whois.expiresDate' => array('content' => __('Whois Expires'), 'options' => array('sort' => 'Whois.expiresDate')),
//	'Whois.modified' => array('content' => __('Updated'), 'options' => array('sort' => 'Whois.modified')),
	'Whois.created' => array('content' => __('Added'), 'options' => array('sort' => 'Whois.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($whois_nameservers as $i => $whois_nameserver)
{
	$td[$i] = array(
		$whois_nameserver['Whois']['Vector']['vector'],
//		$this->Html->link($whois_nameserver['Whois']['whois'], array('controller' => 'whois', 'action' => 'view', $whois_nameserver['Whois']['id'])),
		$this->Wrap->niceDay($whois_nameserver['Whois']['createdDate']),
		$this->Wrap->niceDay($whois_nameserver['Whois']['updatedDate']),
		$this->Wrap->niceDay($whois_nameserver['Whois']['expiresDate']),
//		$this->Wrap->niceTime($whois_nameserver['Whois']['modified']),
		$this->Wrap->niceTime($whois_nameserver['Whois']['created']),
		array(
			$this->Html->link(__('View Whois'), array('controller' => 'whois', 'action' => 'view', $whois_nameserver['Whois']['id'])).
			$this->Html->link(__('View Vector'), array('controller' => 'vectors', 'action' => 'view', $whois_nameserver['Whois']['Vector']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Whois Records'),
	'search_placeholder' => __('Whois Records'),
	'th' => $th,
	'td' => $td,
	));
?>
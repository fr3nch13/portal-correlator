<?php 
// File: app/View/WhoisNameservers/whois.ctp

// content
$th = array(
	'Nameserver.nameserver' => array('content' => __('Nameserver'), 'options' => array('sort' => 'Nameserver.nameserver')),
//	'Nameserver.modified' => array('content' => __('Updated'), 'options' => array('sort' => 'Nameserver.modified')),
	'Nameserver.created' => array('content' => __('Added'), 'options' => array('sort' => 'Nameserver.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($whois_nameservers as $i => $whois_nameserver)
{
	$td[$i] = array(
		$this->Html->link($whois_nameserver['Nameserver']['nameserver'], array('controller' => 'nameservers', 'action' => 'view', $whois_nameserver['Nameserver']['id'])),
//		$this->Wrap->niceTime($whois_nameserver['Nameserver']['modified']),
		$this->Wrap->niceTime($whois_nameserver['Nameserver']['created']),
		array(
			$this->Html->link(__('View'), array('controller' => 'nameservers', 'action' => 'view', $whois_nameserver['Nameserver']['id'])),
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Nameserver for a Whois Record'),
	'search_placeholder' => __('Nameservers'),
	'th' => $th,
	'td' => $td,
	));
?>
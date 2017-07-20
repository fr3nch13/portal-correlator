<?php 
// File: app/View/Nameserver/view.ctp

// options like edit/delete/etc
$page_options = array();


$details = array();
$details[] = array('name' => __('Added'), 'value' => $this->Wrap->niceTime($nameserver['Nameserver']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($nameserver['Nameserver']['modified']));

$stats = array(
	array(
		'id' => 'Whois',
		'name' => __('Whois Records'), 
		'value' => $nameserver['Nameserver']['counts']['WhoisNameserver.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'whois',
		'title' => __('Whois Records'),
		'url' => array('controller' => 'whois_nameservers', 'action' => 'nameserver', $nameserver['Nameserver']['id']),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Nameserver Details for: %s',  $nameserver['Nameserver']['nameserver']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
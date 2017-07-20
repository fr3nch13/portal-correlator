<?php 
// File: app/View/WhoisLogs/view.ctp

// options like edit/delete/etc
$page_options = array();
$page_options[] = $this->Html->link(__('Return to Current Whois'), array('controller' => 'whois', 'action' => 'view', $whois_log['WhoisLog']['whois_id']));

$details_left = array();
$details_left[] = array('name' => __('Source'), 'value' => $this->Wrap->sourceUser($whois_log['WhoisLog']['source']));
$details_left[] = array('name' => __('Added'), 'value' => $this->Wrap->niceTime($whois_log['WhoisLog']['created']));
$details_left[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($whois_log['WhoisLog']['modified']));
$details_left[] = array('name' => __('Tld'), 'value' => $whois_log['WhoisLog']['tld'], 'filter_data' => 'tld');

if($vector['Vector']['type'] == 'hostname' and isset($vector['Hostname']))
{
	$details_left[] = array('name' => __('Tracking Level'), 'value' => $this->Wrap->whoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup']));
	$details_left[] = array('name' => __('Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_checked']));
	$details_left[] = array('name' => __('Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_updated']));
}
elseif($vector['Vector']['type'] == 'ipaddress' and isset($vector['Ipaddress']))
{
	$details_left[] = array('name' => __('Tracking Level'), 'value' => $this->Wrap->whoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup']));
	$details_left[] = array('name' => __('Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_checked']));
	$details_left[] = array('name' => __('Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_updated']));
}

$details_right = array();
$details_right[] = array('name' => __('Record Created'), 'value' => $this->Wrap->niceTime($whois_log['WhoisLog']['createdDate']));
$details_right[] = array('name' => __('Record Updated'), 'value' => $this->Wrap->niceTime($whois_log['WhoisLog']['updatedDate']));
$details_right[] = array('name' => __('Record Expires'), 'value' => $this->Wrap->niceTime($whois_log['WhoisLog']['expiresDate']));
$details_right[] = array('name' => __('Regristrar Name'), 'value' => $whois_log['WhoisLog']['registrarName'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrarName'));
$details_right[] = array('name' => __('Regristrar Status'), 'value' => $whois_log['WhoisLog']['registrarStatus'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrarStatus'));
$details_right[] = array('name' => __('Contact Email'), 'value' => $whois_log['WhoisLog']['contactEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'contactEmail'), 'escape' => true);

$details_registrant = array();
$details_registrant[] = array('name' => __('Name'), 'value' => $whois_log['WhoisLog']['registrantName'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantName'));
$details_registrant[] = array('name' => __('Org'), 'value' => $whois_log['WhoisLog']['registrantOrg'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantOrg'));
$details_registrant[] = array('name' => __('Address'), 'value' => $whois_log['WhoisLog']['registrantAddress'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantAddress'));
$details_registrant[] = array('name' => __('City'), 'value' => $whois_log['WhoisLog']['registrantCity'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantCity'));
$details_registrant[] = array('name' => __('State'), 'value' => $whois_log['WhoisLog']['registrantState'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantState'));
$details_registrant[] = array('name' => __('Postal'), 'value' => $whois_log['WhoisLog']['registrantPostalCode'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantPostalCode'));
$details_registrant[] = array('name' => __('Country'), 'value' => $whois_log['WhoisLog']['registrantCountry'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantCountry'));
$details_registrant[] = array('name' => __('Phone'), 'value' => $whois_log['WhoisLog']['registrantPhone'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantPhone'));
$details_registrant[] = array('name' => __('Fax'), 'value' => $whois_log['WhoisLog']['registrantFax'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantFax'));
$details_registrant[] = array('name' => __('Email'), 'value' => $whois_log['WhoisLog']['registrantEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantEmail'), 'escape' => true);

$details_admin = array();
$details_admin[] = array('name' => __('Name'), 'value' => $whois_log['WhoisLog']['adminName'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminName'));
$details_admin[] = array('name' => __('Org'), 'value' => $whois_log['WhoisLog']['adminOrg'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminOrg'));
$details_admin[] = array('name' => __('Address'), 'value' => $whois_log['WhoisLog']['adminAddress'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminAddress'));
$details_admin[] = array('name' => __('City'), 'value' => $whois_log['WhoisLog']['adminCity'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminCity'));
$details_admin[] = array('name' => __('State'), 'value' => $whois_log['WhoisLog']['adminState'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminState'));
$details_admin[] = array('name' => __('Postal'), 'value' => $whois_log['WhoisLog']['adminPostalCode'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminPostalCode'));
$details_admin[] = array('name' => __('Country'), 'value' => $whois_log['WhoisLog']['adminCountry'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminCountry'));
$details_admin[] = array('name' => __('Phone'), 'value' => $whois_log['WhoisLog']['adminPhone'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminPhone'));
$details_admin[] = array('name' => __('Fax'), 'value' => $whois_log['WhoisLog']['adminFax'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminFax'));
$details_admin[] = array('name' => __('Email'), 'value' => $whois_log['WhoisLog']['adminEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminEmail'), 'escape' => true);

$details_tech = array();
$details_tech[] = array('name' => __('Name'), 'value' => $whois_log['WhoisLog']['techName'], 'filter_data' => array('controller' => 'whois', 'field' => 'techName'));
$details_tech[] = array('name' => __('Org'), 'value' => $whois_log['WhoisLog']['techOrg'], 'filter_data' => array('controller' => 'whois', 'field' => 'techOrg'));
$details_tech[] = array('name' => __('Address'), 'value' => $whois_log['WhoisLog']['techAddress'], 'filter_data' => array('controller' => 'whois', 'field' => 'techAddress'));
$details_tech[] = array('name' => __('City'), 'value' => $whois_log['WhoisLog']['techCity'], 'filter_data' => array('controller' => 'whois', 'field' => 'techCity'));
$details_tech[] = array('name' => __('State'), 'value' => $whois_log['WhoisLog']['techState'], 'filter_data' => array('controller' => 'whois', 'field' => 'techState'));
$details_tech[] = array('name' => __('Postal'), 'value' => $whois_log['WhoisLog']['techPostalCode'], 'filter_data' => array('controller' => 'whois', 'field' => 'techPostalCode'));
$details_tech[] = array('name' => __('Country'), 'value' => $whois_log['WhoisLog']['techCountry'], 'filter_data' => array('controller' => 'whois', 'field' => 'techCountry'));
$details_tech[] = array('name' => __('Phone'), 'value' => $whois_log['WhoisLog']['techPhone'], 'filter_data' => array('controller' => 'whois', 'field' => 'techPhone'));
$details_tech[] = array('name' => __('Fax'), 'value' => $whois_log['WhoisLog']['techFax'], 'filter_data' => array('controller' => 'whois', 'field' => 'techFax'));
$details_tech[] = array('name' => __('Email'), 'value' => $whois_log['WhoisLog']['techEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'techEmail'), 'escape' => true);

$details_billing = array();
$details_billing[] = array('name' => __('Name'), 'value' => $whois_log['WhoisLog']['billingName'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingName'));
$details_billing[] = array('name' => __('Org'), 'value' => $whois_log['WhoisLog']['billingOrg'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingOrg'));
$details_billing[] = array('name' => __('Address'), 'value' => $whois_log['WhoisLog']['billingAddress'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingAddress'));
$details_billing[] = array('name' => __('City'), 'value' => $whois_log['WhoisLog']['billingCity'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingCity'));
$details_billing[] = array('name' => __('State'), 'value' => $whois_log['WhoisLog']['billingState'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingState'));
$details_billing[] = array('name' => __('Postal'), 'value' => $whois_log['WhoisLog']['billingPostalCode'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingPostalCode'));
$details_billing[] = array('name' => __('Country'), 'value' => $whois_log['WhoisLog']['billingCountry'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingCountry'));
$details_billing[] = array('name' => __('Phone'), 'value' => $whois_log['WhoisLog']['billingPhone'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingPhone'));
$details_billing[] = array('name' => __('Fax'), 'value' => $whois_log['WhoisLog']['billingFax'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingFax'));
$details_billing[] = array('name' => __('Email'), 'value' => $whois_log['WhoisLog']['billingEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingEmail'), 'escape' => true);

$stats = array(
);

$tabs = array(
	array(
		'key' => 'details_registrant',
		'title' => __('Registrant Details'),
		'content' => $this->element('Utilities.details', array(
			'title' => __('Registrant Details'),
			'details' => $details_registrant,
		)),
	),
	array(
		'key' => 'details_admin',
		'title' => __('Administrator Details'),
		'content' => $this->element('Utilities.details', array(
			'title' => __('Administrator Details'),
			'details' => $details_admin,
		)),
	),
	array(
		'key' => 'details_tech',
		'title' => __('Technical Details'),
		'content' => $this->element('Utilities.details', array(
			'title' => __('Technical Details'),
			'details' => $details_tech,
		)),
	),
	array(
		'key' => 'details_billing',
		'title' => __('Billing Details'),
		'content' => $this->element('Utilities.details', array(
			'title' => __('Billing Details'),
			'details' => $details_billing,
		)),
	),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Whois Log Details for: %s',  $whois_log['Vector']['vector']),
	'page_options' => $page_options,
	'details_left_title' => __('Details'),
	'details_left' => $details_left,
	'details_right_title' => '&nbsp;',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
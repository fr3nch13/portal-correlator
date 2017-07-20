<?php 
// File: app/View/Whois/view.ctp

// options like edit/delete/etc
$page_options = array();
$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $whois['Whois']['id']));
$page_options[] = $this->Form->postLink(__('Update WHOIS'),array('controller' => 'vectors', 'action' => 'update_whois', $whois['Vector']['id']), array('confirm' => 'Are you sure? Depending on the sources, this may take awhile.'));


$details_left = array();
$details_left[] = array('name' => __('Vector'), 'value' => $this->Html->link($vector['Vector']['vector'], array('controller' => 'vectors', 'action' => 'view', $vector['Vector']['id'])));
$details_left[] = array('name' => __('Source'), 'value' => $this->Wrap->sourceUser($whois['Whois']['source']));
$details_left[] = array('name' => __('Added'), 'value' => $this->Wrap->niceTime($whois['Whois']['created']));
$details_left[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($whois['Whois']['modified']));
$details_left[] = array('name' => __('Tld'), 'value' => $whois['Whois']['tld'], 'filter_data' => 'tld');

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
else
{
	$whoiser_status = $vector['WhoiserTransaction']['status'];
	$details_left[] = array('name' => __('Whoiser Status'), 'value' => $whoiser_compile_states[$whoiser_status]);
	$details_left[] = array('name' => __('Checked'), 'value' => $this->Wrap->niceTime($whois['Whois']['whois_checked']));
	$details_left[] = array('name' => __('Updated'), 'value' => $this->Wrap->niceTime($whois['Whois']['whois_updated']));
}


$details_right = array();
$details_right[] = array('name' => __('Record Created'), 'value' => $this->Wrap->niceTime($whois['Whois']['createdDate']));
$details_right[] = array('name' => __('Record Updated'), 'value' => $this->Wrap->niceTime($whois['Whois']['updatedDate']));
$details_right[] = array('name' => __('Record Expires'), 'value' => $this->Wrap->niceTime($whois['Whois']['expiresDate']));
$details_right[] = array('name' => __('Regristrar Name'), 'value' => $whois['Whois']['registrarName'], 'filter_data' => 'registrarName');
$details_right[] = array('name' => __('Regristrar Status'), 'value' => $whois['Whois']['registrarStatus'], 'filter_data' => 'registrarStatus');
$details_right[] = array('name' => __('Contact Email'), 'value' => $whois['Whois']['contactEmail'], 'filter_data' => 'contactEmail', 'escape' => true);

$details_registrant = array();
$details_registrant[] = array('name' => __('Name'), 'value' => $whois['Whois']['registrantName'], 'filter_data' => 'registrantName');
$details_registrant[] = array('name' => __('Org'), 'value' => $whois['Whois']['registrantOrg'], 'filter_data' => 'registrantOrg');
$details_registrant[] = array('name' => __('Address'), 'value' => $whois['Whois']['registrantAddress'], 'filter_data' => 'registrantAddress');
$details_registrant[] = array('name' => __('City'), 'value' => $whois['Whois']['registrantCity'], 'filter_data' => 'registrantCity');
$details_registrant[] = array('name' => __('State'), 'value' => $whois['Whois']['registrantState'], 'filter_data' => 'registrantState');
$details_registrant[] = array('name' => __('Postal'), 'value' => $whois['Whois']['registrantPostalCode'], 'filter_data' => 'registrantPostalCode');
$details_registrant[] = array('name' => __('Country'), 'value' => $whois['Whois']['registrantCountry'], 'filter_data' => 'registrantCountry');
$details_registrant[] = array('name' => __('Phone'), 'value' => $whois['Whois']['registrantPhone'], 'filter_data' => 'registrantPhone');
$details_registrant[] = array('name' => __('Fax'), 'value' => $whois['Whois']['registrantFax'], 'filter_data' => 'registrantFax');
$details_registrant[] = array('name' => __('Email'), 'value' => $whois['Whois']['registrantEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'registrantEmail'), 'escape' => true);

$details_admin = array();
$details_admin[] = array('name' => __('Name'), 'value' => $whois['Whois']['adminName'], 'filter_data' => 'adminName');
$details_admin[] = array('name' => __('Org'), 'value' => $whois['Whois']['adminOrg'], 'filter_data' => 'adminOrg');
$details_admin[] = array('name' => __('Address'), 'value' => $whois['Whois']['adminAddress'], 'filter_data' => 'adminAddress');
$details_admin[] = array('name' => __('City'), 'value' => $whois['Whois']['adminCity'], 'filter_data' => 'adminCity');
$details_admin[] = array('name' => __('State'), 'value' => $whois['Whois']['adminState'], 'filter_data' => 'adminState');
$details_admin[] = array('name' => __('Postal'), 'value' => $whois['Whois']['adminPostalCode'], 'filter_data' => 'adminPostalCode');
$details_admin[] = array('name' => __('Country'), 'value' => $whois['Whois']['adminCountry'], 'filter_data' => 'adminCountry');
$details_admin[] = array('name' => __('Phone'), 'value' => $whois['Whois']['adminPhone'], 'filter_data' => 'adminPhone');
$details_admin[] = array('name' => __('Fax'), 'value' => $whois['Whois']['adminFax'], 'filter_data' => 'adminFax');
$details_admin[] = array('name' => __('Email'), 'value' => $whois['Whois']['adminEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'adminEmail'), 'escape' => true);

$details_tech = array();
$details_tech[] = array('name' => __('Name'), 'value' => $whois['Whois']['techName'], 'filter_data' => 'techName');
$details_tech[] = array('name' => __('Org'), 'value' => $whois['Whois']['techOrg'], 'filter_data' => 'techOrg');
$details_tech[] = array('name' => __('Address'), 'value' => $whois['Whois']['techAddress'], 'filter_data' => 'techAddress');
$details_tech[] = array('name' => __('City'), 'value' => $whois['Whois']['techCity'], 'filter_data' => 'techCity');
$details_tech[] = array('name' => __('State'), 'value' => $whois['Whois']['techState'], 'filter_data' => 'techState');
$details_tech[] = array('name' => __('Postal'), 'value' => $whois['Whois']['techPostalCode'], 'filter_data' => 'techPostalCode');
$details_tech[] = array('name' => __('Country'), 'value' => $whois['Whois']['techCountry'], 'filter_data' => 'techCountry');
$details_tech[] = array('name' => __('Phone'), 'value' => $whois['Whois']['techPhone'], 'filter_data' => 'techPhone');
$details_tech[] = array('name' => __('Fax'), 'value' => $whois['Whois']['techFax'], 'filter_data' => 'techFax');
$details_tech[] = array('name' => __('Email'), 'value' => $whois['Whois']['techEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'techEmail'), 'escape' => true);

$details_billing = array();
$details_billing[] = array('name' => __('Name'), 'value' => $whois['Whois']['billingName'], 'filter_data' => 'billingName');
$details_billing[] = array('name' => __('Org'), 'value' => $whois['Whois']['billingOrg'], 'filter_data' => 'billingOrg');
$details_billing[] = array('name' => __('Address'), 'value' => $whois['Whois']['billingAddress'], 'filter_data' => 'billingAddress');
$details_billing[] = array('name' => __('City'), 'value' => $whois['Whois']['billingCity'], 'filter_data' => 'billingCity');
$details_billing[] = array('name' => __('State'), 'value' => $whois['Whois']['billingState'], 'filter_data' => 'billingState');
$details_billing[] = array('name' => __('Postal'), 'value' => $whois['Whois']['billingPostalCode'], 'filter_data' => 'billingPostalCode');
$details_billing[] = array('name' => __('Country'), 'value' => $whois['Whois']['billingCountry'], 'filter_data' => 'billingCountry');
$details_billing[] = array('name' => __('Phone'), 'value' => $whois['Whois']['billingPhone'], 'filter_data' => 'billingPhone');
$details_billing[] = array('name' => __('Fax'), 'value' => $whois['Whois']['billingFax'], 'filter_data' => 'billingFax');
$details_billing[] = array('name' => __('Email'), 'value' => $whois['Whois']['billingEmail'], 'filter_data' => array('controller' => 'whois', 'field' => 'billingEmail'), 'escape' => true);

$stats = array(
	array(
		'id' => 'Nameservers',
		'name' => __('Nameservers'), 
		'value' => $whois['Whois']['counts']['WhoisNameserver.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'WhoisLogHistory',
		'name' => __('Whois Log History'), 
		'value' => $whois['Whois']['counts']['WhoisLog.all'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
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
	array(
		'key' => 'nameservers',
		'title' => __('Nameservers'),
		'url' => array('controller' => 'whois_nameservers', 'action' => 'whois', $whois['Whois']['id']),
	),
	array(
		'key' => 'WhoisLogHistory',
		'title' => __('Whois Log History'),
		'url' => array('controller' => 'whois_logs', 'action' => 'whois', $whois['Whois']['id']),
	),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('Whois Details for: %s',  $whois['Vector']['vector']),
	'page_subtitle' => __('Source: %s - Tld: %s', $this->Wrap->sourceUser($whois['Whois']['source']), $whois['Whois']['tld']),
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
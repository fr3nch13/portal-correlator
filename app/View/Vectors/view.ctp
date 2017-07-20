<?php 

$page_options = [
	$this->Html->link(__('Edit'), ['action' => 'edit', $vector['Vector']['id']]),
];
$page_options2 = [
	$this->Html->link(__('Update WHOIS'), ['action' => 'update_whois', $vector['Vector']['id'], 'tab' => 'whois'], ['confirm' => 'Are you sure? Depending on the sources, this may take awhile.']),
	$this->Html->link(__('Update Type'), ['action' => 'update_type', $vector['Vector']['id']]),
];

// VirusTotal plugs
if(in_array($vector['Vector']['type'], $vtTypeList))
{
	$page_options[] = $this->Html->link(__('%s Details', __('VirusTotal')), ['action' => 'vtview', $vector['Vector']['id']]);
//	$page_options2[] = $this->Html->link(__('Update %s Details', __('VirusTotal')),array('action' => 'update_vt', $vector['Vector']['id']), array('confirm' => 'Are you sure? Depending on the results, this may take awhile.'));
}

$details_left = [];
$details_left[] = ['name' => __('Type'), 'value' => $this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), ['action' => 'type', $vector['Vector']['type']])];
$details_left[] = ['name' => __('Added'), 'value' => $this->Wrap->niceTime($vector['Vector']['created'])];
//$details[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($vector['Vector']['modified']));
$vgroup = '&nbsp;';
if($vector['VectorType']['name'])
{
	$vgroup = $this->Html->link($vector['VectorType']['name'], ['controller' => 'vector_types', 'action' => 'view', $vector['VectorType']['id']]);
}
$details_left[] = ['name' => __('Vector Group'), 'value' => $vgroup];

$details_right = [];
$details_right[] = ['name' => __('First Source Type'), 'value' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type'])];
$details_right[] = ['name' => __('First Source'), 'value' => $this->Wrap->sourceUser($vector['VectorSourceFirst']['source'])];
$details_right[] = ['name' => __('First Source Date'), 'value' => $this->Wrap->niceTime($vector['VectorSourceFirst']['created'])];
$details_right[] = ['name' => __('Last Source Type'), 'value' => $this->Wrap->niceWord($vector['VectorSourceLast']['source_type'])];
$details_right[] = ['name' => __('Last Source'), 'value' => $this->Wrap->sourceUser($vector['VectorSourceLast']['source'])];
$details_right[] = ['name' => __('Last Source Date'), 'value' => $this->Wrap->niceTime($vector['VectorSourceLast']['created'])];

// VirusTotal plugs
if(in_array($vector['Vector']['type'], $vtTypeList))
{
	$details_right[] = ['name' => __('VT Tracking Level'), 'value' => $this->Wrap->vtAutoLookupLevel($vector['VectorDetail']['vt_lookup'])];
	$details_right[] = ['name' => __('VT Checked'), 'value' => $this->Wrap->niceTime($vector['VectorDetail']['vt_checked'])];
	$details_right[] = ['name' => __('VT Updated'), 'value' => $this->Wrap->niceTime($vector['VectorDetail']['vt_updated'])];
	
}

$stats = $tabs = [];

$tabs['categories'] = $stats['categories'] = [
	'id' => 'categories',
	'name' => __('Categories'), 
	'ajax_url' => ['controller' => 'categories', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['reports'] = $stats['reports'] = [
	'id' => 'reports',
	'name' => __('Reports'), 
	'ajax_url' => ['controller' => 'reports', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['files'] = $stats['files'] = [
	'id' => 'files',
	'name' => __('Files'), 
	'ajax_url' => ['controller' => 'uploads', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['imports'] = $stats['imports'] = [
	'id' => 'imports',
	'name' => __('Imports'), 
	'ajax_url' => ['controller' => 'imports', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['vector_sources'] = $stats['vector_sources'] = [
	'id' => 'vector_sources',
	'name' => __('Sources'), 
	'ajax_url' => ['controller' => 'vector_sources', 'action' => 'vector', $vector['Vector']['id']],
];

// for specific hostname and ip address vectors
if(in_array($vector['Vector']['type'], ['hostname', 'ipaddress']))
{
	$page_options2[] = $this->Html->link(__('Update DNS'), ['action' => 'update_dns', $vector['Vector']['id'], 'tab' => 'dns'], ['confirm' => 'Are you sure? Depending on the sources, this may take awhile.']);
		
	$count_value = 0;
	if($vector['Vector']['type'] == 'hostname')
	{
		$details_left[] = ['name' => __('DNS Tracking Level'), 'value' => $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['dns_auto_lookup'])];
		$details_left[] = ['name' => __('DNS Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_checked'])];
		$details_left[] = ['name' => __('DNS Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_updated'])];
		$dns_count_value = $vector['Vector']['counts']['NslookupHostname.all'];
		$details_left[] = ['name' => __('Whois Tracking Level'), 'value' => $this->Wrap->WhoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup'])];
		$details_left[] = ['name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_checked'])];
		$details_left[] = ['name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_updated'])];
		$action = 'hostname';
	}
	elseif($vector['Vector']['type'] == 'ipaddress')
	{
		$details_left[] = ['name' => __('DNS Tracking Level'), 'value' => $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'])];
		$details_left[] = ['name' => __('DNS Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_checked'])];
		$details_left[] = ['name' => __('DNS Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_updated'])];
		$dns_count_value = $vector['Vector']['counts']['NslookupIpaddress.all'];
		$details_left[] = ['name' => __('Whois Tracking Level'), 'value' => $this->Wrap->WhoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup'])];
		$details_left[] = ['name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_checked'])];
		$details_left[] = ['name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_updated'])];
		$action = 'ipaddress';
		$page_options2[] = $this->Html->link(__('Update Geoip'), ['action' => 'update_geoip', $vector['Vector']['id'], 'tab' => 'geoip'], ['confirm' => 'Are you sure?']);
	}

	$tabs['dns'] = $stats['dns'] = [
		'id' => 'dns',
		'name' => __('DNS Records'), 
		'ajax_url' => ['controller' => 'nslookups', 'action' => $vector['Vector']['type'], $vector['Vector']['id']],
	];
	$tabs['dns_transaction_logs'] = $stats['dns_transaction_logs'] = [
		'id' => 'dns_transaction_logs',
		'name' => __('DNS Transaction Logs'), 
		'ajax_url' => ['controller' => 'dns_transaction_logs', 'action' => 'vector', $vector['Vector']['id']],
	];
	
	if($vector['Vector']['type'] == 'ipaddress' and isset($vector['Geoip']['id']))
	{
		$geoip_details = [];
		$geoip_details[] = ['name' => __('Country'), 'value' => $vector['Geoip']['country_name']];
		$geoip_details[] = ['name' => __('Region'), 'value' => $vector['Geoip']['region_name']];
		$geoip_details[] = ['name' => __('City'), 'value' => $vector['Geoip']['city']];
		$geoip_details[] = ['name' => __('Postal Code'), 'value' => $vector['Geoip']['postal_code']];
		$geoip_details[] = ['name' => __('Added'), 'value' => $this->Wrap->niceTime($vector['Geoip']['created'])];
		$geoip_details[] = ['name' => __('Updated'), 'value' => $this->Wrap->niceTime($vector['Geoip']['modified'])];
		
		$tabs['geoip'] = [
			'id' => 'geoip',
			'name' => __('Geoip Details'),
			'content' => $this->element('Utilities.details', [
				'details' => $geoip_details,
			]),
		];
	}
}
else
{
	$whois_checked = $vector['WhoisLast']['whois_checked'];
	if(!$whois_checked) $whois_checked = $vector['WhoiserTransaction']['last_checked'];
	$details_left[] = ['name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($whois_checked)];
	
	$whois_updated = $vector['WhoisLast']['whois_updated'];
	if(!$whois_updated) $whois_updated = $vector['WhoiserTransaction']['last_changed'];
	if(!$whois_updated) $whois_checked;
	$details_left[] = ['name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($whois_updated)];
	
	if(isset($vector['WhoiserTransaction']['status']))
	{
		$whoiser_status = $vector['WhoiserTransaction']['status'];
		$details_left[] = ['name' => __('Whoiser Status'), 'value' => $whoiser_compile_states[$whoiser_status]];
	}
}

$tabs['whois'] = $stats['whois'] = [
	'id' => 'whois',
	'name' => __('Whois Records'), 
	'ajax_url' => ['controller' => 'whois', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['whois_transaction_logs'] = $stats['whois_transaction_logs'] = [
	'id' => 'whois_transaction_logs',
	'name' => __('Whois Transaction Logs'), 
	'ajax_url' => ['controller' => 'whois_transaction_logs', 'action' => 'vector', $vector['Vector']['id']],
];
$tabs['tags'] = $stats['tags'] = [
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_url' => ['plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'vector', $vector['Vector']['id']],
];

/*
// VirusTotal Stuff
if(in_array($vector['Vector']['type'], $vtTypeList))
{
	
	$stat_count++;
	$tab_count++;
	$stats[] = array(
		'id' => 'VtNtRecords',
		'name' => __('VT Network Records'), 
		'value' => $vector['Vector']['counts']['VtNtRecord.all'], 
		'tab' => array('tabs', $tab_count), // the tab to display
	);
	$tabs[] = array(
		'key' => 'VtNtRecords',
		'title' => __('VT Network Records'),
		'url' => array('controller' => 'vt_nt_records', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	$stat_count++;
	$tab_count++;
	$stats[] = array(
		'id' => 'VtRelatedSamples',
		'name' => __('VT Related Samples'), 
		'value' => $vector['Vector']['counts']['VtRelatedSample.all'], 
		'tab' => array('tabs', $tab_count), // the tab to display
	);
	$tabs[] = array(
		'key' => 'VtRelatedSamples',
		'title' => __('VT Related Samples'),
		'url' => array('controller' => 'vt_related_samples', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	$stat_count++;
	$tab_count++;
	$stats[] = array(
		'id' => 'VtDetectedUrls',
		'name' => __('VT Detected Urls'), 
		'value' => $vector['Vector']['counts']['VtDetectedUrl.all'], 
		'tab' => array('tabs', $tab_count), // the tab to display
	);
	$tabs[] = array(
		'key' => 'VtDetectedUrls',
		'title' => __('VT Detected Urls'),
		'url' => array('controller' => 'vt_detected_urls', 'action' => 'vector', $vector['Vector']['id']),
	);
}
*/

echo $this->element('Utilities.page_compare', [
	'page_title' => __('%s: %s', __('Vector'), $vector['Vector']['vector']),
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'details_left_title' => __('Details'),
	'details_left' => $details_left,
	'details_right_title' => '&nbsp;',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);
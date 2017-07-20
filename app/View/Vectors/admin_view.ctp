<?php 
// File: app/View/Vectors/admin_view.ctp

$page_options = array(
	$this->Html->link(__('Edit'), array('action' => 'edit', $vector['Vector']['id'])),
	$this->Html->link(__('Toggle %s State', __('Benign')), array('action' => 'toggle', 'bad', $vector['Vector']['id'])),
	$this->Form->postLink(__('Update WHOIS'),array('action' => 'update_whois', $vector['Vector']['id'], 'ui-tabs-7'), array('confirm' => 'Are you sure? Depending on the sources, this may take awhile.')),
);
// VirusTotal plugs
if(in_array($vector['Vector']['type'], $vtTypeList))
{
	$page_options[] = $this->Form->postLink(__('Update %s Details', __('VirusTotal')),array('action' => 'update_vt', $vector['Vector']['id']), array('confirm' => 'Are you sure? Depending on the results, this may take awhile.'));
}

$details_blocks = array();

$vgroup = '&nbsp;';
if($vector['VectorType']['name'])
{
	$vgroup = $this->Html->link($vector['VectorType']['name'], array('controller' => 'vector_types', 'action' => 'view', $vector['VectorType']['id']));
}

$details_blocks[1][1] = array(
	'title' => __('Details'),
	'details' => array(
		array('name' => __('%s State', __('Benign')), 'value' => $this->Wrap->yesNo($vector['Vector']['bad'])),
		array('name' => __('Type'), 'value' => $this->Html->link($this->Wrap->niceWord($vector['Vector']['type']), array('action' => 'type', $vector['Vector']['type']))),
		array('name' => __('Vector Group'), 'value' => $vgroup),
		array('name' => __('Added'), 'value' => $this->Wrap->niceTime($vector['Vector']['created'])),
	),
);

$details_blocks[1][2] = array(
	'title' => __('First Source Tracking'),
	'details' => array(
		array('name' => __('Type'), 'value' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source_type'])),
		array('name' => __('Source'), 'value' => $this->Wrap->niceWord($vector['VectorSourceFirst']['source'])),
		array('name' => __('Date'), 'value' => $this->Wrap->niceTime($vector['VectorSourceFirst']['created'])),
	),
);

$details_blocks[1][3] = array(
	'title' => __('Last Source Tracking'),
	'details' => array(
		array('name' => __('Type'), 'value' => $this->Wrap->niceWord($vector['VectorSourceLast']['source_type'])),
		array('name' => __('Source'), 'value' => $this->Wrap->niceWord($vector['VectorSourceLast']['source'])),
		array('name' => __('Date'), 'value' => $this->Wrap->niceTime($vector['VectorSourceLast']['created'])),
	),
);


$stats = array(
	array(
		'id' => 'CategoriesVector',
		'name' => __('Categories'), 
		'value' => $vector['Vector']['counts']['CategoriesVector.related'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'ReportsVector',
		'name' => __('Reports'), 
		'value' => $vector['Vector']['counts']['ReportsVector.related'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'UploadsVector',
		'name' => __('Files'), 
		'value' => $vector['Vector']['counts']['UploadsVector.related'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
	array(
		'id' => 'ImportsVector',
		'name' => __('Imports'), 
		'value' => $vector['Vector']['counts']['ImportsVector.related'], 
		'tab' => array('tabs', '4'), // the tab to display
	),
	array(
		'id' => 'VectorSources',
		'name' => __('Sources'), 
		'value' => $vector['Vector']['counts']['VectorSource.all'], 
		'tab' => array('tabs', '5'), // the tab to display
	),
	array(
		'id' => 'tagsVector',
		'name' => __('Tags'), 
		'value' => $vector['Vector']['counts']['Tagged.all'], 
		'tab' => array('tabs', '6'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'categories',
		'title' => __('Categories'),
		'url' => array('controller' => 'categories', 'action' => 'vector', $vector['Vector']['id']),
	),
	array(
		'key' => 'reports',
		'title' => __('Reports'),
		'url' => array('controller' => 'reports', 'action' => 'vector', $vector['Vector']['id']),
	),
	array(
		'key' => 'files',
		'title' => __('Files'),
		'url' => array('controller' => 'uploads', 'action' => 'vector', $vector['Vector']['id']),
	),
	array(
		'key' => 'imports',
		'title' => __('Imports'),
		'url' => array('controller' => 'imports', 'action' => 'vector', $vector['Vector']['id']),
	),
	array(
		'key' => 'sources',
		'title' => __('Sources'),
		'url' => array('controller' => 'vector_sources', 'action' => 'vector', $vector['Vector']['id']),
	),
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'vector', $vector['Vector']['id']),
	),
);

$whois_count_value = $vector['Vector']['counts']['Whois.all'];

// for specific hostname and ip address vectors
if(in_array($vector['Vector']['type'], array('hostname', 'ipaddress')))
{
	$page_options[] = $this->Form->postLink(__('Update DNS'),array('action' => 'update_dns', $vector['Vector']['id'], 'ui-tabs-7'), array('confirm' => 'Are you sure? Depending on the sources, this may take awhile.'));
	
	$details_blocks[2][1] = array(
		'title' => __('Host Tracking'),
		'details' => array(),
	);
	
	$details_blocks[2][2] = array(
		'title' => __('Other DNS Tracking'),
		'details' => array(),
	);

	$count_value = 0;
	if($vector['Vector']['type'] == 'hostname')
	{
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Tracking Level'), 'value' => $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['dns_auto_lookup']));
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_checked']));
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_updated']));
		$dns_count_value = $vector['Vector']['counts']['NslookupHostname.all'];
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Tracking Level'), 'value' => $this->Wrap->WhoisAutoLookupLevel($vector['Hostname']['whois_auto_lookup']));
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_checked']));
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['whois_updated']));
		
		$details_blocks[2][2]['details'][] = array('name' => __('DnsDbapi Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_checked_dnsdbapi']));
		$details_blocks[2][2]['details'][] = array('name' => __('DnsDbapi Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_updated_dnsdbapi']));
		$details_blocks[2][2]['details'][] = array('name' => __('VirusTotal Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_checked_virustotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('VirusTotal Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_updated_virustotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('PassiveTotal Checked'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_checked_passivetotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('PassiveTotal Updated'), 'value' => $this->Wrap->niceTime($vector['Hostname']['dns_updated_passivetotal']));
		
		$action = 'hostname';
	}
	elseif($vector['Vector']['type'] == 'ipaddress')
	{
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Tracking Level'), 'value' => $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup']));
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_checked']));
		$details_blocks[2][1]['details'][] = array('name' => __('DNS Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_updated']));
		$dns_count_value = $vector['Vector']['counts']['NslookupIpaddress.all'];
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Tracking Level'), 'value' => $this->Wrap->WhoisAutoLookupLevel($vector['Ipaddress']['whois_auto_lookup']));
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_checked']));
		$details_blocks[2][1]['details'][] = array('name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['whois_updated']));
		
		$details_blocks[2][2]['details'][] = array('name' => __('DnsDbapi Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_checked_dnsdbapi']));
		$details_blocks[2][2]['details'][] = array('name' => __('DnsDbapi Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_updated_dnsdbapi']));
		$details_blocks[2][2]['details'][] = array('name' => __('VirusTotal Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_checked_virustotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('VirusTotal Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_updated_virustotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('PassiveTotal Checked'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_checked_passivetotal']));
		$details_blocks[2][2]['details'][] = array('name' => __('PassiveTotal Updated'), 'value' => $this->Wrap->niceTime($vector['Ipaddress']['dns_updated_passivetotal']));
	
		$action = 'ipaddress';
		$page_options[] = $this->Form->postLink(__('Update Geoip'),array('action' => 'update_geoip', $vector['Vector']['id'], 'tabs-geoip'), array('confirm' => 'Are you sure?'));
	}


	$stats[] = array(
		'id' => 'nslookups',
		'name' => __('DNS Records'), 
		'value' => $dns_count_value, 
		'tab' => array('tabs', '7'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'nslookups',
		'title' => __('DNS Records'),
		'url' => array('controller' => 'nslookups', 'action' => $action, $vector['Vector']['id']),
	);
	
	$stats[] = array(
		'id' => 'dns_transaction_logs',
		'name' => __('DNS Transaction Logs'), 
		'value' => $vector['Vector']['counts']['DnsTransactionLog.all'], 
		'tab' => array('tabs', '8'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'dns_transaction_logs',
		'title' => __('DNS Transaction Logs'),
		'url' => array('controller' => 'dns_transaction_logs', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	$stats[] = array(
		'id' => 'whois',
		'name' => __('Whois Records'), 
		'value' => $whois_count_value, 
		'tab' => array('tabs', '9'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'whois',
		'title' => __('Whois Records'),
		'url' => array('controller' => 'whois', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	$stats[] = array(
		'id' => 'whois_transaction_logs',
		'name' => __('Whois Transaction Logs'), 
		'value' => $vector['Vector']['counts']['WhoisTransactionLog.all'], 
		'tab' => array('tabs', '10'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'whois_transaction_logs',
		'title' => __('Whois Transaction Logs'),
		'url' => array('controller' => 'whois_transaction_logs', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	if($vector['Vector']['type'] == 'ipaddress' and isset($vector['Geoip']['id']))
	{
		$details_blocks[2][3] = array(
			'title' => __('GeoIp Tracking'),
			'details' => array(),
		);
		
		$details_blocks[2][3]['details'] = array();
		$details_blocks[2][3]['details'][] = array('name' => __('Country'), 'value' => $vector['Geoip']['country_name']);
		$details_blocks[2][3]['details'][] = array('name' => __('Region'), 'value' => $vector['Geoip']['region_name']);
		$details_blocks[2][3]['details'][] = array('name' => __('City'), 'value' => $vector['Geoip']['city']);
		$details_blocks[2][3]['details'][] = array('name' => __('Postal Code'), 'value' => $vector['Geoip']['postal_code']);
		$details_blocks[2][3]['details'][] = array('name' => __('Added'), 'value' => $this->Wrap->niceTime($vector['Geoip']['created']));
		$details_blocks[2][3]['details'][] = array('name' => __('Updated'), 'value' => $this->Wrap->niceTime($vector['Geoip']['modified']));
		
	}
}
else
{
	$whois_checked = $vector['WhoisLast']['whois_checked'];
	if(!$whois_checked) $whois_checked = $vector['WhoiserTransaction']['last_checked'];
	$details_left[] = array('name' => __('Whois Checked'), 'value' => $this->Wrap->niceTime($whois_checked));
	
	$whois_updated = $vector['WhoisLast']['whois_updated'];
	if(!$whois_updated) $whois_updated = $vector['WhoiserTransaction']['last_changed'];
	if(!$whois_updated) $whois_checked;
	$details_left[] = array('name' => __('Whois Updated'), 'value' => $this->Wrap->niceTime($whois_updated));
	
	if(isset($vector['WhoiserTransaction']['status']))
	{
		$whoiser_status = $vector['WhoiserTransaction']['status'];
		$details_left[] = array('name' => __('Whoiser Status'), 'value' => $whoiser_compile_states[$whoiser_status]);
	}
	
	$stats[] = array(
		'id' => 'whois',
		'name' => __('Whois Records'), 
		'value' => $whois_count_value, 
		'tab' => array('tabs', '7'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'whois',
		'title' => __('Whois Records'),
		'url' => array('controller' => 'whois', 'action' => 'vector', $vector['Vector']['id']),
	);
	
	$stats[] = array(
		'id' => 'whois_transaction_logs',
		'name' => __('Whois Transaction Logs'), 
		'value' => $vector['Vector']['counts']['WhoisTransactionLog.all'], 
		'tab' => array('tabs', '8'), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'whois_transaction_logs',
		'title' => __('Whois Transaction Logs'),
		'url' => array('controller' => 'whois_transaction_logs', 'action' => 'vector', $vector['Vector']['id']),
	);
}


echo $this->element('Utilities.page_view_columns', array(
	'page_title' => __('Vector'). ': '. $vector['Vector']['vector'],
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
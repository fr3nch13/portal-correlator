<?php 
// File: app/View/Nslookups/view.ctp

$page_title = __('Nslookup Details');
if(isset($nslookup['VectorHostname']['vector'])) $page_title .= ': '. $nslookup['VectorHostname']['vector'];
if(isset($nslookup['VectorIpaddress']['vector'])) $page_title .= ' - '. $nslookup['VectorIpaddress']['vector'];
if(isset($nslookup['Nslookup']['source'])) $page_title .= ' - '. $this->Wrap->sourceUser($nslookup['Nslookup']['source']);

$details = array();

$details[] = array('name' => __('Reported TTL'), 'value' => $nslookup['Nslookup']['ttl']);
//$details[] = array('name' => __('Manual TTL'), 'value' => $nslookup['Nslookup']['ttl_manual']);
//$details[] = array('name' => __('Dynamic TTL'), 'value' => $nslookup['Nslookup']['ttl_dynamic']);
$details[] = array('name' => __('First Seen'), 'value' => $this->Wrap->niceTime($nslookup['Nslookup']['first_seen']));
$details[] = array('name' => __('Last Seen'), 'value' => $this->Wrap->niceTime($nslookup['Nslookup']['last_seen']));
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($nslookup['Nslookup']['created']));
$details[] = array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($nslookup['Nslookup']['modified']));

$page_options = array();

$stats = array();
$tabs = array();

//

$stats[] = array(
	'id' => 'NslookupLogs',
	'name' => __('Log History'), 
	'value' => $nslookup['Nslookup']['counts']['NslookupLog.all'], 
	'tab' => array('tabs', '1'), // the tab to display
);

$tabs[] = array(
	'key' => 'NslookupsNslookupLogs',
	'title' => __('Log History'), 
	'url' => array('controller' => 'nslookup_logs', 'action' => 'nslookup', $nslookup['Nslookup']['id']),
);

echo $this->element('Utilities.page_view', array(
//	'page_title' => __('Nslookup'). ': '. $nslookup['Nslookup']['id'],
	'page_title' => $page_title,
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>

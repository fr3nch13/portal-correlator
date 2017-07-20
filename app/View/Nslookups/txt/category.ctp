<?php 
// File: app/View/Nslookups/txt/category.ctp

$data = array();
foreach ($nslookups as $i => $nslookup)
{
	$data[] = $nslookup['VectorHostname']['vector']. ' - '. $nslookup['VectorIpaddress']['vector'];
}
echo implode("\n", $data);
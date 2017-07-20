<?php 
// File: app/View/Vectors/admin_multiselect_multidnstracking.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'dns_auto_lookup_select' => array('content' => __('Select DNS Tracking Level'), 'options' => array('class' => 'actions')),
	'dns_auto_lookup' => array('content' => __('DNS Tracking')),
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$actions = '';
	$dns_auto_lookup = '';
	if(isset($vector['Ipaddress']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['dns_auto_lookup'], true);
		$actions = $this->Form->input('Ipaddress.'.$i.'.id', array('type' => 'hidden', 'value' => $vector['Ipaddress']['id']));
		$actions .= $this->Form->input('Ipaddress.'.$i.'.dns_auto_lookup', array(
			'div' => false,
			'label' => false,
			'options' => $this->Wrap->dnsAutoLookupLevelOptions(false),
			'selected' => $vector['Ipaddress']['dns_auto_lookup'],
		));
	}
	if(isset($vector['Hostname']['id']))
	{
		$dns_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['dns_auto_lookup'], true);
		$actions = $this->Form->input('Hostname.'.$i.'.id', array('type' => 'hidden', 'value' => $vector['Hostname']['id']));
		$actions .= $this->Form->input('Hostname.'.$i.'.dns_auto_lookup', array(
			'div' => false,
			'label' => false,
			'options' => $this->Wrap->dnsAutoLookupLevelOptions(false),
			'selected' => $vector['Hostname']['dns_auto_lookup'],
		));
	}

	$td[$i] = array(
		$vector['Vector']['vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$dns_auto_lookup,
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('Vector', array('url' => array('action' => 'multiselect_multidnstracking')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select DNS Tracking for these Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
	));
?>
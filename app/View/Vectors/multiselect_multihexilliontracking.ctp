<?php 
// File: app/View/Vectors/multiselect_multihexilliontracking.ctp

// content
$th = array(
	'Vector.vector' => array('content' => __('Vector'), 'options' => array('sort' => 'Vector.vector')),
	'hexillion_auto_lookup_select' => array('content' => __('Select Hexillion Tracking Level'), 'options' => array('class' => 'actions')),
	'hexillion_auto_lookup' => array('content' => __('Hexillion Tracking')),
);

$td = array();
foreach ($vectors as $i => $vector)
{
	$actions = '';
	$hexillion_auto_lookup = '';
	if(isset($vector['Ipaddress']['id']))
	{
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Ipaddress']['hexillion_auto_lookup'], true);
		$actions = $this->Form->input('Ipaddress.'.$i.'.id', array('type' => 'hidden', 'value' => $vector['Ipaddress']['id']));
		$actions .= $this->Form->input('Ipaddress.'.$i.'.hexillion_auto_lookup', array(
			'div' => false,
			'label' => false,
			'options' => $this->Wrap->dnsAutoLookupLevelOptions(false),
			'selected' => $vector['Ipaddress']['hexillion_auto_lookup'],
		));
	}
	if(isset($vector['Hostname']['id']))
	{
		$hexillion_auto_lookup = $this->Wrap->dnsAutoLookupLevel($vector['Hostname']['hexillion_auto_lookup'], true);
		$actions = $this->Form->input('Hostname.'.$i.'.id', array('type' => 'hidden', 'value' => $vector['Hostname']['id']));
		$actions .= $this->Form->input('Hostname.'.$i.'.hexillion_auto_lookup', array(
			'div' => false,
			'label' => false,
			'options' => $this->Wrap->dnsAutoLookupLevelOptions(false),
			'selected' => $vector['Hostname']['hexillion_auto_lookup'],
		));
	}

	$td[$i] = array(
		$vector['Vector']['vector'],
		array(
			$actions,
			array('class' => 'actions'),
		),
		$hexillion_auto_lookup,
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('Vector', array('url' => array('action' => 'multiselect_multihexilliontracking')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select Hexillion Tracking for these Vectors'),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
));
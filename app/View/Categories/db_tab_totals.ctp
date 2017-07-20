<?php

// tab-hijack
$page_options = [];
$page_options['org'] = $this->Html->link(__('By %s', __('ORG/IC')), ['action' => 'db_tab_totals', 'org', $as_block], ['class' => 'tab-hijack block-hijack']);
$page_options['division'] = $this->Html->link(__('By %s', __('Division')), ['action' => 'db_tab_totals', 'division', $as_block], ['class' => 'tab-hijack block-hijack']);
$page_options['branch'] = $this->Html->link(__('By %s', __('Branch')), ['action' => 'db_tab_totals', 'branch', $as_block], ['class' => 'tab-hijack block-hijack']);
$page_options['sac'] = $this->Html->link(__('By %s', __('SAC')), ['action' => 'db_tab_totals', 'sac', $as_block], ['class' => 'tab-hijack block-hijack']);
$page_options['owner'] = $this->Html->link(__('By %s', __('System Owner')), ['action' => 'db_tab_totals', 'owner', $as_block], ['class' => 'tab-hijack block-hijack']);
$page_options['system'] = $this->Html->link(__('By %s', __('FISMA System')), ['action' => 'db_tab_totals', 'system', $as_block], ['class' => 'tab-hijack block-hijack']);

$subtitle = __('All Results');

$barStatsLabels = [
	'name' => $scopeName,
	'total' => __('Categories'),
];

$th = [];
$th['path'] = ['content' => __('Path')];
$th['name'] = ['content' => $scopeName];
$th['total'] = ['content' => __('Total')];

$barStats = [];
$totals = [];
$td = [];
foreach($results as $resultId => $result)
{
	$td[$resultId] = [];
	
	$td[$resultId]['path'] = false;
	if(isset($result['object']))
		$td[$resultId]['path'] = $this->Contacts->makePath($result['object']);
	
	$td[$resultId]['name'] = $this->Html->link($result['name'], $result['url']);
	$td[$resultId]['total'] = count($result['Categories']);
	
	$barStats[$resultId] = [
		'name' => $result['name'],
		'total' => $td[$resultId]['total'],
	];
	
	if(!isset($totals['total'])) $totals['total'] = 0;
	$totals['total'] = ($totals['total'] + $td[$resultId]['total']);
}

$totals_row = [];
if(isset($resultId) and isset($td[$resultId]))
{
	$line_count = 0;
	$totals_row['path'] = __('Totals:');
	$totals_row['name'] = count($td);
	foreach($td[$resultId] as $k => $v)
	{
		//$totals_row[$k] = false;
		if(isset($totals[$k]))
			$totals_row[$k] = $totals[$k];
		
		if(!isset($totals_row[$k]))
			$totals_row[$k] = 0;
		
		if($totals_row[$k])
			$totals_row[$k] = array(
				$totals_row[$k],
				array('class' => 'highlight bold'),
			);
	}
	if(is_int($resultId))
		array_push($td, $totals_row);
	else
		$td['totals'] = $totals_row;
}

if($as_block)
{
	$j = 0;
	$barStats = Hash::sort($barStats, '{n}.total', 'desc');
	$bar_data = [];
	foreach($barStats as $i => $stat)
	{
		$k = 0;
		
		$bar_data[$j] = [$stat['name'], $stat['total']];
		
		$barStats[$i]['value'] = $stat['total'];
		
		$j++;
	}
	
	$content = $this->element('Utilities.object_dashboard_chart_bar', [
		'title' => '',
		'data' => ['legend' => $barStatsLabels, 'data' => $bar_data],
	]);

	echo $this->element('Utilities.object_dashboard_block', [
		'title' => __('%s - %s grouped by %s', __('Categories'), __('Totals'), $scopeName),
		'subtitle' => $subtitle,
		'description' => __('Excluding items that have a 0 count. Based on %s related by %s', __('Categories'), $scopeName),
		'content' => $content,
		'page_options_title' => __('Change Scope'),
		'page_options' => $page_options,
	]);
}
else
{
	echo $this->element('Utilities.page_index', [
		'page_title' => __('%s - Counts', __('Categories')),
		'page_subtitle' => __('%s, grouped by %s', $subtitle, $scopeName),
		'page_options_title' => __('Change Scope'),
		'page_options' => $page_options,
		'th' => $th,
		'td' => $td,
		'use_pagination' => false,
		'use_search' => false,
	]);
}
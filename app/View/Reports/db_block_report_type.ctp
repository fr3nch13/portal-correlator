<?php

$stats = array(
	'total' => array('name' => __('Total with a %s Assigned', __('Report Group')), 'value' => count($reports), 'color' => 'FFFFFF'),
);

foreach($reportTypes as $reportType)
{
	$id = $reportType['ReportType']['id'];
	$stats['ReportType.'.$id] = array(
		'name' => $reportType['ReportType']['name'],
		'value' => 0,
		'color' => substr(md5($reportType['ReportType']['name']), 0, 6),
	);
}

foreach($reports as $report)
{
	if($report['ReportType']['id'])
	{
		$report_type_id = $report['ReportType']['id'];
		if(!isset($stats['ReportType.'.$report_type_id]))
		{
			$stats['ReportType.'.$report_type_id] = array(
				'name' => $report['ReportType']['name'],
				'value' => 0,
				'color' => substr(md5($report['ReportType']['name']), 0, 6),
				
			);
		}
		$stats['ReportType.'.$report_type_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = array(array(__('Report Group'), __('num %s', __('Reports')) ));
$pie_options = array('slices' => array());
foreach($stats as $i => $stat)
{
	if($i == 'total')
	{
		$stats[$i]['pie_exclude'] = true;
		$stats[$i]['color'] = 'FFFFFF';
		continue;
	}
	if(!$stat['value'])
	{
		unset($stats[$i]);
		continue;
	}
	$pie_data[] = array($stat['name'], $stat['value'], $i);
	$pie_options['slices'][] = array('color' => '#'. $stat['color']);
}

$content = $this->element('Utilities.object_dashboard_chart_pie', array(
	'title' => '',
	'data' => $pie_data,
	'options' => $pie_options,
));

$content .= $this->element('Utilities.object_dashboard_stats', array(
	'title' => '',
	'details' => $stats,
));

echo $this->element('Utilities.object_dashboard_block', array(
	'title' => __('%s by %s', __('Reports'), __('Report Group') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
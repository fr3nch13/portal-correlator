<?php

$topNum = (isset($topNum)?$topNum:10);

$stats = (isset($stats)?$stats:array());
$stats = array_slice($stats, 0, $topNum);

$pie_data = array(array(__('Type'), __('num %s', __('Vectors')) ));
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
	$pie_options['slices'][] = array('color' => '#'. substr(md5($stat['name']), 0, 6));
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
	'title' => __('%s by Top %s %s', __('Vectors'), $topNum, __('Types') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
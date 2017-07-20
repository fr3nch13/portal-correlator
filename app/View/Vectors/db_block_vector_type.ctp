<?php

$topNum = (isset($topNum)?$topNum:10);

$stats = array(
	'total' => array('name' => __('Total with a %s Assigned', __('Vector Group')), 'value' => count($vectors), 'color' => 'FFFFFF'),
);


foreach($vectorTypes as $vectorType)
{
	$id = $vectorType['VectorType']['id'];
	$stats['VectorType.'.$id] = array(
		'name' => $vectorType['VectorType']['name'],
		'value' => 0,
		'color' => substr(md5($vectorType['VectorType']['name']), 0, 6),
	);
}

foreach($vectors as $vector)
{
	if($vector['VectorType']['id'])
	{
		$vector_type_id = $vector['VectorType']['id'];
		if(!isset($stats['VectorType.'.$vector_type_id]))
		{
			$stats['VectorType.'.$vector_type_id] = array(
				'name' => $vector['VectorType']['name'],
				'value' => 0,
				'color' => substr(md5($vector['VectorType']['name']), 0, 6),
				
			);
		}
		$stats['VectorType.'.$vector_type_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');
$stats = array_slice($stats, 0, $topNum);

$pie_data = array(array(__('Vector Group'), __('num %s', __('Vectors')) ));
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
	'title' => __('%s by Top %s %s', __('Vectors'), $topNum, __('Vector Groups') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
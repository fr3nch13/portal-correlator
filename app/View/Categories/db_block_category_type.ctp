<?php

$stats = array(
	'total' => array('name' => __('Total with a %s Assigned', __('Category Type')), 'value' => count($categories), 'color' => 'FFFFFF'),
);

foreach($categoryTypes as $categoryType)
{
	$id = $categoryType['CategoryType']['id'];
	$stats['CategoryType.'.$id] = array(
		'name' => $categoryType['CategoryType']['name'],
		'value' => 0,
		'color' => substr(md5($categoryType['CategoryType']['name']), 0, 6),
	);
}

foreach($categories as $category)
{
	if($category['CategoryType']['id'])
	{
		$category_type_id = $category['CategoryType']['id'];
		if(!isset($stats['CategoryType.'.$category_type_id]))
		{
			$stats['CategoryType.'.$category_type_id] = array(
				'name' => $category['CategoryType']['name'],
				'value' => 0,
				'color' => substr(md5($category['CategoryType']['name']), 0, 6),
				
			);
		}
		$stats['CategoryType.'.$category_type_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = array(array(__('Category Type'), __('num %s', __('Categories')) ));
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
	'title' => __('%s by %s', __('Categories'), __('Category Type') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
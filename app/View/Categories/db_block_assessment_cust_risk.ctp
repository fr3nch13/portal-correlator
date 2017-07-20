<?php

$stats = array(
	'total' => array('name' => __('Total with a %s Assigned', __('Customer Risk')), 'value' => count($categories), 'color' => 'FFFFFF'),
);


foreach($assessmentCustRisks as $assessmentCustRisk)
{
	$id = $assessmentCustRisk['AssessmentCustRisk']['id'];
	$stats['AssessmentCustRisk.'.$id] = array(
		'name' => $assessmentCustRisk['AssessmentCustRisk']['name'],
		'value' => 0,
		'color' => str_replace('#', '', $assessmentCustRisk['AssessmentCustRisk']['color_code_hex']),
	);
}

foreach($categories as $category)
{
	if($category['AssessmentCustRisk']['id'])
	{
		$assessment_cust_risk_id = $category['AssessmentCustRisk']['id'];
		if(!isset($stats['AssessmentCustRisk.'.$assessment_cust_risk_id]))
		{
			$stats['AssessmentCustRisk.'.$assessment_cust_risk_id] = array(
				'name' => $category['AssessmentCustRisk']['name'],
				'value' => 0,
				'color' => str_replace('#', '', $category['AssessmentCustRisk']['color_code_hex']),
				
			);
		}
		$stats['AssessmentCustRisk.'.$assessment_cust_risk_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');
$pie_data = array(array(__('Assessment %s', __('Customer Risk')), __('num %s', __('Categories')) ));
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
	'title' => __('%s by %s', __('Categories'), __('Assessment %s', __('Customer Risk')) ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
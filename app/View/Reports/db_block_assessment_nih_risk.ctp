<?php

$stats = array(
	'total' => array('name' => __('Total with a %s Assigned', __('NIH Risk')), 'value' => count($reports), 'color' => 'FFFFFF'),
);


foreach($assessmentNihRisks as $assessmentNihRisk)
{
	$id = $assessmentNihRisk['AssessmentNihRisk']['id'];
	$stats['AssessmentNihRisk.'.$id] = array(
		'name' => $assessmentNihRisk['AssessmentNihRisk']['name'],
		'value' => 0,
		'color' => str_replace('#', '', $assessmentNihRisk['AssessmentNihRisk']['color_code_hex']),
	);
}

foreach($reports as $report)
{
	if($report['AssessmentNihRisk']['id'])
	{
		$assessment_nih_risk_id = $report['AssessmentNihRisk']['id'];
		if(!isset($stats['AssessmentNihRisk.'.$assessment_nih_risk_id]))
		{
			$stats['AssessmentNihRisk.'.$assessment_nih_risk_id] = array(
				'name' => $report['AssessmentNihRisk']['name'],
				'value' => 0,
				'color' => str_replace('#', '', $report['AssessmentNihRisk']['color_code_hex']),
				
			);
		}
		$stats['AssessmentNihRisk.'.$assessment_nih_risk_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');
$pie_data = array(array(__('Assessment %s', __('NIH Risk')), __('num %s', __('Reports')) ));
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
	'title' => __('%s by %s', __('Reports'), __('Assessment %s', __('NIH Risk')) ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
));
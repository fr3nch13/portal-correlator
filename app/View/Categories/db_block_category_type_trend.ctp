<?php

$line_options = ['colors' => []];
if(isset($snapshotStats['legend']))
{
	foreach($snapshotStats['legend'] as $key => $name)
	{
		$snapshotStats['legend'][$key] = __('With Type: %s', $name);
		$color = '#'. substr(md5($name), 0, 6);
		if(strpos($key, '-'))
		{
			$parts = explode('-', $key);
			$key_id = array_pop($parts);
			if(isset($colors[$key_id]))
				$color = $colors[$key_id];
			$line_options['colors'][] = $color;
		}
	}
}

$content = $this->element('Utilities.object_dashboard_chart_line', [
	'title' => '',
	'data' => $snapshotStats,
	'options' => $line_options,
]);

echo $this->element('Utilities.object_dashboard_block', [
	'title' => $this->Html->link(__('%s - By %s Trending', __('Categories'), __('Type')), ['action' => 'dashboard']),
	'content' => $content,
]);
<?php

$prefix = false;
if(isset($this->request->params['prefix']) and $this->request->params['prefix'])
	$prefix = $this->request->params['prefix'];

$stats = [
	'total' => ['name' => __('Total'), 'value' => count($results)],
];

if(!in_array($prefix, ['director']))
{
	$stats['hasFismaSystem'] = ['name' => __('With %s', __('FISMA Systems')), 'value' => 0];
	$stats['hasRogueFismaSystem'] = ['name' => __('With Rogue %s', __('FISMA Systems')), 'value' => 0];
	$stats['hasNotFismaSystem'] = ['name' => __('Without %s', __('FISMA Systems')), 'value' => 0];
	$stats['hasManyFismaSystem'] = ['name' => __('Multiple %s', __('FISMA Systems')), 'value' => 0];
}

foreach($results as $result)
{
	if(!in_array($prefix, ['director']))
	{
		$fismaSystemIds = [];
		$inRogue = false;
		if(count($result['FismaInventories']))
		{
			foreach($result['FismaInventories'] as $fismaInventory)
			{
				if(isset($fismaInventory['FismaSystem']['id']))
				{
					$fismaSystemIds[$fismaInventory['FismaSystem']['id']] = $fismaInventory['FismaSystem']['id'];
					if(isset($fismaInventory['FismaSystem']['is_rogue']) and $fismaInventory['FismaSystem']['is_rogue'] == 2)
						$inRogue = true;
				}
			}
		}
		
		$fismaCount = count($fismaSystemIds);
		if($fismaCount)
		{
			$stats['hasFismaSystem']['value']++;
			if($fismaCount > 1)
				$stats['hasManyFismaSystem']['value']++;
		}
		else
			$stats['hasNotFismaSystem']['value']++;
		
		if($inRogue)
			$stats['hasRogueFismaSystem']['value']++;
	}
}

if(!in_array($prefix, ['director']))
{
	if($stats['hasRogueFismaSystem']['value'])
		$stats['hasRogueFismaSystem']['class'] = 'notice';
	
	if($stats['hasNotFismaSystem']['value'])
		$stats['hasNotFismaSystem']['class'] = 'warning';
	
	if($stats['hasManyFismaSystem']['value'])
		$stats['hasManyFismaSystem']['class'] = 'warning';
}

$content = $this->element('Utilities.object_dashboard_stats', [
	'title' => false,
	'details' => $stats,
]);

echo $this->element('Utilities.object_dashboard_block', [
	'title' => $this->Html->link(__('%s - Overview', __('Reports')), ['action' => 'dashboard']),
	'content' => $content,
]);
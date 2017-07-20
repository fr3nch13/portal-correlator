<?php ?>
<!-- File: app/View/Elements/assign_vttracking.ctp -->
<div class="top">
	<h1><?php echo __('Assign %s Tracking to all %s', __('VirusTotal'), Inflector::pluralize($parent2Name)); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create($thisName, array('url' => array('action' => 'assign_vttracking', $id)));?>
	    <fieldset>
	        <legend><?php echo __('Assign %s Tracking to all %s', __('VirusTotal'), Inflector::pluralize($parent2Name)); ?></legend>
	    	<?php
				echo $this->Form->input('vt_lookup', array(
					'label' => __('%s Tracking Level', __('VirusTotal')),
					'options' => $this->Wrap->vtAutoLookupLevelOptions(false, true),
				));
				echo $this->Form->input('id', array(
					'type' => 'hidden',
					'value' => $id,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

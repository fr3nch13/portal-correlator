<?php ?>
<!-- File: app/View/UploadsVectors/assign_dnstracking.ctp -->
<div class="top">
	<h1><?php echo __('Assign all Vectors to one Group'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('UploadsVector', array('url' => array('action' => 'assign_dnstracking', $upload_id)));?>
	    <fieldset>
	        <legend><?php echo __('Assign DNS Tracking to all Vectors'); ?></legend>
	    	<?php
				echo $this->Form->input('dns_auto_lookup', array(
					'label' => __('DNS Tracking Level'),
					'options' => $this->Wrap->dnsAutoLookupLevelOptions(false, true),
				));
				echo $this->Form->input('upload_id', array(
					'type' => 'hidden',
					'value' => $upload_id,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

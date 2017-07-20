<?php ?>
<!-- File: app/View/UploadsVectors/admin_assign_whoistracking.ctp -->
<div class="top">
	<h1><?php echo __('Assign WHOIS Tracking to all Vectors'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('UploadsVector', array('url' => array('action' => 'assign_whoistracking', $upload_id)));?>
	    <fieldset>
	        <legend><?php echo __('Assign WHOIS Tracking to all Vectors'); ?></legend>
	    	<?php
				echo $this->Form->input('whois_auto_lookup', array(
					'label' => __('WHOIS Tracking Level'),
					'options' => $this->Wrap->whoisAutoLookupLevelOptions(false, true),
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

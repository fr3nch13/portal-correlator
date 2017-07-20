<?php
// File: app/View/Whois/edit.ctp 
?>
<div class="top">
	<h1><?php echo __('Edit Whois Settings for: %s', $this->data['Vector']['vector']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Whois');?>
		    <fieldset>
		        <legend><?php echo __('Edit Whois Settings'); ?></legend>
		    	<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
					echo $this->Form->input('whois_auto_lookup', array(
						'label' => __('Whois Tracking Level'),
						'options' => $this->Wrap->whoisAutoLookupLevelOptions(),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Whois Settings')); ?>
	</div>
</div>

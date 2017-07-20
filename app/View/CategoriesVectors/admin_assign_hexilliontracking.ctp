<?php 
// <!-- File: app/View/CategoriesVectors/admin_assign_hexilliontracking.ctp -->
?>
<div class="top">
	<h1><?php echo __('Assign Hexillion Tracking to all Vectors'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create(); ?>
	    <fieldset>
	        <legend><?php echo __('Assign Hexillion Tracking to all Vectors'); ?></legend>
	    	<?php
				echo $this->Form->input('hexillion_auto_lookup', array(
					'label' => __('Hexillion Tracking Level'),
					'options' => $this->Wrap->dnsAutoLookupLevelOptions(false, true),
				));
				echo $this->Form->input('category_id', array(
					'type' => 'hidden',
					'value' => $category_id,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

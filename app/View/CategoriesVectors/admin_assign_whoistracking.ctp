<?php ?>
<!-- File: app/View/CategoriesVectors/assign_whoistracking.ctp -->
<div class="top">
	<h1><?php echo __('Assign WHOIS Tracking to all Vectors'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('CategoriesVector', array('url' => array('action' => 'assign_whoistracking', $category_id)));?>
	    <fieldset>
	        <legend><?php echo __('Assign WHOIS Tracking to all Vectors'); ?></legend>
	    	<?php
				echo $this->Form->input('whois_auto_lookup', array(
					'label' => __('WHOIS Tracking Level'),
					'options' => $this->Wrap->whoisAutoLookupLevelOptions(false, true),
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

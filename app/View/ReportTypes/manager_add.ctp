<?php ?>
<!-- File: app/View/ReportType/manager_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Report Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ReportType');?>
		    <fieldset>
		        <legend><?php echo __('Add Report Group'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
					
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Report Group')); ?>
	</div>
</div>
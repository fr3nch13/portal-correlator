<?php ?>
<!-- File: app/View/VectorType/admin_admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Vector Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('VectorType');?>
		    <fieldset>
		        <legend><?php echo __('Add Vector Group'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Vector Group')); ?>
	</div>
</div>
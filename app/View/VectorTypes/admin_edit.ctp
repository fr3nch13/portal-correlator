<?php ?>
<!-- File: app/View/VectorType/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Vector Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('VectorType');?>
		    <fieldset>
		        <legend><?php echo __('Edit Vector Group'); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Vector Group')); ?>
	</div>
</div>
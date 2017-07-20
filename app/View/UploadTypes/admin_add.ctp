<?php ?>
<!-- File: app/View/UploadType/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add File Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('UploadType');?>
		    <fieldset>
		        <legend><?php echo __('Add File Group'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
					echo $this->Form->input('org_group_id', array(
						'label' => 'Org Group',
						'empty' => __('[ Global ]'),
					));
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save File Group')); ?>
	</div>
</div>
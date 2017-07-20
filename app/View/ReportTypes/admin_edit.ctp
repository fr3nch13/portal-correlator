<?php ?>
<!-- File: app/View/ReportType/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Report Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ReportType');?>
		    <fieldset>
		        <legend><?php echo __('Edit Report Group'); ?></legend>
		    	<?php
					echo $this->Form->input('id');
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
		<?php echo $this->Form->end(__('Save Report Group')); ?>
	</div>
</div>
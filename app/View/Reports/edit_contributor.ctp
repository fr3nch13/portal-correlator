<?php
// File: app/View/Reports/edit_contributor.ctp 
?>
<div class="top">
	<h1><?php echo __('Edit Report: %s', $this->data['Report']['name']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Report');?>
			<fieldset>
				<legend><?php echo __('Edit Report'); ?></legend>
				<?php
					echo $this->Form->input('id', array(
							'type' => 'hidden'
							));
//					echo $this->Form->input('name');
					echo $this->Form->input('ReportsDetail.id', array(
							'type' => 'hidden'
							));
					echo $this->Form->input('ReportsDetail.desc', array(
						'label' => __('Append to Existing Description'),
						'value' => false,
					));
					
					echo $this->Tag->autocomplete('tags', array(
						'label' => __('Append to Existing Tags'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update Report')); ?>
	</div>
</div>

<?php ?>
<!-- File: app/View/TempReportsVectors/assign_vector_type.ctp -->
<div class="top">
	<h1><?php echo __('Assign all Vectors to one Group'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('TempReportsVector', array('url' => array('action' => 'assign_vector_type', $temp_report_id)));?>
	    <fieldset>
	        <legend><?php echo __('Assign all Vectors to one Group'); ?></legend>
	    	<?php
				echo $this->Form->input('vector_type_id', array(
					'label' => 'Vector Group',
					'empty' => __('[ None ]'),
				));
				echo $this->Form->input('only_unassigned', array(
					'label' => 'Which Vectors should be assigned?',
					'options' => array(
						0 => __('All'),
						1 => __('Only Unassigned'),
					),
				));
				echo $this->Form->input('temp_report_id', array(
					'type' => 'hidden',
					'value' => $temp_report_id,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

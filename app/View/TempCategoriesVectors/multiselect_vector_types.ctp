<?php ?>
<!-- File: app/View/TempCategoriesVectors/multiselect_vector_types.ctp -->
<div class="top">
	<h1><?php echo __('Assign Temp Vectors to Group'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('TempCategoriesVector', array('url' => array('action' => 'multiselect_vector_types')));?>
	    <fieldset>
	        <legend><?php echo __('Assign Temp Vectors to Group'); ?></legend>
	    	<?php
				echo $this->Form->input('vector_type_id', array(
					'label' => 'Vector Group',
					'empty' => __('[ None ]'),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

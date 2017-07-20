<?php ?>
<!-- File: app/View/TempCategoriesVectors/add.ctp -->
<div class="top">
	<h1><?php echo __('Add Vectors'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('TempCategoriesVector', array('url' => array('action' => 'add', $temp_category_id)));?>
	    <fieldset>
	        <legend><?php echo __('Add Vectors'); ?></legend>
	    	<?php
				echo $this->Form->input('temp_category_id', array(
					'value' => $temp_category_id,
					'type' => 'hidden',
				));
				echo $this->Form->input('temp_vectors', array(
					'type' => 'textarea',
					'label' => array(
						'text' => __('Vectors'),
						'class' => 'tipsy',
						'title' => __('Separate each one with a new line. Valid types are: host names, ip addresses, hashes, urls, alphanumeric values'),
					),
					'between' => __('These are case sensitive.'),
				));
				echo $this->Form->input('vector_type_id', array(
	        		'label' => __('Vector Group'),
					'empty' => __('[ None ]'),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save Vectors')); ?>
	</div>
</div>

<?php
// File: app/View/Categories/edit_contributor.ctp 
?>
<div class="top">
	<h1><?php echo __('Edit Category: %s', $this->data['Category']['name']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Category');?>
			<fieldset>
				<legend><?php echo __('Edit Category'); ?></legend>
				<?php
					echo $this->Form->input('id', array(
							'type' => 'hidden'
							));
//					echo $this->Form->input('name');
					echo $this->Form->input('CategoriesDetail.id', array(
							'type' => 'hidden'
							));
					echo $this->Form->input('CategoriesDetail.desc', array(
						'label' => __('Append to Existing Description'),
						'value' => false,
					));
					
					echo $this->Tag->autocomplete('tags', array(
						'label' => __('Append to Existing Tags'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update Category')); ?>
	</div>
</div>

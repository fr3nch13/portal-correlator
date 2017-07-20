<?php ?>
<!-- File: app/View/Vectors/multiselect_vector_types.ctp -->
<div class="top">
	<h1><?php echo __('Assign Vectors to Group'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Vector', array('url' => array('action' => 'multiselect_vector_types')));?>
	    <fieldset>
	        <legend><?php echo __('Assign Vectors to Group'); ?></legend>
	    	<?php
				echo $this->Form->input('vector_type_id', array(
					'label' => 'Vector Group',
					'empty' => __('[ None ]'),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
<?php
if(isset($selected_vectors) and $selected_vectors)
{
	$details = array();
	foreach($selected_vectors as $selected_vector)
	{
		$details[] = array('name' => __('Vector: '), 'value' => $selected_vector);
	}
	echo $this->element('Utilities.details', array(
			'title' => __('Selected Vectors. Count: %s', count($selected_vectors)),
			'details' => $details,
		));
}
?>
</div>

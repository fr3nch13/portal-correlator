<?php 
// File: app/View/Vectors/multiselect_vectortype.ctp
$vcount = 0;
if(isset($sessionData['multiple']))
{
	foreach($sessionData['multiple'] as $selected) 
	{
		if(!$selected) continue;
		$vcount++;
	}
}
?>
<div class="top">
	<h1><?php echo __('Select Detected Type for %s Vectors', (isset($selected_vectors)?count($selected_vectors): (isset($vcount)?$vcount:0) )); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Vector', array('url' => array('action' => 'multiselect_vectortype')));?>
	    <fieldset>
	        <legend><?php echo __('Select Detected Type for %s Vectors', $vcount); ?></legend>
	    	<?php
				echo $this->Form->input('type', array(
					'label' => __('Vector Type'),
					'options' => $types,
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

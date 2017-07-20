<?php ?>
<!-- File: app/View/Vectors/multiselect_vttracking.ctp -->
<div class="top">
	<h1><?php echo __('Assign %s Tracking to %s', __('VirusTotal'), __('Vectors')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Vector', array('url' => array('action' => 'multiselect_vttracking')));?>
	    <fieldset>
	        <legend><?php echo __('Assign %s Tracking to %s', __('VirusTotal'), __('Vectors')); ?></legend>
	    	<?php
				echo $this->Form->input('vt_lookup', array(
					'label' => __('%s Tracking Level', __('VirusTotal')),
					'options' => $this->Wrap->vtAutoLookupLevelOptions(false, true),
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

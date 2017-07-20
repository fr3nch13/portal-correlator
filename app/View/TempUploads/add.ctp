<?php

?>
<div class="top">
	<h1><?php echo __('Add %s %s', __('File'), $this->data['TempUpload']['_title']); ?></h1>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('TempUpload', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add %s %s', __('File'), $this->data['TempUpload']['_title']); ?></legend>
		    	<?php
					echo $this->Form->input('scan', array(
						'type' => 'checkbox',
						'checked' => true,
						'label' => __('Check to scan File for %s', __('Vectors')),
					));
					echo $this->Form->input('file', array(
						'type' => 'file',
					));
					echo $this->Form->input('upload_type_id', array(
						'label' => __('File Group'),
						'empty' => __('[ None ]'),
					));
					echo $this->AutoComplete->input('mysource', array(
						'label' => __('User Source'),
						'autoCompleteUrl'=>$this->Html->url(array(
							'admin' => false,
							'controller' => 'uploads',
							'action'=>'auto_complete',
							'mysource'
						)),
						'autoCompleteRequestItem'=>'autoCompleteText',
					));
					echo $this->Form->input('desc_private', array(
						'label' => __('Private Notes'),
					));
					echo $this->Tag->autocomplete();
					echo $this->Form->input('temp_category_id', array('type' => 'hidden'));
					echo $this->Form->input('temp_report_id', array('type' => 'hidden'));
					echo $this->Form->input('category_id', array('type' => 'hidden'));
					echo $this->Form->input('report_id', array('type' => 'hidden'));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Upload %s', __('File'))); ?>
	</div>
</div>

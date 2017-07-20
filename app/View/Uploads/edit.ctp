<?php
// File: app/View/Uploads/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit File: %s', $this->data['Upload']['filename']); ?></h1>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
	    	<fieldset>
	    	    <legend><?php echo __('Edit File'); ?></legend>
	    		<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
	    	    	echo $this->Form->input('public', array(
						'type' => 'select',
						'options' => $this->Wrap->publicStateOptions(),
						'default' => 1,
						'label' => array(
							'text' => __('Share State'),
							'class' => 'tipsy',
							'title' => __('If other users can see this file, or just you.'),
						)
					));
					
					echo $this->Form->input('upload_type_id', array(
						'label' => 'File Group',
						'empty' => __('[ None ]'),
					));
					
					echo $this->AutoComplete->input('Upload.mysource', array(
						'label' => __('User Source'),
						'autoCompleteUrl'=>$this->Html->url(array(
							'admin' => false,
							'action'=>'auto_complete',
							'mysource'
						)),
						'autoCompleteRequestItem'=>'autoCompleteText',
					));
					
					echo $this->Form->input('Upload.desc_private', array(
						'label' => __('Private Notes'),
					));
					echo $this->Tag->autocomplete();
	    		?>
	    	</fieldset>
		<?php echo $this->Form->end(__('Update File')); ?>
	</div>
</div>

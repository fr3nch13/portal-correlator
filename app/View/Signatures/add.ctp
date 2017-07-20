<?php ?>
<!-- File: app/View/Signatures/add.ctp -->
<div class="top">
	<h1><?php echo __('Add %s', __('Signatures')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Signature');?>
	    <fieldset>
	        <legend><?php echo __('Add %s', __('Signatures')); ?></legend>
	    	<?php
	    		
	    		echo $this->Form->input('Signature.category_id', array(
					'type' => 'hidden',
				));
	    		echo $this->Form->input('Signature.temp_category_id', array(
					'type' => 'hidden',
				));
	    		echo $this->Form->input('Signature.report_id', array(
					'type' => 'hidden',
				));
	    		echo $this->Form->input('Signature.temp_report_id', array(
					'type' => 'hidden',
				));
				
				echo $this->AutoComplete->input('Signature.signature_source', array(
	        		'label' => __('%s Source', __('Signature')),
					'autoCompleteUrl'=>$this->Html->url(array(
						'admin' => false,
						'controller' => 'signature_sources',
						'action'=>'auto_complete',
						'name'
					)),
					'autoCompleteRequestItem'=>'name',
				));
				
				echo $this->Form->input('Signature.signatures', array(
					'type' => 'textarea',
					'label' => array(
						'text' => __('The %s', __('Signatures')),
						'class' => 'tipsy',
						'title' => __('The full text of the %s.', __('Signatures')),
					),
				));
				
				echo $this->Tag->autocomplete(false, array(
					'label' => array(
						'text' => __('Tags'),
						'class' => 'tipsy',
						'title' => __('Comma separated like the Tags in other items.'),
					),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save %s', __('Signatures'))); ?>
	</div>
</div>

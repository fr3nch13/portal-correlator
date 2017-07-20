<?php ?>
<!-- File: app/View/Signature/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit a %s', __('Signature')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Signature');?>
	    <fieldset>
	        <legend><?php echo __('Edit a %s', __('Signature')); ?></legend>
	    	<?php
	    	
	    		echo $this->Form->input('Signature.id', array(
	    			'type' => 'hidden',
	    		));
	    	
	    		echo $this->Form->input('SnortSignature.id', array(
	    			'type' => 'hidden',
	    		));
	    	
	    		echo $this->Form->input('YaraSignature.id', array(
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
					'value' => (isset($this->data['SignatureSource']['name'])?$this->data['SignatureSource']['name']:false),
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
	<?php echo $this->Form->end(__('Update %s', __('Signature'))); ?>
	</div>
</div>

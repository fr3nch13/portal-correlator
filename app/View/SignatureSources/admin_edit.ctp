<?php ?>
<!-- File: app/View/SignatureSource/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit a %s', __('Signature Source')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('SignatureSource');?>
	    <fieldset>
	        <legend><?php echo __('Edit a %s', __('Signature Source')); ?></legend>
	    	<?php
	    	
	    		echo $this->Form->input('SignatureSource.id', array(
	    			'type' => 'hidden',
	    		));
				
				echo $this->Form->input('SignatureSource.name', array(
					'label' => array(
						'text' => __('%s Name', __('Signature Source')),
						'class' => 'tipsy',
						'title' => __('Name for the %s', __('Signature Source'))
					),
					'autoCompleteUrl'=>$this->Html->url(array(
						'admin' => false,
						'controller' => 'signature_sources',
						'action'=>'auto_complete',
						'name'
					)),
					'autoCompleteRequestItem'=>'name',
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Update %s', __('Signature Source'))); ?>
	</div>
</div>

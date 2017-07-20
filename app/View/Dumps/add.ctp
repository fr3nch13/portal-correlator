<?php ?>
<!-- File: app/View/Dumps/add.ctp -->
<div class="top">
	<h1><?php echo __('Add Dump'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Dump', array('type' => 'file'));?>
	    <fieldset>
	        <legend><?php echo __('Add Dump'); ?></legend>
	    	<?php
	    		
				echo $this->Form->input('name');
				
				echo $this->Form->input('DumpsDetail.desc', array(
					'label' => __('Description'),
					'between' => __('This will not be scanned for vectors, use the field below.'),
				));
				echo $this->Form->input('DumpsDetail.dumptext', array(
					'type' => 'textarea',
					'label' => array(
						'text' => __('Dump Text'), 
						'class' => 'tipsy',
						'title' => __('Add text here to be scanned for vectors. You can also upload a file below... Or both.'),
					),
				));
				
				echo $this->Form->input('DumpsDetail.allvectors', array('type' => 'hidden', 'value' => ' '));
				
				echo $this->Form->input('file', array(
					'type' => 'file',
					'between' => __('(Max file size is %s).', $this->Wrap->maxFileSize()),
				));
//				echo $this->Form->input('tags');
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Add Dump')); ?>
	</div>
</div>

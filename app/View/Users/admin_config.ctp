<?php
//pr($fields);
//pr($this->request->data);
?>
<!-- File: app/View/Users/admin_config.ctp -->
<div class="top">
	<h1><?php echo __('Manage the Static Config'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('User');?>
	    <fieldset>
	        <legend><?php echo __('Manage the Static Config'); ?></legend>
	    	<?php
	    		foreach($fields as $key => $settings)
	    		{
	    			echo $this->Form->input($key, $settings);
	    		}
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>
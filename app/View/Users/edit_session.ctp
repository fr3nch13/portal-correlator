<?php ?>

<div class="top">
	<h1><?php echo __('Edit %s', __('Session Settings')); ?></h1>
</div>
<div class="center">
	<div class="form">
	<?php echo $this->Form->create('User', array('url' => array('action' => 'edit_session')));?>
		<fieldset>
			<legend><?php echo __('Session Settings'); ?></legend>
			<?php
				echo $this->Form->input('role', array(
					'label' => __('Active User Role'),
					'after' => $this->Html->para('form_info', __('Change your user role for this session.')),
					'options' => $availableRoles,
					'default' => AuthComponent::user('role'),
				));
				echo $this->Form->input('id', array('type' => 'hidden'));
			?>
		</fieldset>
	<?php echo $this->Form->end(__('Update %s', __('Session Settings')));?>
	</div>
</div>
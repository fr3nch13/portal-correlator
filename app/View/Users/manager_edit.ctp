<?php ?>
<!-- File: app/View/Users/admin_edit.ctp -->

<div class="top">
	<h1><?php echo __('Edit User'); ?></h1>
</div>

<div class="center">
	<div class="tabs">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo __('Edit User Details'); ?></a></li>
<!--				<li><a href="#tabs-2"><?php echo __('Change User Password'); ?></a></li>
				<li><a href="#tabs-3"><?php echo __('Edit Tags'); ?></a></li> -->
			</ul>
			
			<div id="tabs-1">
				<div class="form">
					<?php echo $this->Form->create('User');?>
					<fieldset>
						<legend><?php echo __('Edit User Details'); ?></legend>
						<?php
				echo $this->Form->input('id', array('type' => 'hidden'));
//				echo $this->Form->input('name');
//				echo $this->Form->input('email');
//				echo $this->Form->input('role', array('options' => $this->Wrap->userRoles()));
				echo $this->Form->input('paginate_items', array(
					'between' => $this->Html->para('form_info', __('How many items should show up in a table by default.')),
					'options' => array(
						'10' => '10',
						'25' => '25',
						'50' => '50',
						'100' => '100',
						'150' => '150',
						'200' => '200',
						'500' => __('500 - May Load Slowly'),
						'1000' => __('1000 - May Load Slowly'),
					),
				));
						?>
					</fieldset>
					<?php echo $this->Form->end(__('Save User Details'));?>
				</div>
			</div>
	
<!--
			<div id="tabs-2">
				<div class="form">
				<?php echo $this->Form->create('User', array('url' => array('action' => 'password')));?>
					<fieldset>
						<legend><?php echo __('Change User Password'); ?></legend>
						<?php
							echo $this->Form->input('password', array('type' => 'password'));
							echo $this->Form->input('confirm_password', array('type' => 'password'));
							echo $this->Form->input('id', array('type' => 'hidden'));
						?>
					</fieldset>
				<?php echo $this->Form->end(__('Save User Password'));?>
				</div>
			</div>
-->
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
	$(document).ready(function () {
		$( "#tabs" ).tabs();
	});
//]]>
</script>
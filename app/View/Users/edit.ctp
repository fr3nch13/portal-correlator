<?php 
// File: app/View/Users/edit.ctp
?>

<div class="top">
	<h1><?php echo __('Edit Settings'); ?></h1>
</div>

<div class="center">
	<div class="tabs">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo __('Edit Details'); ?></a></li>
<!--
				<li><a href="#tabs-2"><?php echo __('Change Password'); ?></a></li>
-->
			</ul>
			
			<div id="tabs-1">
				<div class="form">
					<?php echo $this->Form->create('User');?>
					<fieldset>
						<!--<legend><?php echo __('Edit Details'); ?></legend>-->
					<?php
				echo $this->Form->input('id', array('type' => 'hidden'));
//				echo $this->Form->input('name');
//				echo $this->Form->input('email');
//				echo $this->Form->input('role', array('options' => $this->Wrap->userRoles()));
	        				
	        				
	        			if($this->request->data['User']['role'] == 'admin')
	        			{
							echo $this->Form->input('admin_emails', array(
								'label' => array(
									'text' => __('Receive Admin Emails?'),
									'class' => 'tipsy',
									'title' => __('If they\'re an Admin Role, can they receive system emails (issues/notices)?'),
								),
							));
						}
							
						echo $this->Form->input('paginate_items', array(
							'between' => $this->Html->para('form_info', __('How many items should show up in a table by default.')),
							'options' => array(
								'10' => __('10'),
								'25' => __('25'),
								'50' => __('50'),
								'100' => __('100'),
								'150' => __('150'),
								'200' => __('200'),
								'500' => __('500 - May Load Slowly'),
								'1000' => __('1000 - May Load Slowly'),
							),
						));
					?>
					</fieldset>
					<?php echo $this->Form->end(__('Save Details'));?>
				</div>
			</div>
			
<!--
			<div id="tabs-2">
				<div style="text-align:center;">
					<h3><?php 
						$accounts_link = Configure::read('OAuth.serverURI');
						$accounts_link .= '/users/edit?referer='. urlencode($this->Html->url(null, true));
						$accounts_link .= '#tabs-2';
						echo __(
							'To change your password, please visit the %s app.', 
							$this->Html->link(__('Accounts'), $accounts_link)
						);
					?></h3>
				</div>
			</div>
-->
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
	$(document).ready(function () {
		$( "#tabs" ).tabs({select:function (event, ui) {window.location.hash = ui.tab.hash;}});
	});
//]]>
</script>
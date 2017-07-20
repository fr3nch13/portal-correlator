<?php ?>
<!-- File: app/View/Users/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add User'); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('User');?>
	    <fieldset>
	        <legend><?php echo __('Add User'); ?></legend>
	    	<?php
				echo $this->Form->input('name', array('default' => ''));
				echo $this->Form->input('email', array('default' => ''));
				echo $this->Form->input('password', array('default' => ''));
				echo $this->Form->input('active');
				echo $this->Form->input('role', array(
					'options' => Configure::read('App.user_roles'),
					'selected' => 'regular',
				));
	        				
							echo $this->Form->input('admin_emails', array(
								'label' => array(
									'text' => __('Receive Admin Emails?'),
									'class' => 'tipsy',
									'title' => __('If they\'re an Admin Role, can they receive system emails (issues/notices)?'),
								),
							));
					
				echo $this->Form->input('User.org_group_id', array(
					'label' => array(
						'text' => __('Org Group'),
					),
					'options' => $org_groups,
					'empty' => __('None'),
				));
						echo $this->Form->input('paginate_items', array(
								'label' => array(
									'text' => __('Paginate Items'),
									'class' => 'tipsy',
									'title' => __('How many items should show up in a list by default.'),
								),
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
							'default' => '25',
						));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save User')); ?>
	</div>
</div>
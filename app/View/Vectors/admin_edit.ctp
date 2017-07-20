<?php
// File: app/View/Vectors/admin_edit.ctp 
?>
<div class="top">
	<h1><?php echo __('Edit Vector: %s', $this->data['Vector']['vector']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Vector');?>
		    <fieldset>
		        <legend><?php echo __('Edit Vector'); ?></legend>
		    	<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
					echo $this->Form->input('vector', array(
						'type' => 'hidden'
					));
					echo $this->Form->input('vector_type_id', array(
						'label' => 'Vector Group',
						'empty' => __('[ None ]'),
					));
					if($this->data['Hostname']['id'])
					{
						echo $this->Form->input('Hostname.id', array(
							'type' => 'hidden'
						));
						echo $this->Form->input('Hostname.dns_auto_lookup', array(
							'label' => __('DNS Tracking Level'),
							'options' => $this->Wrap->dnsAutoLookupLevelOptions(),
						));
					}
					elseif($this->data['Ipaddress']['id'])
					{
						echo $this->Form->input('Ipaddress.id', array(
							'type' => 'hidden'
						));
						echo $this->Form->input('Ipaddress.dns_auto_lookup', array(
							'label' => __('DNS Tracking Level'),
							'options' => $this->Wrap->dnsAutoLookupLevelOptions(),
						));
					}
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Vector')); ?>
	</div>
</div>

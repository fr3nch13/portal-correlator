<?php 
// File: app/View/ReportsVectors/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Add Vectors to the Report: %s', $report_name); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('ReportsVector', array('url' => array('action' => 'add', $report_id)));?>
	    <fieldset>
	        <legend><?php echo __('Add Vectors to the Report: %s', $report_name); ?></legend>
	    	<?php
				echo $this->Form->input('report_id', array(
					'value' => $report_id,
					'type' => 'hidden',
				));
				
				echo $this->Form->input('vectors', array(
					'type' => 'textarea',
					'label' => array(
						'text' => __('Vectors'),
						'class' => 'tipsy',
						'title' => __('Separate each one with a new line. Valid types are: host names, ip addresses, hashes, urls, alphanumeric values'),
					),
					'between' => __('These are case sensitive.'),
				));
				
				echo $this->Form->input('vector_type_id', array(
	        		'label' => __('Vector Group'),
					'empty' => __('[ None ]'),
				));
				
				echo $this->Form->input('dns_auto_lookup', array(
					'label' => __('DNS Tracking Level for Hostnames/Ip Addresses'),
					'between' => $this->Html->para('info', __('This settings is global to the vectors.')),
					'options' => $this->Wrap->dnsAutoLookupLevelOptions(false, true),
				));
				
				echo $this->Form->input('whois_auto_lookup', array(
					'label' => __('Whois Tracking Level for Hostnames/Ip Addresses'),
					'between' => $this->Html->para('info', __('This settings is global to the vectors.')),
					'options' => $this->Wrap->whoisAutoLookupLevelOptions(false, true),
				));
				
				echo $this->Form->input('vt_lookup', array(
					'label' => __('Lookup in %s. (for %s/%s/%s)', __('VirusTotal'), __('Hostnames'), __('Ip Addresses'), __('Hashes')),
					'between' => $this->Html->para('info', __('This setting is global to the %s.', __('Vectors'))),
					'options' => $this->Wrap->vtAutoLookupLevelOptions(false, true),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save Vectors')); ?>
	</div>
</div>

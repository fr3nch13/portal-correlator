<?php ?>
<!-- File: app/View/CategoriesVectors/add.ctp -->
<div class="top">
	<h1><?php echo __('Add %s to the %s: %s.', __('Vectors'), __('Category'), $category_name); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('CategoriesVector', array('url' => array('action' => 'add', $category_id)));?>
	    <fieldset>
	        <legend><?php echo __('Add %s to the %s: %s.', __('Vectors'), __('Category'), $category_name); ?></legend>
	    	<?php
				echo $this->Form->input('category_id', array(
					'value' => $category_id,
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
					'label' => __('DNS Tracking Level for %s/%s', __('Hostnames'), __('Ip Addresses')),
					'between' => $this->Html->para('info', __('This setting is global to the %s.', __('Vectors'))),
					'options' => $this->Wrap->dnsAutoLookupLevelOptions(false, true),
				));
				
				echo $this->Form->input('hexillion_auto_lookup', array(
					'label' => __('Hexillion Tracking Level for %s/%s', __('Hostnames'), __('Ip Addresses')),
					'between' => $this->Html->para('info', __('This setting is global to the %s.', __('Vectors'))),
					'options' => $this->Wrap->dnsAutoLookupLevelOptions(false, true),
				));
				
				echo $this->Form->input('whois_auto_lookup', array(
					'label' => __('Whois Tracking Level for %s/%s', __('Hostnames'), __('Ip Addresses')),
					'between' => $this->Html->para('info', __('This setting is global to the %s.', __('Vectors'))),
					'options' => $this->Wrap->whoisAutoLookupLevelOptions(false, true),
				));
				
				echo $this->Form->input('vt_lookup', array(
					'label' => __('Lookup in %s. (for %s/%s/%s)', __('VirusTotal'), __('Hostnames'), __('Ip Addresses'), __('Hashes')),
					'between' => $this->Html->para('info', __('This setting is global to the %s.', __('Vectors'))),
					'options' => $this->Wrap->vtAutoLookupLevelOptions(false, true),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save %s.', __('Vectors'))); ?>
	</div>
</div>

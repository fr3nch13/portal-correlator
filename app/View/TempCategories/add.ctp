<?php

$form_id = (isset($form_id)?$form_id:rand(1,1000));
?>
<div class="top">
	<h1><?php echo __('Add %s', __('Category')); ?></h1>
</div>
<div class="center">
	<div class="form" id="object-form-<?php echo $form_id; ?>">
		<?php echo $this->Form->create('TempCategory', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add %s', __('Category')); ?></legend>
		    	<?php
					echo $this->Form->input('name', array(
					));
					echo $this->Html->divClear();
		        	echo $this->Form->input('public', array(
						'div' => array('class' => 'forth'),
						'type' => 'select',
						'options' => $this->Wrap->publicStateOptions(),
						'default' => 1,
						'label' => array(
							'text' => __('Share State'),
							'class' => 'tipsy',
							'title' => __('If other users can see this %s, or just you.', __('Category')),
						)
					));
					echo $this->Form->input('category_type_id', array(
						'div' => array('class' => 'forth'),
						'label' => __('Category Group'),
						'empty' => __('[ None ]'),
					));
					echo $this->AutoComplete->input('TempCategory.mysource', array(
						'div' => array('class' => 'forth'),
						'label' => __('User Source'),
						'autoCompleteUrl'=>$this->Html->url(array(
							'admin' => false,
							'controller' => 'categories',
							'action'=>'auto_complete',
							'mysource'
						)),
						'autoCompleteRequestItem'=>'autoCompleteText',
					));
					echo $this->Form->input('compromise_date', array(
						'div' => array('class' => 'forth'),
						'label' => __('Compromised Date'),
						'type' => 'date',
					));
					echo $this->Html->divClear();
					echo $this->Form->input('adaccount', array(
						'div' => array('class' => 'forth'),
						'label' => __('Victim AD Account'),
						'id' => 'adaccount',
						'type' => 'autocomplete',
						'rel' => array(
							'controller' => 'ad_accounts',
							'action' => 'autocomplete',
						),
					));
					echo $this->Form->input('victim_ip', array(
						'div' => array('class' => 'forth'),
						'label' => __('Victim IP Address'),
					));
					echo $this->Form->input('victim_mac', array(
						'div' => array('class' => 'forth'),
						'label' => __('Victim MAC Address'),
					));
					echo $this->Form->input('victim_asset_tag', array(
						'div' => array('class' => 'forth'),
						'label' => __('Victim Asset Tag'),
					));
					echo $this->Html->divClear();
					echo $this->Form->input('sac_id', array(
						'div' => array('class' => 'forth'),
						'label' => __('Victim SAC'),
						'empty' => __('(Empty) To Be Determined'),
						'searchable' => true,
						'id' => 'sac_id',
					));
					echo $this->Form->input('targeted', array(
						'div' => array('class' => 'forth'),
						'label' => __('Assessment %s', __('Targeted APT')),
						'type' => 'boolean',
					));
					echo $this->Form->input('assessment_nih_risk_id', array(
						'div' => array('class' => 'forth'),
						'label' => __('Assessment %s', __('User Risk')),
						'empty' => __('[ None ]'),
					));
					echo $this->Form->input('assessment_cust_risk_id', array(
						'div' => array('class' => 'forth'),
						'label' => __('Assessment %s', __('Customer Risk')),
						'empty' => __('[ None ]'),
					));
					echo $this->Html->divClear();
					echo $this->Form->input('scan_desc', array(
						'type' => 'checkbox',
						'checked' => true,
						'label' => 'Check to scan Description for Vectors',
					));
					echo $this->Form->input('TempCategoriesDetail.desc', array(
						'label' => __('Description'),
					));
					echo $this->Form->input('TempCategoriesDetail.desc_private', array(
						'label' => __('Private Notes'),
					));
					echo $this->Form->input('temp_vectors', array(
						'type' => 'textarea',
						'label' => array(
							'text' => __('Vectors'), 
							'class' => 'tipsy',
							'title' => __('Separate each one with a new line. Valid types are: host names, ip addresses, hashes, urls, alphanumeric values'),
						),
						'between' => __('These are case sensitive.'),
					));
					echo $this->Form->input('TempUpload.0.scan', array(
						'type' => 'checkbox',
						'checked' => true,
						'label' => 'Check to scan File for Vectors',
					)); 
					echo $this->Form->input('TempUpload.0.file', array(
						'type' => 'file',
					));
					echo $this->Tag->autocomplete();
					
					echo $this->Local->editors(array(
						'available_options' => $editors['available'],
						'editors_options' => $editors['editors'],
						'contributors_options' => $editors['contributors'],
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Category'))); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	$('div#object-form-<?php echo $form_id; ?>').objectForm();
	
	$('#adaccount').on('blur', function(event)
	{
		var input = $(this);
		setTimeout(function(){
		if(!input.val())
			return true;
		// disable all un-disabled fields
		$('div#object-form-<?php echo $form_id; ?> input:enabled').addClass('temp-disabled').prop( "disabled", true );
		$('div#object-form-<?php echo $form_id; ?> select:enabled').addClass('temp-disabled').prop( "disabled", true );
		$('div#object-form-<?php echo $form_id; ?> select[searchable]').trigger("chosen:updated");
		
		$('div#object-form-<?php echo $form_id; ?>').objectForm('ajax', {
			url: '<?= $this->Html->url($this->Html->urlModify(array("controller" => "ad_accounts", "action" => "user_info"))) ?>.json',
			dataType: 'json',
			method: 'POST',
			data: { username: input.val() },
			success: function(data) {
				// fill out the other forms items
				if(data.result.sac_id && $('#sac_id').length && !$('#sac_id').val())
				{
					$('#sac_id').val(data.result.sac_id);
					if($('#sac_id[searchable]').length)
						$("#sac_id").trigger("chosen:updated");
				}
			},
			complete: function(data, textStatus, jqXHR) {
				$('div#object-form-<?php echo $form_id; ?> input:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				$('div#object-form-<?php echo $form_id; ?> select:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				$('div#object-form-<?php echo $form_id; ?> select[searchable]').trigger("chosen:updated");
			}
		});
		}, 500); // setTimeout
	});
	

});
//]]>
</script>

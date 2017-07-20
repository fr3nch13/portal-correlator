<?php
// File: app/View/Uploads/admin_edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit File: %s', $this->data['Upload']['filename']); ?></h1>
</div>
<div class="center">
	<div class="posts form">
		<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Edit File'); ?></legend>
		    	<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
		        	echo $this->Form->input('public', array(
						'type' => 'select',
						'options' => $this->Wrap->publicStateOptions(),
						'default' => 1,
						'label' => array(
							'text' => __('Share State'),
							'class' => 'tipsy',
							'title' => __('If other users can see this file, or just you.'),
						)
					));
					
					echo $this->Form->input('user_id', array(
						'label' => __('Owner'),
					));
				
					echo $this->Form->input('upload_type_id', array(
						'label' => 'File Group',
						'empty' => __('[ None ]'),
					));
					
					echo $this->AutoComplete->input('Upload.mysource', array(
						'label' => __('User Source'),
						'autoCompleteUrl'=>$this->Html->url(array(
							'admin' => false,
							'action'=>'auto_complete',
							'mysource',
							$this->request->data['Upload']['user_id'],
						)),
						'autoCompleteRequestItem'=>'autoCompleteText',
					));
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update File')); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{ 
	// change the upload type options based on a selected user when a user is changed
	$('#UploadUserId').change(function(){
		setTimeout(function(){
		$('#UploadUploadTypeId').html('<option value="">Loading...</option>');
		$.ajax({
			url:"<?php echo $this->Html->url(array('controller' => 'upload_types', 'action' => 'listfromuserid', )); ?>/" + $('#UploadUserId').val() + ".json",
			dataType: "json",
			async: false,                                // tried with true before
			success: function(data) 
			{
				var $items='<option value="">[ Global ]</option>';
				jQuery.each(data, function (id, name)
				{
					$items += '<option value="'+id+'">'+name+'</option>';
				});
				$('#UploadUploadTypeId').html($items);
			}
		});
		}, 500);//200 ms should be fine
	});
});
//]]>
</script>

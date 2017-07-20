<?php 
// File: app/View/ImportManager/admin_edit.ctp

$result = $this->Js->get('.parser_options')->effect('hide');
$this->Js->buffer($result);
$code = "var options = \"#parser_\"+$(\"#ImportManagerParser\").val(); $(options).show();";
$this->Js->buffer($code);

$result = $this->Js->get('.location_options')->effect('hide');
$this->Js->buffer($result);
$code = "var options = \"#location_\"+$(\"#ImportManagerLocation\").val(); $(options).show();";
$this->Js->buffer($code);

?>
<div class="top">
	<h1><?php echo __('Edit Import Manager'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ImportManager');?>
		    <fieldset>
		        <legend><?php echo __('Edit Import Manager'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
					
					echo $this->Form->input('key', array(
						'label' => __('Key'),
						'between' => __('A simple id to identify this import. Rules: 1. Unique, 2. Contain only letters, numbers, and underscore. 3. It will be lower-cased.'),
					));
					
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
					
					echo $this->Tag->autocomplete();
					
					echo $this->Form->input('auto_reviewed', array(
						'options' => Configure::read('Importer.auto_reviewed_options'),
						'empty' => __('[ Select ]'),
						'label' => __('Auto review?'),
						'between' => __('If you would like this Import Manager to mark the Imports as review, and the vectors as active, or require the vectors to be reviewed.'),
					));
					
					// unchangable values but need them for validation
					echo $this->Form->input('cron', array('type' => 'hidden'));
					echo $this->Form->input('parser', array('type' => 'hidden'));
					echo $this->Form->input('location', array('type' => 'hidden'));
		    	?>
				<div class="parser_options" id="parser_csv">
					<h2><?php echo __('Parser - CSV File Options'); ?></h2>
		    		<?php
						echo $this->Wrap->editCsvFields($this->data['ImportManager']['csv_fields'], $vectorTypes);
					?>
				</div>
				
				<div class="location_options" id="location_local">
					<h2><?php echo __('Location - Local Path Options'); ?></h2>
		    		<?php
						echo $this->Form->input('local_path', array(
							'label' => __('Local Path'),
							'between' => __('Absolute path to the directory that holds the files to be imported. <br /> No spaces allowed. <br /> See the `pwd` command in linux. Must start with a "%s"', DS),
						));
					?>
				</div>
		    </fieldset>
		<?php echo $this->Form->end(__('Update Import Manager')); ?>
	</div>
</div>
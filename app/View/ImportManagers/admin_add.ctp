<?php 
// File: app/View/ImportManager/admin_add.ctp

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
	<h1><?php echo __('Add Import Manager'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ImportManager');?>
		    <fieldset>
		        <legend><?php echo __('Add Import Manager'); ?></legend>
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
					
					echo $this->Form->input('cron', array(
						'options' => Configure::read('Importer.cron_options'),
						'empty' => __('[ Select Automatic state ]'),
						'label' => array(
							'text' => __('Automatically run?'),
							'class' => 'tipsy',
							'title' => __('If you would like this Import Manager to run on a scheduled basis, and automatically create Imports.'),
						)
					));
					
					echo $this->Form->input('auto_reviewed', array(
						'options' => Configure::read('Importer.auto_reviewed_options'),
						'empty' => __('[ Select ]'),
						'label' => __('Auto review?'),
						'between' => __('If you would like this Import Manager to mark the Imports as review, and the vectors as active, or require the vectors to be reviewed.'),
					));
					
		        	echo $this->Form->input('parser', array(
						'options' => Configure::read('Importer.parsers'),
						'empty' => __('[ Select Parser ]'),
						'label' => array(
							'text' => __('Parser'),
							'class' => 'tipsy',
							'title' => __('Basically what the format of the file is.'),
						)
					));
					$code = $this->Js->get('.parser_options')->effect('hide');
					$code .= " 
					var options = \"#parser_\"+$(\"#ImportManagerParser\").val();
					$(options).show();
					";
					
					
					$this->Js->buffer($this->Js->get('#ImportManagerParser')->event('change', $code));
					
		    	?>
				<div class="parser_options" id="parser_csv">
					<h2><?php echo __('Parser - CSV File Options'); ?></h2>
		    		<?php
						echo $this->Wrap->editCsvFields(false, $vectorTypes);
					?>
				</div>
				
				<?php
		        	echo $this->Form->input('location', array(
						'options' => Configure::read('Importer.locations'),
						'empty' => __('[ Select Location ]'),
						'label' => array(
							'text' => __('File(s) Location'),
							'class' => 'tipsy',
							'title' => __('How are we going to receive the file(s).'),
						)
					));
					$code = $this->Js->get('.location_options')->effect('hide');
					$code .= " 
					var options = \"#location_\"+$(\"#ImportManagerLocation\").val();
					$(options).show();
					";
					
					$this->Js->buffer($this->Js->get('#ImportManagerLocation')->event('change', $code));
		    	?>
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
		<?php echo $this->Form->end(__('Add Import Manager')); ?>
	</div>
</div>
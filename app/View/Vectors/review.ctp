<?php
// File: app/View/Vectors/review.ctp
?>
<div class="top">
	<h1><?php echo __('Review Vectors'); ?></h1>
	<div class="clearb"> </div>
</div>
<div class="center">
	<div class="form">
	<?php echo $this->Form->create('Vector');?>
		<?php 
		$i=0;
		foreach ($this->data as $model => $fields): 
		?>
	    <fieldset>
	    	<h2>
	        	<?php 
	        		$_model = $model;
	        		if($model == 'Upload') $_model = 'File';
	        		echo __('Review Vectors for: %s - %s', $_model, $reviewItems[$model]); 
	        	?>
	        </h2>
	        <legend>
	        </legend>
	    	<?php
				echo $this->Form->input($model. '.id');
				
				// build out the vectors table
				$th = array();
				$th['Vector.vector'] = array('content' => __('Vector'));

				if($model == 'Category')
				{
					$th['CategoriesVector.active'] = array('content' => __('Active for Category'));
					$th['CategoriesVector.remove'] = array('content' => __('Don\'t Add to Category'));
				}
				if($model == 'Report')
				{
					$th['ReportsVector.active'] = array('content' => __('Active for Report'));
					$th['ReportsVector.remove'] = array('content' => __('Don\'t Add to Report'));
				}
				if($model == 'Upload')
				{
					$th['UploadsVector.active'] = array('content' => __('Active for File'));
					$th['UploadsVector.remove'] = array('content' => __('Don\'t Add to File'));
				}

				$th['remove'] = array('content' => __('Don\'t Add at All'));
				
				$td = array();
				
				foreach ($fields['vectors'] as $j => $vector)
				{
					$i++;
					$td[$j]['Vector.vector'] = $this->Form->input('Vector.'.$i.'.vector', array('value' => $vector, 'div' => false, 'label' => false));
						
					if($model == 'Category')
					{
						$td[$j]['CategoriesVector.active'] = $this->Form->input('CategoriesVector.'.$i.'.active', array('type' => 'checkbox', 'checked' => true, 'value' => 1, 'div' => false, 'label' => false));
						$td[$j]['CategoriesVector.remove'] = $this->Form->input('CategoriesVector.'.$i.'.remove', array('type' => 'checkbox', 'checked' => false, 'value' => 1, 'div' => false, 'label' => false));
					}
					if($model == 'Report')
					{
						$td[$j]['ReportsVector.active'] = $this->Form->input('ReportsVector.'.$i.'.active', array('type' => 'checkbox', 'checked' => true, 'value' => 1, 'div' => false, 'label' => false));
						$td[$j]['ReportsVector.remove'] = $this->Form->input('ReportsVector.'.$i.'.remove', array('type' => 'checkbox', 'checked' => false, 'value' => 1, 'div' => false, 'label' => false));
					}
					if($model == 'Upload')
					{
						$td[$j]['UploadsVector.active'] = $this->Form->input('UploadsVector.'.$i.'.active', array('type' => 'checkbox', 'checked' => true, 'value' => 1, 'div' => false, 'label' => false));
						$td[$j]['UploadsVector.remove'] = $this->Form->input('UploadsVector.'.$i.'.remove', array('type' => 'checkbox', 'checked' => false, 'value' => 1, 'div' => false, 'label' => false));
					}
					
					$td[$j]['remove'] = $this->Form->input('Vector.'.$i.'.remove', array('type' => 'checkbox', 'checked' => false, 'value' => 1, 'div' => false, 'label' => false));
				}				
				echo $this->element('Utilities.table', array(
					'th' => $th,
					'td' => $td,
					'use_search' => false,
					'use_pagination' => false,
				)); ?>
	    </fieldset>
		<?php endforeach; ?>
	<?php echo $this->Form->end(__('Save Vectors')); ?>
	</div>
</div>

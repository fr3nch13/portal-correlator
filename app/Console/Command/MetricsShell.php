<?php

class MetricsShell extends AppShell
{
	// the models to use
	public $uses = array('Report', 'Category', 'Upload', 'Import', 'Signature', 'Vector');
	
	public function startup() 
	{
//		$this->clear();
		$this->Vector->shellOut('Metrics Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Metrics Shell runs metrics on the different objects.'));
		
		$parser->addSubcommand('yearly', array(
			'help' => __d('cake_console', 'Metrics Reports for object from the beginning of this year, to today.'),
		));
		
		return $parser;
	}
	
	public function yearly()
	{
		Configure::write('debug', 1);
		$created = date('Y'). '-01-01 00:00:00';
		//$created = '2013-01-01 00:00:00';
		
		
		$this->Vector->shellOut(__('Metrics Counts since %s', $created), 'metrics');
		
		$counts = array();
		
		$categories = $this->Category->find('all', array(
			'recursive' => 0,
			'contain' => array('CategoryType'),
			'order' => array('Category.created DESC'),
			'conditions' => array(
				'Category.created >' => $created,
			),
		));
		
		$counts['Categories Added'] = count($categories);
		$this->Vector->shellOut(__('Found %s Categories.', $counts['Categories Added']), 'metrics');
		
		$category_types = array();
		foreach($categories as $category)
		{
			$category_type_id = $category['CategoryType']['name'];
			if(!$category_type_id) $category_types['Unassigned'] = (isset($category_types['Unassigned'])?++$category_types['Unassigned']:1);
			else $category_types[$category_type_id] = (isset($category_types[$category_type_id])?++$category_types[$category_type_id]:1);
		}
		
		arsort($category_types);
		$counts['Category Counts by Category Type'] = $category_types;
		
		
		$category_ids = Set::extract('/Category/id', $categories);
		
		$categories_vectors = $this->Category->CategoriesVector->find('all', array(
			'recursive' => 0,
			'contain' => array('VectorType', 'Vector'),
			'order' => array('CategoriesVector.created DESC'),
			'conditions' => array(
				'CategoriesVector.category_id' => $category_ids,
				'Vector.bad' => false,
				'CategoriesVector.created >' => $created,
			),
		));
		
		$counts['Category Vectors Added'] = count($categories_vectors);
		$this->Vector->shellOut(__('Found %s Category Vectors Added.', $counts['Category Vectors Added']), 'metrics');
		
		$vector_types = array();
		$vector_groups = array();
		foreach($categories_vectors as $categories_vector)
		{
			$vector_group_id = $categories_vector['VectorType']['name'];
			if(!$vector_group_id) $vector_groups['Unassigned'] = (isset($vector_groups['Unassigned'])?++$vector_groups['Unassigned']:1);
			else $vector_groups[$vector_group_id] = (isset($vector_groups[$vector_group_id])?++$vector_groups[$vector_group_id]:1);
			
			$vector_type = $categories_vector['Vector']['type'];
			if(!$vector_type) $vector_type['Unknown'] = (isset($vector_type['Unknown'])?++$vector_type['Unknown']:1);
			else $vector_types[$vector_type] = (isset($vector_types[$vector_type])?++$vector_types[$vector_type]:1);
		}
		
		arsort($vector_types);
		arsort($vector_groups);
		
		$counts['Category Vectors by Type'] = $vector_types;
		$counts['Category Vectors by Group'] = $vector_groups;
		
		unset($categories);
		unset($category_ids);
		unset($categories_vectors);
		unset($vector_types);
		unset($vector_groups);
		
//////////////////////////////////////
		
		$reports = $this->Report->find('all', array(
			'recursive' => 0,
			'contain' => array('ReportType'),
			'order' => array('Report.created DESC'),
			'conditions' => array(
				'Report.created >' => $created,
			),
		));
		
		$counts['Reports Added'] = count($reports);
		$this->Vector->shellOut(__('Found %s Reports.', $counts['Reports Added']), 'metrics');
		
		$report_types = array();
		foreach($reports as $report)
		{
			$report_type_id = $report['ReportType']['name'];
			if(!$report_type_id) $report_types['Unassigned'] = (isset($report_types['Unassigned'])?++$report_types['Unassigned']:1);
			else $report_types[$report_type_id] = (isset($report_types[$report_type_id])?++$report_types[$report_type_id]:1);
		}
		
		arsort($report_types);
		$counts['Report Counts by Report Type'] = $report_types;
		
		
		$report_ids = Set::extract('/Report/id', $reports);
		
		$reports_vectors = $this->Report->ReportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('VectorType', 'Vector'),
			'order' => array('ReportsVector.created DESC'),
			'conditions' => array(
				'ReportsVector.report_id' => $report_ids,
				'Vector.bad' => false,
				'ReportsVector.created >' => $created,
			),
		));
		
		$counts['Report Vectors Added'] = count($reports_vectors);
		$this->Vector->shellOut(__('Found %s Report Vectors Added.', $counts['Report Vectors Added']), 'metrics');
		
		$vector_types = array();
		$vector_groups = array();
		foreach($reports_vectors as $reports_vector)
		{
			$vector_group_id = $reports_vector['VectorType']['name'];
			if(!$vector_group_id) $vector_groups['Unassigned'] = (isset($vector_groups['Unassigned'])?++$vector_groups['Unassigned']:1);
			else $vector_groups[$vector_group_id] = (isset($vector_groups[$vector_group_id])?++$vector_groups[$vector_group_id]:1);
			
			$vector_type = $reports_vector['Vector']['type'];
			if(!$vector_type) $vector_type['Unknown'] = (isset($vector_type['Unknown'])?++$vector_type['Unknown']:1);
			else $vector_types[$vector_type] = (isset($vector_types[$vector_type])?++$vector_types[$vector_type]:1);
		}
		
		arsort($vector_types);
		arsort($vector_groups);
		
		$counts['Report Vectors by Type'] = $vector_types;
		$counts['Report Vectors by Group'] = $vector_groups;
		
		unset($reports);
		unset($report_ids);
		unset($reports_vectors);
		unset($vector_types);
		unset($vector_groups);
		
///////////////////////////////

		
		$uploads = $this->Upload->find('all', array(
			'recursive' => 0,
			'contain' => array('UploadType'),
			'order' => array('Upload.created DESC'),
			'conditions' => array(
				'Upload.created >' => $created,
			),
		));
		
		$counts['Uploads Added'] = count($uploads);
		$this->Vector->shellOut(__('Found %s Uploads.', $counts['Uploads Added']), 'metrics');
		
		$upload_types = array();
		foreach($uploads as $upload)
		{
			$upload_type_id = $upload['UploadType']['name'];
			if(!$upload_type_id) $upload_types['Unassigned'] = (isset($upload_types['Unassigned'])?++$upload_types['Unassigned']:1);
			else $upload_types[$upload_type_id] = (isset($upload_types[$upload_type_id])?++$upload_types[$upload_type_id]:1);
		}
		
		arsort($upload_types);
		$counts['Upload Counts by Upload Type'] = $upload_types;
		
		
		$upload_ids = Set::extract('/Upload/id', $uploads);
		
		$uploads_vectors = $this->Upload->UploadsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('VectorType', 'Vector'),
			'order' => array('UploadsVector.created DESC'),
			'conditions' => array(
				'UploadsVector.upload_id' => $upload_ids,
				'Vector.bad' => false,
				'UploadsVector.created >' => $created,
			),
		));
		
		$counts['Upload Vectors Added'] = count($uploads_vectors);
		$this->Vector->shellOut(__('Found %s Upload Vectors Added.', $counts['Upload Vectors Added']), 'metrics');
		
		$vector_types = array();
		$vector_groups = array();
		foreach($uploads_vectors as $uploads_vector)
		{
			$vector_group_id = $uploads_vector['VectorType']['name'];
			if(!$vector_group_id) $vector_groups['Unassigned'] = (isset($vector_groups['Unassigned'])?++$vector_groups['Unassigned']:1);
			else $vector_groups[$vector_group_id] = (isset($vector_groups[$vector_group_id])?++$vector_groups[$vector_group_id]:1);
			
			$vector_type = $uploads_vector['Vector']['type'];
			if(!$vector_type) $vector_type['Unknown'] = (isset($vector_type['Unknown'])?++$vector_type['Unknown']:1);
			else $vector_types[$vector_type] = (isset($vector_types[$vector_type])?++$vector_types[$vector_type]:1);
		}
		
		arsort($vector_types);
		arsort($vector_groups);
		
		$counts['Upload Vectors by Type'] = $vector_types;
		$counts['Upload Vectors by Group'] = $vector_groups;
		
		unset($uploads);
		unset($upload_ids);
		unset($uploads_vectors);
		unset($vector_types);
		unset($vector_groups);
		
////////////////////////////////

		
		$imports = $this->Import->find('all', array(
			'recursive' => 0,
			//'contain' => array('ImportType'),
			'order' => array('Import.created DESC'),
			'conditions' => array(
				'Import.created >' => $created,
			),
		));
		
		$counts['Imports Added'] = count($imports);
		$this->Vector->shellOut(__('Found %s Imports.', $counts['Imports Added']), 'metrics');
		
		
		$import_ids = Set::extract('/Import/id', $imports);
		
		$imports_vectors = $this->Import->ImportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('VectorType', 'Vector'),
			'order' => array('ImportsVector.created DESC'),
			'conditions' => array(
				'ImportsVector.import_id' => $import_ids,
				'Vector.bad' => false,
				'ImportsVector.created >' => $created,
			),
		));
		
		$counts['Import Vectors Added'] = count($imports_vectors);
		$this->Vector->shellOut(__('Found %s Import Vectors Added.', $counts['Import Vectors Added']), 'metrics');
		
		$vector_types = array();
		$vector_groups = array();
		foreach($imports_vectors as $imports_vector)
		{
			$vector_group_id = $imports_vector['VectorType']['name'];
			if(!$vector_group_id) $vector_groups['Unassigned'] = (isset($vector_groups['Unassigned'])?++$vector_groups['Unassigned']:1);
			else $vector_groups[$vector_group_id] = (isset($vector_groups[$vector_group_id])?++$vector_groups[$vector_group_id]:1);
			
			$vector_type = $imports_vector['Vector']['type'];
			if(!$vector_type) $vector_type['Unknown'] = (isset($vector_type['Unknown'])?++$vector_type['Unknown']:1);
			else $vector_types[$vector_type] = (isset($vector_types[$vector_type])?++$vector_types[$vector_type]:1);
		}
		
		arsort($vector_types);
		arsort($vector_groups);
		
		$counts['Import Vectors by Type'] = $vector_types;
		$counts['Import Vectors by Group'] = $vector_groups;
		
		unset($imports);
		unset($import_ids);
		unset($imports_vectors);
		unset($vector_types);
		unset($vector_groups);
		
/////////////////////////
		
		$signatures = $this->Signature->find('all', array(
			'recursive' => 0,
			'contain' => array('SignatureSource'),
			'order' => array('Signature.created DESC'),
			'conditions' => array(
				'Signature.created >' => $created,
			),
		));
		
		$counts['Signatures Added'] = count($signatures);
		$this->Vector->shellOut(__('Found %s Signatures Added.', $counts['Signatures Added']), 'metrics');
		
		$signature_types = array();
		$signature_sources = array();
		foreach($signatures as $signature)
		{
			$signature_source_id = $signature['SignatureSource']['name'];
			if(!$signature_source_id) $signature_sources['Unassigned'] = (isset($signature_sources['Unassigned'])?++$signature_sources['Unassigned']:1);
			else $signature_sources[$signature_source_id] = (isset($signature_sources[$signature_source_id])?++$signature_sources[$signature_source_id]:1);
			
			$signature_type = $signature['Signature']['signature_type'];
			if(!$signature_type) $signature_type['Unknown'] = (isset($signature_type['Unknown'])?++$signature_type['Unknown']:1);
			else $signature_types[$signature_type] = (isset($signature_types[$signature_type])?++$signature_types[$signature_type]:1);
		}
		
		arsort($signature_types);
		arsort($signature_sources);
		
		$counts['Signatures by Type'] = $signature_types;
		$counts['Signatures by Source'] = $signature_sources;
		
		unset($signatures);
		unset($signature_types);
		unset($signature_sources);
		
///////////////////////////////

		
		$_vector_types = $this->Vector->VectorType->find('list', array(
			'recursive' => -1,
			'fields' => array('VectorType.id', 'VectorType.name'),
			'order' => array('VectorType.name'),
		));
		$this->Vector->shellOut(__('Found %s Vectors Groups.', count($_vector_types)), 'metrics');
		
		$vectors = $this->Vector->find('all', array(
			'recursive' => -1,
			//'contain' => array('VectorType'),
			'fields' => array(
				'Vector.vector_type_id',
				'Vector.type',
			),
			'order' => array('Vector.created DESC'),
			'conditions' => array(
				'Vector.created >' => $created,
				'Vector.bad' => false,
			),
		));
		
		$counts['Vectors Added'] = count($vectors);
		$this->Vector->shellOut(__('Found %s Vectors Added.', $counts['Vectors Added']), 'metrics');
		
		$vector_types = array();
		$_vector_groups = array();
		$vector_groups = array();
		foreach($vectors as $vector)
		{
		
			$vector_group_id = $vector['Vector']['vector_type_id'];
			if(!$vector_group_id) $_vector_groups['Unassigned'] = (isset($_vector_groups['Unassigned'])?++$_vector_groups['Unassigned']:1);
			else $_vector_groups[$vector_group_id] = (isset($_vector_groups[$vector_group_id])?++$_vector_groups[$vector_group_id]:1);
			
			$vector_type = $vector['Vector']['type'];
			if(!$vector_type) $vector_type['Unknown'] = (isset($vector_type['Unknown'])?++$vector_type['Unknown']:1);
			else $vector_types[$vector_type] = (isset($vector_types[$vector_type])?++$vector_types[$vector_type]:1);
		}
		
		// replace the vector type id with the vector type name
		foreach($_vector_groups as $vector_group_id => $count)
		{
			$name = (isset($_vector_types[$vector_group_id])?$_vector_types[$vector_group_id]:$vector_group_id);
			
			$vector_groups[$name] = $count;
		}
		unset($_vector_groups);
		
		arsort($vector_types);
		arsort($vector_groups);
		
		$counts['Vectors by Type'] = $vector_types;
		$counts['Vectors by Group'] = $vector_groups;
		
		unset($vectors);
		unset($vector_types);
		unset($vector_groups);
pr($counts);
		
	}
}
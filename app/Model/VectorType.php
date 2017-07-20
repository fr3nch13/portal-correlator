<?php
App::uses('AppModel', 'Model');
/**
 * VectorType Model
 *
 * @property TempVector $TempVector
 * @property Vector $Vector
 */
class VectorType extends AppModel 
{

	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'Vector' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'CategoriesVector' => array(
			'className' => 'CategoriesVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'ReportsVector' => array(
			'className' => 'ReportsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'ImportsVector' => array(
			'className' => 'ImportsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'UploadsVector' => array(
			'className' => 'UploadsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'DumpsVector' => array(
			'className' => 'DumpsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'TempVector' => array(
			'className' => 'TempVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'TempCategoriesVector' => array(
			'className' => 'TempCategoriesVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'TempReportsVector' => array(
			'className' => 'TempReportsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
		'TempUploadsVector' => array(
			'className' => 'TempUploadsVector',
			'foreignKey' => 'vector_type_id',
			'dependent' => false,
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'VectorType.name',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active', 'bad');
	
	// fields that are boolean and marks the record as the default
	// e.g. only one record can be 1, the rest are 0
	public $defaultFields = array('holder');
	
	// options when you are modifying multiple objects
	public $multiselectOptions = array('type');
	
	public function delete($id = null, $cascade = false)
	{
		if(!$id) return false;
		
		// update all $hasMany to have their vector_type_ids set to 0
		$this->CategoriesVector->updateAll(
			array('CategoriesVector.vector_type_id' => $id),
			array('CategoriesVector.vector_type_id' => 0)
		);
		$this->ReportsVector->updateAll(
			array('ReportsVector.vector_type_id' => $id),
			array('ReportsVector.vector_type_id' => 0)
		);
		$this->UploadsVector->updateAll(
			array('UploadsVector.vector_type_id' => $id),
			array('UploadsVector.vector_type_id' => 0)
		);
		$this->ImportsVector->updateAll(
			array('ImportsVector.vector_type_id' => $id),
			array('ImportsVector.vector_type_id' => 0)
		);
		$this->DumpsVector->updateAll(
			array('DumpsVector.vector_type_id' => $id),
			array('DumpsVector.vector_type_id' => 0)
		);
/*
		$this->TempCategoriesVector->updateAll(
			array('TempCategoriesVector.vector_type_id' => $id),
			array('TempCategoriesVector.vector_type_id' => 0)
		);
		$this->TempReportsVector->updateAll(
			array('TempReportsVector.vector_type_id' => $id),
			array('TempReportsVector.vector_type_id' => 0)
		);
		$this->TempUploadsVector->updateAll(
			array('TempUploadsVector.vector_type_id' => $id),
			array('TempUploadsVector.vector_type_id' => 0)
		);
*/
		return parent::delete($id, $cascade);
	}
	
	public function add($name = null)
	{
		if(!$name) return false;
		
		// return the id if it already exists
		if($id = $this->field('id', array('name' => $name)))
		{
			return $id;
		}
		$this->create();
		if($this->save(array('name' => $name)))
		{
			return $this->id;
		}
		return false;
	}
	
	public function toggleRecord($id = null, $field = false)
	{
		if(!parent::toggleRecord($id, $field))
		{
			return false;
		}
		
		if($field != 'bad')
		{
			return true;
		}
		
		// toggle the bad state of each vector in this group
		$this->Vector->updateAll(
			array('Vector.bad' => $this->toggleNewValue), // set in common behavior
			array('Vector.vector_type_id' => $id)
		);
		
		return true;
	}
	
	public function updateAssessments()
	{
		Configure::write('debug', 1);
		
		$modelMap = array(
			'assessment_ors_or_orf' => array('model' => 'AssessmentOrganization', 'xrefField' => 'assessment_organization_id'),
			'assessment_gal_listed_lab_branch_office' => array('model' => 'AssessmentOffice', 'xrefField' => 'assessment_office_id'),
			'assessment_risk_to_customer' => array('model' => 'AssessmentCustRisk', 'xrefField' => 'assessment_cust_risk_id'),
			'assessment_risk_to_nih' => array('model' => 'AssessmentNihRisk', 'xrefField' => 'assessment_nih_risk_id'),
//			'assessment_targeted_apt' => 'AssessmentNihRisk',
//			'assessment_compromise_date' => 'AssessmentNihRisk',
		);
		
		// get a list of all of the vector types with 'assessment' in their name
		$vectorTypes = $this->find('list', array(
			'conditions' => array(
				'VectorType.name LIKE' => '%Assessment%',
			),
		));
		
		$saveMany_reports = array();
		$saveMany_categories = array();
		
		$delete_reportsVectors = array();
		$delete_categoriesVectors = array();
		$delete_vectors = array();
		
		foreach($vectorTypes as $vectorType_id => $vectorType_name)
		{
			$vectorType_slug = strtolower($vectorType_name);
			$vectorType_slug = Inflector::slug($vectorType_slug);
pr($vectorType_slug);
			
			// find all reports vectors assinged to this type
			$reportsVectors = $this->Vector->ReportsVector->find('list', array(
				'conditions' => array(
					'ReportsVector.vector_type_id' => $vectorType_id,
				),
				'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
			));
			
			// find all category vectors assinged to this type
			$categoriesVectors = $this->Vector->CategoriesVector->find('list', array(
				'conditions' => array(
					'CategoriesVector.vector_type_id' => $vectorType_id,
				),
				'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
			));
			
			$moreVectorIds = array_merge($categoriesVectors, $reportsVectors);
			
			$vectors = $this->Vector->find('list', array(
				'conditions' => array(
					'OR' => array(
						'Vector.vector_type_id' => $vectorType_id,
						'Vector.id' => $moreVectorIds,
					),
				),
				'order' => array('Vector.vector' => 'ASC'),
			));
			
			foreach($vectors as $vector_id => $vector)
			{
				$vectorSlug = $this->slugify($vector);
				if($vectorSlug == 'med')
					$vector = 'Medium';
				
				// add/update the corrosponding report/category attribute
				// if it's a relationship to reports/categories
				$data = array();
				$xref_id = false;
				if(isset($modelMap[$vectorType_slug]))
				{
					$data[$modelMap[$vectorType_slug]['xrefField']] = $this->Vector->ReportsVector->Report->{$modelMap[$vectorType_slug]['model']}->checkAdd($vector);
				}
				elseif($vectorType_slug == 'assessment_targeted_apt')
				{
					$data['targeted'] = 0;
					if($vectorSlug == 'no')
						$data['targeted'] = 1;
					elseif($vectorSlug == 'yes')
						$data['targeted'] = 2;
				}
				elseif($vectorType_slug == 'assessment_compromise_date')
				{
					$data['compromise_date'] = false;
					$parts = explode('/', $vector);
					if(count($parts) == 3)
					{
						$mon = $parts[0];
						$day = $parts[1];
						$year = $parts[2];
						if(strlen($year) == 2)
							$year = '20'. $year;
						$data['compromise_date'] = __('%s-%s-%s 00:00:00', $year, $mon, $day);
					}
				}
				
				// find all of the reports that use this vector
				$reportsVectors = $this->Vector->ReportsVector->find('list', array(
					'conditions' => array(
						'ReportsVector.vector_id' => $vector_id,
					),
					'fields' => array('ReportsVector.id', 'ReportsVector.report_id'),
				));
				
				foreach($reportsVectors as $reportsVector_id => $report_id)
				{
					// track the report for updating
					if(!isset($saveMany_reports[$report_id]))
						$saveMany_reports[$report_id] = array('id' => $report_id);
					$saveMany_reports[$report_id] = array_merge($saveMany_reports[$report_id], $data);
					
					// track the xref record to delete later
					$delete_reportsVectors[$reportsVector_id] = $reportsVector_id;
				}
				
				// find all of the categories that use this vector
				$categoriesVectors = $this->Vector->CategoriesVector->find('list', array(
					'conditions' => array(
						'CategoriesVector.vector_id' => $vector_id,
					),
					'fields' => array('CategoriesVector.id', 'CategoriesVector.category_id'),
				));
				
				foreach($categoriesVectors as $categoriesVector_id => $category_id)
				{
					// track the category for updating
					if(!isset($saveMany_categories[$category_id]))
						$saveMany_categories[$category_id] = array('id' => $category_id);
					$saveMany_categories[$category_id] = array_merge($saveMany_categories[$category_id], $data);
					
					// track the xref record to delete later
					$delete_categoriesVectors[$categoriesVector_id] = $categoriesVector_id;
				}
				
				// track the vector for deletion
				$delete_vectors[$vector_id] = $vector_id;
			}
		}
		
		// update all of the reports
		foreach($saveMany_reports as $report_id => $data)
		{
pr($data);
			$this->Vector->ReportsVector->Report->id = $report_id;
			$this->Vector->ReportsVector->Report->data = $data;
			$this->Vector->ReportsVector->Report->save($this->Vector->ReportsVector->Report->data);
		}
		
		// update all of the categories
		foreach($saveMany_categories as $category_id => $data)
		{
pr($data);
			$this->Vector->CategoriesVector->Category->id = $category_id;
			$this->Vector->CategoriesVector->Category->data = $data;
			$this->Vector->CategoriesVector->Category->save($this->Vector->CategoriesVector->Category->data);
		}
		
		// delete the report vectors
pr('Deleting ReportsVector records');
		$this->Vector->ReportsVector->deleteAll(array(
			'ReportsVector.id' => $delete_reportsVectors,
		));
		// delete the category vectors
pr('Deleting CategoriesVector records');
		$this->Vector->CategoriesVector->deleteAll(array(
			'CategoriesVector.id' => $delete_categoriesVectors,
		));
		// delete the vectors
pr('Deleting Vector records');
		$this->Vector->deleteAll(array(
			'Vector.id' => $delete_vectors,
		));
		
	}
}

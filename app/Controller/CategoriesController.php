<?php
App::uses('AppController', 'Controller');

class CategoriesController extends AppController 
{
	public function isAuthorized($user = [])
	{
		// All registered users can add 
		if($this->action === 'add')
		{
			return $this->redirect(['controller' => 'temp_categories', 'action' => 'add']);
		}
		
		if($this->Common->isAdmin())
		{
			return true;
		}
		
		if(in_array($this->action, ['view'])) 
		{
			$categoryId = $this->request->params['pass'][0];
			
			if($this->Category->isOwnedBy($categoryId, AuthComponent::user('id')))
			{
				return true;
			}
			
			$public = $this->Category->isPublic($categoryId);
			if($public == 2)
			{
				return true;
			}
			elseif($public == 1)
			{
				if($this->Category->isSameOrgGroup($categoryId, AuthComponent::user('org_group_id')))
				{
					return true;
				}
				$this->Flash->error(__('You don\'t have access to that %s.', __('Category')));
				return $this->redirect(['action' => 'index']);
			}
			elseif($public == 0)
			{
				$this->Flash->error(__('You don\'t have access to that %s.', __('Category')));
				return $this->redirect(['action' => 'index']);
			}
		}
		
		// allowed editors
		if(in_array($this->action, ['edit_editor'])) 
		{
			$categoryId = $this->request->params['pass'][0];
			if($this->Category->CategoriesEditor->isEditor($categoryId, AuthComponent::user('id')))
			{
				return true;
			}
			$this->Flash->error(__('You don\'t have access to modify this %s.', __('Category')));
			return $this->redirect(['action' => 'view', $categoryId]);
		}
		
		// allowed contributors
		if(in_array($this->action, ['edit_contributor'])) 
		{
			$categoryId = $this->request->params['pass'][0];
			if($this->Category->CategoriesEditor->isContributor($categoryId, AuthComponent::user('id')))
			{
				return true;
			}
			$this->Flash->error(__('You don\'t have access to modify this %s.', __('Category')));
			return $this->redirect(['action' => 'view', $categoryId]);
		}
		
		// The owner of a Category can toggle, edit and delete it
		if(in_array($this->action, ['toggle', 'edit', 'delete'])) 
		{
			$categoryId = $this->request->params['pass'][0];
			if($this->Category->isOwnedBy($categoryId, AuthComponent::user('id')))
			{
				return true;
			}
			$this->Flash->error(__('You don\'t have access to modify this %s.', __('Category')));
			return $this->redirect(['action' => 'view', $categoryId]);
		}
		return parent::isAuthorized($user);
	}
	
	public function db_block_overview()
	{	
		$results = $this->Category->find('all');
		
		foreach($results as $i => $result)
		{
			$results[$i] = $this->Category->attachFismaSystem($result);
		}
		
		$this->set(compact(['results']));
		
		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_cust_risk_trend()
	{
		$snapshotStats = $this->Category->snapshotDashboardGetStats('/^category\.assessment_cust_risk\-\d+$/');
		$colors = $this->Category->AssessmentCustRisk->find('list', ['fields' => ['AssessmentCustRisk.id', 'AssessmentCustRisk.color_code_hex']]);
		
		$this->set(compact('snapshotStats', 'colors'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_nih_risk_trend()
	{
		$snapshotStats = $this->Category->snapshotDashboardGetStats('/^category\.assessment_nih_risk\-\d+$/');
		$colors = $this->Category->AssessmentNihRisk->find('list', ['fields' => ['AssessmentNihRisk.id', 'AssessmentNihRisk.color_code_hex']]);
		
		$this->set(compact('snapshotStats', 'colors'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_category_type_trend()
	{
		$snapshotStats = $this->Category->snapshotDashboardGetStats('/^category\.category_type\-\d+$/');
		
		$this->set(compact('snapshotStats'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_tab_totals($scope = 'org', $as_block = false)
	{
		$conditions = [];
		
		$results = $this->scopedResults($scope, $conditions);
		
		$this->set(compact([
			'as_block', 'results',
		]));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_cust_risk()
	{
		$conditions = $this->Category->conditionsAvailable();
		$conditions['Category.assessment_cust_risk_id >'] = 0;
		
		$categories = $this->Category->find('all', [
			'contain' => ['AssessmentCustRisk'],
			'conditions' => $conditions,
		]);
		
		$assessmentCustRisks = $this->Category->AssessmentCustRisk->find('all');
		
		$this->set(compact(['categories', 'assessmentCustRisks']));
	}
	
	public function db_block_assessment_nih_risk()
	{
		$conditions = $this->Category->conditionsAvailable();
		$conditions['Category.assessment_nih_risk_id >'] = 0;
		
		$categories = $this->Category->find('all', [
			'contain' => ['AssessmentNihRisk'],
			'conditions' => $conditions,
		]);
		
		$assessmentNihRisks = $this->Category->AssessmentNihRisk->find('all');
		
		$this->set(compact(['categories', 'assessmentNihRisks']));
	}
	
	public function db_block_category_type()
	{
		$conditions = $this->Category->conditionsAvailable();
		$conditions['Category.category_type_id >'] = 0;
		
		$categories = $this->Category->find('all', [
			'contain' => ['CategoryType'],
			'conditions' => $conditions,
		]);
		
		$categoryTypes = $this->Category->CategoryType->find('all');
		
		$this->set(compact(['categories', 'categoryTypes']));
	}
	
	public function dashboard()
	{
	}
	
	public function search_results()
	{
		return $this->index();
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		if(!isset($this->paginateModel))
			$this->paginateModel = 'Category';
		
		if(!$this->Common->isAdmin())
		{
			$conditions = $this->Category->conditionsAvailable();
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$page_subtitle = $this->get('page_subtitle');
		$page_description = $this->get('page_description');
		
		$this->Category->recursive = 0;
		
		if(!isset($this->paginate['contain']))
			$this->paginate['contain'] = [
				'User', 'CategoryType', 'OrgGroup', 'CategoriesDetail', 
				'AssessmentNihRisk', 'AssessmentCustRisk', 
				'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
			];
		
		$this->paginate['order'] = ['Category.id' => 'desc'];
		$this->paginate['conditions'] = $this->Category->conditions($conditions, $this->passedArgs); 
		$categories = $this->paginate($this->paginateModel);
		
		$this->set(compact(['page_subtitle', 'page_description', 'categories']));
		
		$categoryTypes = $this->Category->CategoryType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('categoryTypes', $categoryTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->Category->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->Category->AssessmentNihRisk->typeFormList();
		$sacs = $this->Category->Sac->typeFormList();
		$this->set(compact(['assessmentCustRisks', 'assessmentNihRisks', 'sacs']));
	}
	
	public function index_global() 
	{
		$conditions = [
			'Category.public' => 2,
		];
		
		$page_subtitle = __('Globally Available');
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function index_org() 
	{	
		$conditions = [
			'Category.public' => 1,
			'Category.org_group_id' => AuthComponent::user('org_group_id'),
		];
		
		$org_group = $this->Category->OrgGroup->read(null, AuthComponent::user('org_group_id'));
		
		$page_subtitle = __('Assigned to %s: %s', __('Org Group'), $org_group['OrgGroup']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function mine() 
	{
		$conditions = ['Category.user_id' => AuthComponent::user('id')];
		
		$page_subtitle = __('Mine');
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function org($org_id = null)  
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		
		$org = $this->Category->Sac->Branch->Division->Org->find('first', [
			'conditions' => ['Org.id' => $org_id],
		]);
		if (!$org) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		$this->set('object', $org);
		
		$sac_ids = $this->Category->Sac->idsForOrg($org_id);
		
		$conditions = $this->Category->_buildIndexConditions($sac_ids);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function division($division_id = null)  
	{
		if (!$division_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		
		$args = [
			'conditions' => ['Division.id' => $division_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = ['Org'];
		
		$division = $this->Category->Sac->Branch->Division->find('first', $args);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('object', $division);
		
		$sac_ids = $this->Category->Sac->idsForDivision($division_id);
		
		$conditions = $this->Category->_buildIndexConditions($sac_ids);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function branch($branch_id = null)  
	{
		if (!$branch_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		
		$args = [
			'conditions' => ['Branch.id' => $branch_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = ['Division', 'Division.Org'];
			
		$branch = $this->Category->Sac->Branch->find('first', $args);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('object', $branch);
		
		$sac_ids = $this->Category->Sac->idsForBranch($branch_id);
		
		$conditions = $this->Category->_buildIndexConditions($sac_ids);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function sac($sac_id = null)  
	{
		if (!$sac_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('SAC')));
		}
		
		$args = [
			'conditions' => ['Sac.id' => $sac_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = ['Branch', 'Branch.Division', 'Branch.Division.Org'];
		
		$sac = $this->Category->Sac->find('first', $args);
		if (!$sac) 
		{
			throw new NotFoundException(__('Invalid %s', __('SAC')));
		}
		$this->set('object', $sac);
		
		$conditions = $this->Category->_buildIndexConditions($sac_id);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function ad_account($ad_account_id = false)
	{
		if (!$ad_account_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('AD Account')));
		}
		
		$args = [
			'conditions' => ['AdAccount.id' => $ad_account_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = ['Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'];
		
		$adAccount = $this->Category->AdAccount->find('first', $args);
		$this->set('object', $adAccount);
		
		$conditions = $this->Category->_buildIndexConditions($ad_account_id, 'ad_account_id');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function fisma_system($fisma_system_id = false)
	{
		if (!$fisma_system_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA System')));
		}
		
		$args = [
			'conditions' => ['FismaSystem.id' => $fisma_system_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = [
				'OwnerContact', 
				'OwnerContact.Sac', 'OwnerContact.Sac.Branch', 
				'OwnerContact.Sac.Branch.Division', 'OwnerContact.Sac.Branch.Division.Org'
			];
		
		if (!$object = $this->Category->AdAccount->FismaSystem->find('first', $args))
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA System')));
		}
		$this->set('object', $object);
		
		if(!$conditions = $this->Category->correlateCorRToFismaSystem($object['FismaSystem']))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function fisma_inventory($fisma_inventory_id = false)
	{
		if (!$fisma_inventory_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA Inventory')));
		}
		
		$args = [
			'conditions' => ['FismaInventory.id' => $fisma_inventory_id],
		];
		if(!isset($this->passedArgs['getcount']))
			$args['contain'] = [
				'FismaSystem', 'FismaSystem.OwnerContact', 
				'FismaSystem.OwnerContact.Sac', 'FismaSystem.OwnerContact.Sac.Branch', 
				'FismaSystem.OwnerContact.Sac.Branch.Division', 'FismaSystem.OwnerContact.Sac.Branch.Division.Org'
			];
		
		if (!$object = $this->Category->AdAccount->FismaSystem->FismaInventory->find('first', $args))
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA Inventory')));
		}
		$this->set('object', $object);
		
		if(!$conditions = $this->Category->correlateCorRToFismaInventory($object['FismaInventory']))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function category($category_id = false) 
	{
		$this->Category->recursive = -1;
	 	$this->Category->cacher = true;
		if(!$category = $this->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
	 	$this->Category->cacher = true;
		if(!$category_ids = $this->Category->listCategoryRelatedIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Category.id' => $category_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Category'), $category['Category']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function report($report_id = null) 
	{
		$this->Category->Vector->Report->recursive = -1;
	 	$this->Category->Vector->Report->cacher = true;
		if(!$report = $this->Category->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
	 	$this->Category->Vector->cacher = true;
		if(!$category_ids = $this->Category->Vector->listReportToCategoriesIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Category.id' => $category_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Report'), $report['Report']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function upload($upload_id = null) 
	{
		$this->Category->Vector->Upload->recursive = -1;
	 	$this->Category->Vector->Upload->cacher = true;
		if(!$upload = $this->Category->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
	 	$this->Category->Vector->cacher = true;
		if(!$category_ids = $this->Category->Vector->listUploadToCategoriesIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Category.id' => $category_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('File'), $upload['Upload']['filename']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function import($import_id = null) 
	{
		$this->Category->Vector->Import->recursive = -1;
	 	$this->Category->Vector->Import->cacher = true;
		if(!$import = $this->Category->Vector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
	 	$this->Category->Vector->cacher = true;
		if(!$category_ids = $this->Category->Vector->listImportToCategoriesIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Category.id' => $category_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Import'), $import['Import']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function dump($dump_id = null) 
	{
		$this->Category->Vector->Dump->recursive = -1;
	 	$this->Category->Vector->Dump->cacher = true;
		if(!$dump = $this->Category->Vector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
	 	$this->Category->Vector->cacher = true;
		if(!$category_ids = $this->Category->Vector->listDumpToCategoriesIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Category.id' => $category_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Dump'), $dump['Dump']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function user($user_id = false) 
	{
		$this->Category->User->recursive = -1;
	 	$this->Category->User->cacher = true;
		if(!$user = $this->Category->User->read(null, $user_id))
		{
			throw new NotFoundException(__('Unknown %s', __('User')));
		}
		$this->set('user', $user);
		
		$conditions = [
			'Category.user_id' => $user_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Owned by %s: %s', __('User'), $user['User']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function assessment_cust_risk($assessment_cust_risk_id = false) 
	{
		$this->Category->AssessmentCustRisk->recursive = -1;
	 	$this->Category->AssessmentCustRisk->cacher = true;
		if(!$assessmentCustRisk = $this->Category->AssessmentCustRisk->read(null, $assessment_cust_risk_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Customer Risk')));
		}
		$this->set('assessmentCustRisk', $assessmentCustRisk);
		
		$conditions = [
			'Category.assessment_cust_risk_id' => $assessment_cust_risk_id,
		];
		
		if(!$this->Common->roleCheck('admin'))
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('Customer Risk'), $assessmentCustRisk['AssessmentCustRisk']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function assessment_nih_risk($assessment_nih_risk_id = false) 
	{
		$this->Category->AssessmentNihRisk->recursive = -1;
	 	$this->Category->AssessmentNihRisk->cacher = true;
		if(!$assessmentNihRisk = $this->Category->AssessmentNihRisk->read(null, $assessment_nih_risk_id))
		{
			throw new NotFoundException(__('Unknown %s', __('User Risk')));
		}
		$this->set('assessmentNihRisk', $assessmentNihRisk);
		
		$conditions = [
			'Category.assessment_nih_risk_id' => $assessment_nih_risk_id,
		];
		
		if(!$this->Common->roleCheck('admin'))
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('User Risk'), $assessmentNihRisk['AssessmentNihRisk']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function combined_view($combined_view_id = false) 
	{
		$this->Category->CombinedView->recursive = -1;
	 	$this->Category->CombinedView->cacher = true;
		if(!$combinedView = $this->Category->CombinedView->read(null, $combined_view_id))
		{
			throw new NotFoundException(__('Unknown %s', __('View')));
		}
		$this->set('combinedView', $combinedView);
		
		$conditions = $this->Category->conditionsAvailable(AuthComponent::user('id'));
		
		$conditions['Category.id'] = $this->Category->CombinedView->categoryIds($combined_view_id);
		
		$page_subtitle = __('Assigned to %s: %s', __('View'), $combinedView['CombinedView']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function vector($vector_id = false) 
	{
		$this->Category->Vector->recursive = -1;
		if(!$vector = $this->Category->Vector->read(null, $vector_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Vector')));
		}
		$this->set('vector', $vector);
		
		$conditions = [
			'CategoriesVector.vector_id' => $vector_id,
			'CategoriesVector.active' => 1,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$this->Category->CategoriesVector->recursive = 2;
		
		$this->paginateModel = 'CategoriesVector';
		$this->paginate['contain'] = [
			'Category.User', 'Category.CategoryType', 'Category.OrgGroup', 'Category.CategoriesDetail', 
			'Category.AssessmentNihRisk', 'Category.AssessmentCustRisk', 
			'Category.AdAccount', 'Category.Sac', 'Category.Sac.Branch', 'Category.Sac.Branch.Division', 'Category.Sac.Branch.Division.Org',
		];
		$page_subtitle = __('Related to %s: %s', __('Vector'), $vector['Vector']['vector']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function signature($signature_id = false) 
	{
		$this->Category->Signature->recursive = -1;
		if(!$signature = $this->Category->Signature->read(null, $signature_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Signature')));
		}
		$this->set('signature', $signature);
		
		$conditions = [
			'CategoriesSignature.signature_id' => $signature_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$this->Category->CategoriesSignature->recursive = 2;
		
		$this->paginateModel = 'CategoriesSignature';
		$this->paginate['contain'] = [
			'Category.User', 'Category.CategoryType', 'Category.OrgGroup', 'Category.CategoriesDetail', 
			'Category.AssessmentNihRisk', 'Category.AssessmentCustRisk', 
			'Category.AdAccount', 'Category.Sac', 'Category.Sac.Branch', 'Category.Sac.Branch.Division', 'Category.Sac.Branch.Division.Org',
		];
		$page_subtitle = __('Related to %s: %s', __('Signature'), $signature['Signature']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if(!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Category->Tag->read(null, $tag_id);
		if(!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = [];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		$conditions[] = $this->Category->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Category');
		
		$page_subtitle = __('Tagged with %s: %s', __('Tag'), $tag['Tag']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function category_type($category_type_id = false) 
	{
		$this->Category->CategoryType->recursive = -1;
	 	$this->Category->CategoryType->cacher = true;
		if(!$category_type = $this->Category->CategoryType->read(null, $category_type_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category Group')));
		}
		$this->set('category_type', $category_type);
		
		$conditions = [
			'Category.category_type_id' => $category_type_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Category.public' => 2,
				['Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')],
				['Category.public' => 0, 'Category.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('Category Group'), $category_type['CategoryType']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function compare($category_id_1 = false, $category_id_2 = false)
	{
		// make sure they exist
		$this->Category->id = $category_id_1;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->Category->id = $category_id_2;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		// make sure the user can view both categories
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Category->isOwnedBy($category_id_1, AuthComponent::user('id')) or $this->Category->isPublic($category_id_1)
		)
		and
		(
			$this->Category->isOwnedBy($category_id_1, AuthComponent::user('id')) or $this->Category->isPublic($category_id_1)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view one of the %s.', __('Categories')));
		}
		
		$this->Category->recursive = 0;
		$this->Category->contain(['User', 'CategoryType', 'CategoriesDetail']);
		$category_1 = $this->Category->read(null, $category_id_1);
		$this->Category->recursive = 0;
		$this->Category->contain(['User', 'CategoryType', 'CategoriesDetail']);
		$category_2 = $this->Category->read(null, $category_id_2);
		
		// compare the strings
		$this->Category->recursive = -1;
		$this->set('comparisons', $this->Category->compare($category_id_1, $category_id_2));
		$this->set('category_1', $category_1);
		$this->set('category_2', $category_2);
	}
	
	public function view($id = null) 
	{
		$this->Category->contain([
			'User', 'OrgGroup', 'CategoriesDetail', 'CategoryType', 
			'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		]);
		if(!$category = $this->Category->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		// editor/contributor test
		$this->set('is_editor', $this->Category->CategoriesEditor->isEditor($id, AuthComponent::user('id')));
		$this->set('is_contributor', $this->Category->CategoriesEditor->isContributor($id, AuthComponent::user('id')));
	}
	
	public function edit($id = null) 
	{
		$this->Category->contain(['CategoriesDetail', 'Tag', 'AdAccount']);
		if (!$category = $this->Category->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Category->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been updated', __('Category')));
				return $this->redirect(['action' => 'view', $this->Category->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Category')));
			}
		}
		else
		{
			$this->request->data = $category;
		}
		
		$categoryTypes = $this->Category->CategoryType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('categoryTypes', $categoryTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->Category->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->Category->AssessmentNihRisk->typeFormList();
		$sacs = $this->Category->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->Category->CategoriesEditor->editorsList($this->Category->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
		
		$sacs = $this->Category->Sac->typeFormList();
		$this->set(compact('sacs'));
	}
	
	public function edit_editor($id = null) 
	{
		$this->Category->id = $id;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Category->saveEditor($this->request->data, AuthComponent::user())) 
			{
				$this->Flash->success(__('The %s has been updated', __('Category')));
				return $this->redirect(['action' => 'view', $this->Category->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Category')));
			}
		}
		else
		{
			$this->Category->recursive = 0;
			$this->Category->contain(['CategoriesDetail', 'Tag']);
			$this->request->data = $this->Category->read(null, $id);
		}
	}
	
	public function edit_contributor($id = null) 
	{
		$this->Category->id = $id;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Category->saveContributor($this->request->data, AuthComponent::user())) 
			{
				$this->Flash->success(__('The %s has been updated', __('Category')));
				return $this->redirect(['action' => 'view', $this->Category->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Category')));
			}
		}
		else
		{
			$this->Category->recursive = 0;
			$this->Category->contain(['CategoriesDetail', 'Tag']);
			$this->request->data = $this->Category->read(null, $id);
		}
	}
	
	public function auto_complete($field = false, $user_id = false)
	{
		if(!$field) $field = 'mysource';
		if(!$user_id) $user_id = AuthComponent::user('id');
		
		$terms = $this->Category->find('all', [
			'conditions' => [
				'Category.user_id' => $user_id,
				'Category.'.$field.' LIKE' => $this->params['url']['autoCompleteText'].'%'
			],
			'fields' => ['Category.'.$field],
			'limit' => 20,
			'recursive'=> -1,
		]);
		$terms = Set::Extract($terms,'{n}.Category.'.$field);
		$this->set('_serialize', ['terms']);
		$this->set('terms', $terms);
	}
	
	public function toggle($field = null, $id = null)
	{
		if($this->Category->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Category')));
		}
		else
		{
			$this->Flash->error($this->Category->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function delete($id = null) 
	{
		$this->Category->id = $id;
		if(!$this->Category->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		if($this->Category->delete())
		{
			$this->Flash->success(__('%s deleted', __('Category')));
			return $this->redirect(['action' => 'mine']);
		}
		$this->Flash->error(__('%s was not deleted', __('Category')));
		return $this->redirect(['action' => 'mine']);
	}
	
	public function admin_index() 
	{
		$page_subtitle = '';
		$this->set(compact(['page_subtitle', 'page_description']));
		return $this->index();
	}
	
	public function admin_category($category_id = false) 
	{
		return $this->category($category_id);
	}
	
	public function admin_report($report_id = null) 
	{
		return $this->report($report_id);
	}
	
	public function admin_upload($upload_id = null) 
	{
		return $this->upload($upload_id);
	}
	
	public function admin_import($import_id = null) 
	{
		return $this->import($import_id);
	}
	
	public function admin_dump($dump_id = null) 
	{
		return $this->dump($dump_id);
	}
	
	public function admin_user($user_id = false) 
	{
		return $this->user($user_id);
	}
	
	public function admin_vector($vector_id = false) 
	{
		return $this->vector($vector_id);
	}
	
	public function admin_tag($tag_id = null) 
	{
		return $this->tag($tag_id);

	}
	
	public function admin_group($id = 0)
	{
		$conditions = ['Category.org_group_id' => $id];
		
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function admin_category_type($category_type_id = false) 
	{
		return $this->category_type($category_type_id);
	}
	
	public function admin_compare($category_id_1 = false, $category_id_2 = false)
	{
		// make sure they exist
		$this->Category->id = $category_id_1;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->Category->id = $category_id_2;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->Category->recursive = 0;
		$this->Category->contain(['User', 'OrgGroup']);
		$category_1 = $this->Category->read(null, $category_id_1);
		$this->Category->recursive = 0;
		$this->Category->contain(['User', 'OrgGroup']);
		$category_2 = $this->Category->read(null, $category_id_2);
		
		// compare the strings
		$this->Category->recursive = -1;
		$this->set('comparisons', $this->Category->compare($category_id_1, $category_id_2, true));
		$this->set('category_1', $category_1);
		$this->set('category_2', $category_2);
	}
	
	public function admin_view($id = null) 
	{
		return $this->view($id);
	}
	
	public function admin_edit($id = null) 
	{
		return $this->edit($id);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
		if($this->Category->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Category')));
		}
		else
		{
			$this->Flash->error($this->Category->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_delete($id = null) 
	{
		if(!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->Category->id = $id;
		if(!$this->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		if($this->Category->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('Category')));
			return $this->redirect(['action' => 'index']);
		}
		$this->Flash->error(__('%s was not deleted', __('Category')));
		return $this->redirect(['action' => 'index']);
	}
}

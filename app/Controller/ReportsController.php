<?php
App::uses('AppController', 'Controller');

class ReportsController extends AppController 
{
	public function isAuthorized($user = [])
	{
		// All registered users can add posts
		if ($this->action === 'add')
		{
			return $this->redirect(['controller' => 'temp_reports', 'action' => 'add']);
		}
		
		// if anyone else can view this report. if it's a public one and part of the same org group
		if(in_array($this->action, ['view'])) 
		{
			$reportId = $this->request->params['pass'][0];
			
			if($this->Report->isOwnedBy($reportId, AuthComponent::user('id')))
			{
				return true;
			}
			
			$public = $this->Report->isPublic($reportId);
			if($public == 2)
			{
				return true;
			}
			elseif($public == 1)
			{
				if ($this->Report->isSameOrgGroup($reportId, AuthComponent::user('org_group_id')))
				{
					return true;
				}
				$this->Flash->error(__('You don\'t have access to that %s.', __('Report')));
				return $this->redirect(['action' => 'index']);
			}
			elseif($public == 0)
			{
				$this->Flash->error(__('You don\'t have access to that %s.', __('Report')));
				return $this->redirect(['action' => 'index']);
			}
		}
		
		// allowed editors
		if (in_array($this->action, ['edit_editor'])) 
		{
			$reportId = $this->request->params['pass'][0];
			if ($this->Report->ReportsEditor->isEditor($reportId, AuthComponent::user('id')))
			{
				return true;
			}
			$this->Flash->error(__('You don\'t have access to modify this %s.', __('Report')));
			return $this->redirect(['action' => 'view', $reportId]);
		}
		
		// allowed contributors
		if (in_array($this->action, ['edit_contributor'])) 
		{
			$reportId = $this->request->params['pass'][0];
			if ($this->Report->ReportsEditor->isContributor($reportId, AuthComponent::user('id')))
			{
				return true;
			}
			$this->Flash->error(__('You don\'t have access to modify this %s.', __('Report')));
			return $this->redirect(['action' => 'view', $reportId]);
		}
		
		// The owner of a Report can view, edit and delete it
		if (in_array($this->action, ['toggle', 'edit', 'delete']))
		{
			$reportId = $this->request->params['pass'][0];
			if($this->Report->isOwnedBy($reportId, AuthComponent::user('id')))
			{
				return true;
			}
		}
		return parent::isAuthorized($user);
	}
	
	public function db_block_overview()
	{	
		$results = $this->Report->find('all');
		
		foreach($results as $i => $result)
		{
			$results[$i] = $this->Report->attachFismaSystem($result);
		}
		
		$this->set(compact(['results']));
		
		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_cust_risk_trend()
	{
		$snapshotStats = $this->Report->snapshotDashboardGetStats('/^report\.assessment_cust_risk\-\d+$/');
		$colors = $this->Report->AssessmentCustRisk->find('list', ['fields' => ['AssessmentCustRisk.id', 'AssessmentCustRisk.color_code_hex']]);
		
		$this->set(compact('snapshotStats', 'colors'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_nih_risk_trend()
	{
		$snapshotStats = $this->Report->snapshotDashboardGetStats('/^report\.assessment_nih_risk\-\d+$/');
		$colors = $this->Report->AssessmentNihRisk->find('list', ['fields' => ['AssessmentNihRisk.id', 'AssessmentNihRisk.color_code_hex']]);
		
		$this->set(compact('snapshotStats', 'colors'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_report_type_trend()
	{
		$snapshotStats = $this->Report->snapshotDashboardGetStats('/^report\.report_type\-\d+$/');
		
		$this->set(compact('snapshotStats', 'colors'));
//		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_tab_totals($scope = 'org', $as_block = false)
	{
		$conditions = [];
		
		$results = $this->scopedResults($scope, $conditions);
		
		$this->set(compact([
			'as_block', 'results',
		]));
		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function db_block_assessment_cust_risk()
	{
		$conditions = $this->Report->conditionsAvailable();
		$conditions['Report.assessment_cust_risk_id >'] = 0;
		
		$reports = $this->Report->find('all', [
			'contain' => ['AssessmentCustRisk'],
			'conditions' => $conditions,
		]);
		
		$assessmentCustRisks = $this->Report->AssessmentCustRisk->find('all');
		
		$this->set(compact(['reports', 'assessmentCustRisks']));
	}
	
	public function db_block_assessment_nih_risk()
	{
		$conditions = $this->Report->conditionsAvailable();
		$conditions['Report.assessment_nih_risk_id >'] = 0;
		
		$reports = $this->Report->find('all', [
			'contain' => ['AssessmentNihRisk'],
			'conditions' => $conditions,
		]);
		
		$assessmentNihRisks = $this->Report->AssessmentNihRisk->find('all');
		
		$this->set(compact(['reports', 'assessmentNihRisks']));
	}
	
	public function db_block_report_type()
	{
		$conditions = $this->Report->conditionsAvailable();
		$conditions['Report.report_type_id >'] = 0;
		
		$reports = $this->Report->find('all', [
			'contain' => ['ReportType'],
			'conditions' => $conditions,
		]);
		
		$reportTypes = $this->Report->ReportType->find('all');
		
		$this->set(compact(['reports', 'reportTypes']));
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
			$this->paginateModel = 'Report';
		
		$conditions = [];
		if(!$this->Common->isAdmin())
		{
			$conditions = $this->Report->conditionsAvailable();
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$page_subtitle = $this->get('page_subtitle');
		$page_description = $this->get('page_description');
		
		$this->Report->recursive = 0;
		
		if(!isset($this->paginate['contain']))
			if(!isset($this->passedArgs['getcount']))
				$this->paginate['contain'] = [
					'User', 'ReportType', 'OrgGroup', 'ReportsDetail', 
					'AssessmentNihRisk', 'AssessmentCustRisk',
					'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
				];
		$this->Report->searchFields[] = 'User.name';
		$this->Report->searchFields[] = 'ReportType.name';
		$this->Report->searchFields[] = 'OrgGroup.name';
		
		$this->paginate['order'] = ['Report.id' => 'desc'];
		$this->paginate['conditions'] = $this->Report->conditions($conditions, $this->passedArgs); 
		
		$reports = $this->paginate($this->paginateModel);
		
		$this->set(compact(['page_subtitle', 'page_description', 'reports']));
		
		$reportTypes = $this->Report->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->Report->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->Report->AssessmentNihRisk->typeFormList();
		$sacs = $this->Report->Sac->typeFormList();
		$this->set(compact(['assessmentCustRisks', 'assessmentNihRisks', 'sacs']));
	}
	
	public function index_global() 
	{
		$conditions = [
			'Report.public' => 2,
		];
		
		$page_subtitle = __('Globally Available');
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function index_org() 
	{
		$conditions = [
			'Report.public' => 1,
			'Report.org_group_id' => AuthComponent::user('org_group_id'),
		];
		
		$org_group = $this->Report->OrgGroup->read(null, AuthComponent::user('org_group_id'));
		
		$page_subtitle = __('Assigned to %s: %s', __('Org Group'), $org_group['OrgGroup']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function mine() 
	{
		$conditions = ['Report.user_id' => AuthComponent::user('id')];
		
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
		
		$org = $this->Report->Sac->Branch->Division->Org->find('first', [
			'conditions' => ['Org.id' => $org_id],
		]);
		if (!$org) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		$this->set('object', $org);
		
		$sac_ids = $this->Report->Sac->idsForOrg($org_id);
		
		$conditions = $this->Report->_buildIndexConditions($sac_ids);
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
		
		$division = $this->Report->Sac->Branch->Division->find('first', $args);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('object', $division);
		
		$sac_ids = $this->Report->Sac->idsForDivision($division_id);
		
		$conditions = $this->Report->_buildIndexConditions($sac_ids);
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
			
		$branch = $this->Report->Sac->Branch->find('first', $args);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('object', $branch);
		
		$sac_ids = $this->Report->Sac->idsForBranch($branch_id);
		
		$conditions = $this->Report->_buildIndexConditions($sac_ids);
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
		
		$sac = $this->Report->Sac->find('first', $args);
		if (!$sac) 
		{
			throw new NotFoundException(__('Invalid %s', __('SAC')));
		}
		$this->set('object', $sac);
		
		$conditions = $this->Report->_buildIndexConditions($sac_id);
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
		
		$adAccount = $this->Report->AdAccount->find('first', $args);
		$this->set('object', $adAccount);
		
		$conditions = $this->Report->_buildIndexConditions($ad_account_id, 'ad_account_id');
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
		
		if (!$object = $this->Report->AdAccount->FismaSystem->find('first', $args))
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA System')));
		}
		$this->set('object', $object);
		
		if(!$conditions = $this->Report->correlateCorRToFismaSystem($object['FismaSystem']))
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
		
		if (!$object = $this->Report->AdAccount->FismaSystem->FismaInventory->find('first', $args))
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA Inventory')));
		}
		$this->set('object', $object);
		
		if(!$conditions = $this->Report->correlateCorRToFismaInventory($object['FismaInventory']))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function category($category_id = null) 
	{
		$this->Report->Vector->Category->recursive = -1;
	 	$this->Report->Vector->Category->cacher = true;
		if(!$category = $this->Report->Vector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
	 	$this->Report->Vector->cacher = true;
		if(!$report_ids = $this->Report->Vector->listCategoryToReportsIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Report.id' => $report_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Category'), $category['Category']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function report($report_id = false) 
	{
		$this->Report->recursive = -1;
	 	$this->Report->cacher = true;
		if(!$report = $this->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
	 	$this->Report->cacher = true;
		if(!$report_ids = $this->Report->listReportRelatedIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Report.id' => $report_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Report'), $report['Report']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function upload($upload_id = null) 
	{
		$this->Report->Vector->Upload->recursive = -1;
	 	$this->Report->Vector->Upload->cacher = true;
		if(!$upload = $this->Report->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('File')));
		}
		$this->set('upload', $upload);
		
	 	$this->Report->Vector->cacher = true;
		if(!$report_ids = $this->Report->Vector->listUploadToReportsIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Report.id' => $report_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('File'), $upload['Upload']['filename']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function import($import_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Report->Vector->Import->recursive = -1;
	 	$this->Report->Vector->Import->cacher = true;
		if(!$import = $this->Report->Vector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
	 	$this->Report->Vector->cacher = true;
		if(!$report_ids = $this->Report->Vector->listImportToReportsIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Report.id' => $report_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Import'), $import['Import']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function dump($dump_id = null) 
	{
		$this->Report->Vector->Dump->recursive = -1;
	 	$this->Report->Vector->Dump->cacher = true;
		if(!$dump = $this->Report->Vector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
	 	$this->Report->Vector->cacher = true;
		if(!$report_ids = $this->Report->Vector->listDumpToReportsIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), $this->Common->isAdmin()))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = [
			'Report.id' => $report_ids,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Related to %s: %s', __('Dump'), $dump['Dump']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function user($user_id = false) 
	{
		$this->Report->User->recursive = -1;
	 	$this->Report->User->cacher = true;
		if(!$user = $this->Report->User->read(null, $user_id))
		{
			throw new NotFoundException(__('Unknown %s', __('User')));
		}
		$this->set('user', $user);
		
		$conditions = [
			'Report.user_id' => $user_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Owned by %s: %s', __('User'), $user['User']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function assessment_cust_risk($assessment_cust_risk_id = false) 
	{
		$this->Report->AssessmentCustRisk->recursive = -1;
	 	$this->Report->AssessmentCustRisk->cacher = true;
		if(!$assessmentCustRisk = $this->Report->AssessmentCustRisk->read(null, $assessment_cust_risk_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Customer Risk')));
		}
		$this->set('assessmentCustRisk', $assessmentCustRisk);
		
		$conditions = [
			'Report.assessment_cust_risk_id' => $assessment_cust_risk_id,
		];
		
		if(!$this->Common->roleCheck('admin'))
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('Customer Risk'), $assessmentCustRisk['AssessmentCustRisk']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function assessment_nih_risk($assessment_nih_risk_id = false) 
	{
		$this->Report->AssessmentNihRisk->recursive = -1;
	 	$this->Report->AssessmentNihRisk->cacher = true;
		if(!$assessmentNihRisk = $this->Report->AssessmentNihRisk->read(null, $assessment_nih_risk_id))
		{
			throw new NotFoundException(__('Unknown %s', __('User Risk')));
		}
		$this->set('assessmentNihRisk', $assessmentNihRisk);
		
		$conditions = [
			'Report.assessment_nih_risk_id' => $assessment_nih_risk_id,
		];
		
		if(!$this->Common->roleCheck('admin'))
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('User Risk'), $assessmentNihRisk['AssessmentNihRisk']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function combined_view($combined_view_id = false) 
	{
		$this->Report->CombinedView->recursive = -1;
	 	$this->Report->CombinedView->cacher = true;
		if(!$combinedView = $this->Report->CombinedView->read(null, $combined_view_id))
		{
			throw new NotFoundException(__('Unknown %s', __('View')));
		}
		$this->set('combinedView', $combinedView);
		
		$conditions = $this->Report->conditionsAvailable(AuthComponent::user('id'));
		
		$conditions['Report.id'] = $this->Report->CombinedView->reportIds($combined_view_id);
		
		$page_subtitle = __('Assigned to %s: %s', __('View'), $combinedView['CombinedView']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function vector($vector_id = false) 
	{
		$this->Report->Vector->recursive = -1;
		if(!$vector = $this->Report->Vector->read(null, $vector_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Vector')));
		}
		$this->set('vector', $vector);
		
		$conditions = [
			'ReportsVector.vector_id' => $vector_id,
			'ReportsVector.active' => 1,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$this->Report->ReportsVector->recursive = 2;
		
		$this->paginateModel = 'ReportsVector';
		$this->paginate['contain'] = [
			'Report.User', 'Report.ReportType', 'Report.OrgGroup', 'Report.ReportsDetail', 
			'Report.AssessmentNihRisk', 'Report.AssessmentCustRisk', 
			'Report.AdAccount', 'Report.Sac', 'Report.Sac.Branch', 'Report.Sac.Branch.Division', 'Report.Sac.Branch.Division.Org',
		];
		$page_subtitle = __('Related to %s: %s', __('Vector'), $vector['Vector']['vector']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function signature($signature_id = false) 
	{
		$this->Report->Signature->recursive = -1;
		if(!$signature = $this->Report->Signature->read(null, $signature_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Signature')));
		}
		$this->set('signature', $signature);
		
		$conditions = [
			'ReportsSignature.signature_id' => $signature_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$this->Report->ReportsSignature->recursive = 2;
		
		$this->paginateModel = 'ReportsSignature';
		$this->paginate['contain'] = [
			'Report.User', 'Report.ReportType', 'Report.OrgGroup', 'Report.ReportsDetail', 
			'Report.AssessmentNihRisk', 'Report.AssessmentCustRisk', 
			'Report.AdAccount', 'Report.Sac', 'Report.Sac.Branch', 'Report.Sac.Branch.Division', 'Report.Sac.Branch.Division.Org',
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
		
		$tag = $this->Report->Tag->read(null, $tag_id);
		if(!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = [];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		$conditions[] = $this->Report->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Report');
		
		$page_subtitle = __('Tagged with %s: %s', __('Tag'), $tag['Tag']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function report_type($report_type_id = false) 
	{
		$this->Report->ReportType->recursive = -1;
	 	$this->Report->ReportType->cacher = true;
		if(!$report_type = $this->Report->ReportType->read(null, $report_type_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report Group')));
		}
		$this->set('report_type', $report_type);
		
		$conditions = [
			'Report.report_type_id' => $report_type_id,
		];
		
		if(!$this->Common->isAdmin())
		{
			$conditions['OR'] = [
				'Report.public' => 2,
				['Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')],
				['Report.public' => 0, 'Report.user_id' => AuthComponent::user('id')],
			];
		}
		
		$page_subtitle = __('Assigned to %s: %s', __('Report Group'), $report_type['ReportType']['name']);
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function compare($report_id_1 = false, $report_id_2 = false)
	{
		$this->Report->recursive = 0;
		if (!$report_1 = $this->Report->read(null, $report_id_1)) 
		{
			throw new NotFoundException(__('Invalid %s. (1)', __('Report')));
		}
		
		if (!$report_2 = $this->Report->read(null, $report_id_2)) 
		{
			throw new NotFoundException(__('Invalid %s. (2)', __('Report')));
		}
		
		// make sure the user can view both reports
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Report->isOwnedBy($report_id_1, AuthComponent::user('id')) or $this->Report->isPublic($report_id_1)
		)
		and
		(
			$this->Report->isOwnedBy($report_id_1, AuthComponent::user('id')) or $this->Report->isPublic($report_id_1)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view one of the %s.', __('Reports')));
		}
		
		// compare the strings
		$this->Report->recursive = -1;
		$this->set('comparisons', $this->Report->compare($report_id_1, $report_id_2));
		$this->set('report_1', $report_1);
		$this->set('report_2', $report_2);
	}
	
	public function view($id = null) 
	{
		$this->Report->contain([
			'User', 'OrgGroup', 'ReportsDetail', 'ReportType', 
			'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		]);
		if(!$report = $this->Report->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		// editor/contributor test
		$this->set('is_editor', $this->Report->ReportsEditor->isEditor($id, AuthComponent::user('id')));
		$this->set('is_contributor', $this->Report->ReportsEditor->isContributor($id, AuthComponent::user('id')));
	}
	
	public function edit($id = null) 
	{
		$this->Report->contain(['ReportsDetail', 'Tag', 'AdAccount']);
		if (!$report = $this->Report->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Report->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been updated', __('Report')));
				return $this->redirect(['action' => 'view', $this->Report->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Report')));
			}
		}
		else
		{
			$this->request->data = $report;
		}
		
		// get the category types
		$reportTypes = $this->Report->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->Report->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->Report->AssessmentNihRisk->typeFormList();
		$sacs = $this->Report->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->Report->ReportsEditor->editorsList($this->Report->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function edit_editor($id = null) 
	{
		$this->Report->id = $id;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Report->saveEditor($this->request->data, AuthComponent::user())) 
			{
				$this->Flash->success(__('The %s has been updated', __('Report')));
				return $this->redirect(['action' => 'view', $this->Report->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Report')));
			}
		}
		else
		{
			$this->Report->recursive = 0;
			$this->Report->contain(['ReportsDetail', 'Tag']);
			$this->request->data = $this->Report->read(null, $id);
		}
	}
	
	public function edit_contributor($id = null) 
	{
		$this->Report->id = $id;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Report->saveContributor($this->request->data, AuthComponent::user())) 
			{
				$this->Flash->success(__('The %s has been updated', __('Report')));
				return $this->redirect(['action' => 'view', $this->Report->id]);
			}
			else
			{
				$this->Flash->error(__('The %s could not be updated. Please, try again.', __('Report')));
			}
		}
		else
		{
			$this->Report->recursive = 0;
			$this->Report->contain(['ReportsDetail']);
			$this->request->data = $this->Report->read(null, $id);
		}
	}
	
	public function toggle($field = null, $id = null)
	{
		if ($this->Report->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Report')));
		}
		else
		{
			$this->Flash->error($this->Report->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function auto_complete($field = false, $user_id = false)
	{
		if(!$field) $field = 'mysource';
		if(!$user_id) $user_id = AuthComponent::user('id');
		
		$terms = $this->Report->find('all', [
			'conditions' => [
				'Report.user_id' => $user_id,
				'Report.'.$field.' LIKE' => $this->params['url']['autoCompleteText'].'%'
			],
			'fields' => ['Report.'.$field],
			'limit' => 20,
			'recursive'=> -1,
		]);
		$terms = Set::Extract($terms,'{n}.Report.'.$field);
		$this->set('_serialize', ['terms']);
		$this->set('terms', $terms);
	}
	
	public function delete($id = null) 
	{
		$this->Report->id = $id;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if ($this->Report->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('Report')));
			return $this->redirect(['action' => 'mine']);
		}
		$this->Flash->error(__('%s was not deleted', __('Report')));
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
		$conditions = ['Report.org_group_id' => $id];
		
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function admin_report_type($report_type_id = false) 
	{
		return $this->report_type($report_type_id);
	}
	
	public function admin_compare($report_id_1 = false, $report_id_2 = false)
	{
		// make sure they exist
		$this->Report->id = $report_id_1;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->Report->id = $report_id_2;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->Report->recursive = 0;
		$this->Report->contain(['User', 'OrgGroup']);
		$report_1 = $this->Report->read(null, $report_id_1);
		$this->Report->recursive = 0;
		$this->Report->contain(['User', 'OrgGroup']);
		$report_2 = $this->Report->read(null, $report_id_2);
		
		// compare the strings
		$this->Report->recursive = -1;
		$this->set('comparisons', $this->Report->compare($report_id_1, $report_id_2, true));
		$this->set('report_1', $report_1);
		$this->set('report_2', $report_2);
	}
	
	public function admin_view($id = null) 
	{
		return $this->view($id);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
		if ($this->Report->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Report')));
		}
		else
		{
			$this->Flash->error($this->Report->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_edit($id = null) 
	{
		return $this->edit($id);
	}
	
	public function admin_delete($id = null) 
	{
		$this->Report->id = $id;
		if (!$this->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if ($this->Report->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('Report')));
			return $this->redirect(['action' => 'index']);
		}
		$this->Flash->error(__('%s was not deleted', __('Report')));
		return $this->redirect(['action' => 'index']);
	}
}

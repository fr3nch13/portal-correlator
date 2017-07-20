<?php
App::uses('AppController', 'Controller');
/**
 * TempReports Controller
 *
 * @property TempReport $TempReport
 */
class TempReportsController extends AppController 
{
	public function isAuthorized($user = array())
	{
		// All registered users can add posts
		if ($this->action === 'add')
		{
			return true;
		}
		
		// The owner of a TempReport can view, edit and delete it
		if (in_array($this->action, array('view', 'toggle', 'edit', 'delete'))) 
		{
			$temp_reportId = $this->request->params['pass'][0];
			if ($this->TempReport->isOwnedBy($temp_reportId, AuthComponent::user('id')))
			{
				return true;
			}
		}
		return parent::isAuthorized($user);
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		if(!isset($this->paginateModel))
			$this->paginateModel = 'TempReport';
		
		$conditions = $this->conditions;
		
		$conditions['TempReport.user_id'] = AuthComponent::user('id');
		
		$page_subtitle = (isset($this->viewVars['page_subtitle'])?$this->viewVars['page_subtitle']:__('Mine'));
		$page_description = (isset($this->viewVars['page_description'])?$this->viewVars['page_description']:'');
		
		$this->TempReport->recursive = 0;
		if(!isset($this->passedArgs['getcount']))
			$this->paginate['contain'] = array(
			'User', 'OrgGroup', 'TempReportsDetail', 'ReportType', 
			'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		);
		$this->TempReport->searchFields[] = 'ReportType.name';
		$this->paginate['order'] = array('TempReport.id' => 'desc');
		$this->paginate['conditions'] = $this->TempReport->conditions($conditions, $this->passedArgs); 
		$temp_reports = $this->paginate($this->paginateModel);
		
		$this->set(compact(array('page_subtitle', 'page_description', 'temp_reports')));
		
		$reportTypes = $this->TempReport->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempReport->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempReport->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempReport->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
	}
	
	public function report_type($report_type_id = false) 
	{
		$this->TempReport->ReportType->recursive = -1;
	 	$this->TempReport->ReportType->cacher = true;
		if(!$report_type = $this->TempReport->ReportType->read(null, $report_type_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report Group')));
		}
		$this->set('report_type', $report_type);
		
		$conditions = array(
			'TempReport.report_type_id' => $report_type_id,
		);
		
		$page_subtitle = __('Assigned to %s: %s', __('Report Group'), $report_type['ReportType']['name']);
		$this->set(compact(array('page_subtitle', 'page_description')));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->TempReport->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions[] = $this->TempReport->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'TempReport');
		
		$page_subtitle = __('Tagged with %s: %s', __('Tag'), $tag['Tag']['name']);
		$this->set(compact(array('page_subtitle', 'page_description')));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function view($id = null) 
	{
		$this->TempReport->contain(array(
			'User', 'OrgGroup', 'TempReportsDetail', 'ReportType', 
			'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		));
		if(!$temp_report = $this->TempReport->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Temp Report')));
		}
		$this->set('temp_report', $temp_report);
	}
	
	public function add() 
	{
		if ($this->request->is('post')) 
		{
			$this->TempReport->create();
			$this->request->data['TempReport']['user_id'] = AuthComponent::user('id');
			$this->request->data['TempReport']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if(isset($this->request->data['TempUpload'][0]['file']))
			{
				if(isset($this->request->data['TempUpload'][0]['file']['error']) and $this->request->data['TempUpload'][0]['file']['error'] === 4)
				{
					unset($this->request->data['TempUpload']);
				}
			}
			
			if ($this->TempReport->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved and is ready for review.', __('Temp Report')));
				$this->bypassReferer = true;
				return $this->redirect(array('action' => 'view', $this->TempReport->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp Report')));
			}
		}
		
		// get the report types
		$reportTypes = $this->TempReport->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempReport->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempReport->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempReport->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->TempReport->TempReportsEditor->editorsList($this->TempReport->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function batchadd() 
	{
		if ($this->request->is('post')) 
		{
			$this->request->data['TempReport']['user_id'] = AuthComponent::user('id');
			$this->request->data['TempReport']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if ($this->TempReport->batchSave($this->request->data)) 
			{
				$this->Flash->success(__('The %s have been saved and is ready for review.', __('Temp Reports')));
				$this->bypassReferer = true;
				return $this->redirect(array('action' => 'view', $this->TempReport->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp Reports')));
			}
		}
		
		// get the report types
		$reportTypes = $this->TempReport->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempReport->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempReport->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempReport->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->TempReport->TempReportsEditor->editorsList($this->TempReport->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function edit($id = null) 
	{
		$this->TempReport->contain(array('TempReportsDetail', 'Tag', 'AdAccount'));
		if (!$tempReport = $this->TempReport->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempReport->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Temp Report')));
				return $this->redirect(array('action' => 'view', $this->TempReport->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp Report')));
			}
		}
		else
		{
			$this->request->data = $tempReport;
		}
		
		// get the report types
		$reportTypes = $this->TempReport->ReportType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('reportTypes', $reportTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempReport->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempReport->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempReport->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->TempReport->TempReportsEditor->editorsList($this->TempReport->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function toggle($field = null, $id = null)
	{
		if ($this->TempReport->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Temp Report')));
		}
		else
		{
			$this->Flash->error($this->TempReport->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function reviewed($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->TempReport->id = $id;
		if (!$this->TempReport->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Report')));
		}
		if ($report_id = $this->TempReport->reviewed($id)) 
		{
			$this->Flash->success(__('%s reviewed', __('Temp Report')));
			$this->bypassReferer = true;
			$this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
		}
		if($this->TempReport->reviewError)
		{
			$this->Flash->error($this->TempReport->reviewError);
		}
		else
		{
			$this->Flash->error(__('%s was not reviewed', __('Temp Report')));
		}
		$this->bypassReferer = true;
		$this->redirect(array('action' => 'view', $id));
	}
	
	public function delete($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->TempReport->id = $id;
		if (!$this->TempReport->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Report')));
		}
		$this->bypassReferer = true;
		if ($this->TempReport->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('Temp Report')));
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('%s was not deleted', __('Temp Report')));
		$this->redirect(array('action' => 'index'));
	}
}

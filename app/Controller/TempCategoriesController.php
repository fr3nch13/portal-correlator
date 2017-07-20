<?php
App::uses('AppController', 'Controller');

class TempCategoriesController extends AppController 
{
	public function isAuthorized($user = array())
	{
		// All registered users can add posts
		if ($this->action === 'add')
		{
			return true;
		}
		
		// The owner of a TempCategory can view, toggle, edit, and delete it
		if (in_array($this->action, array('view', 'toggle', 'edit', 'delete'))) 
		{
			$temp_categoryId = $this->request->params['pass'][0];
			if ($this->TempCategory->isOwnedBy($temp_categoryId, AuthComponent::user('id')))
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
			$this->paginateModel = 'TempCategory';
		
		$conditions = $this->conditions;
		
		$conditions['TempCategory.user_id'] = AuthComponent::user('id');
		
		$page_subtitle = (isset($this->viewVars['page_subtitle'])?$this->viewVars['page_subtitle']:__('Mine'));
		$page_description = (isset($this->viewVars['page_description'])?$this->viewVars['page_description']:'');
		
		$this->TempCategory->recursive = 0;
		if(!isset($this->passedArgs['getcount']))
			$this->paginate['contain'] = array(
			'CategoryType', 'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		);
		$this->TempCategory->searchFields[] = 'CategoryType.name';
		$this->paginate['order'] = array('TempCategory.id' => 'desc');
		$this->paginate['conditions'] = $this->TempCategory->conditions($conditions, $this->passedArgs); 
		$temp_categories = $this->paginate($this->paginateModel);
		
		$this->set(compact(array('page_subtitle', 'page_description', 'temp_categories')));
		
		$categoryTypes = $this->TempCategory->CategoryType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('categoryTypes', $categoryTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempCategory->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempCategory->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempCategory->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
	}
	
	public function category_type($category_type_id = false) 
	{
		$this->TempCategory->CategoryType->recursive = -1;
	 	$this->TempCategory->CategoryType->cacher = true;
		if(!$category_type = $this->TempCategory->CategoryType->read(null, $category_type_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category Group')));
		}
		$this->set('category_type', $category_type);
		
		$conditions = array(
			'TempCategory.category_type_id' => $category_type_id,
		);
		
		$page_subtitle = __('Assigned to %s: %s', __('Category Group'), $category_type['CategoryType']['name']);
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
		
		$tag = $this->TempCategory->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions[] = $this->TempCategory->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'TempCategory');
		
		$page_subtitle = __('Tagged with %s: %s', __('Tag'), $tag['Tag']['name']);
		$this->set(compact(array('page_subtitle', 'page_description')));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function view($id = null) 
	{
		$this->TempCategory->contain(array(
			'User', 'OrgGroup', 'TempCategoriesDetail', 'CategoryType', 
			'AssessmentNihRisk', 'AssessmentCustRisk', 
			'AdAccount', 'Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org',
		));
		if(!$temp_category = $this->TempCategory->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Temp Category')));
		}
		$this->set('temp_category', $temp_category);
	}
	
	public function add() 
	{
		if ($this->request->is('post')) 
		{
			$this->TempCategory->create();
			$this->request->data['TempCategory']['user_id'] = AuthComponent::user('id');
			$this->request->data['TempCategory']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if(isset($this->request->data['TempUpload'][0]['file']))
			{
				if(isset($this->request->data['TempUpload'][0]['file']['error']) and $this->request->data['TempUpload'][0]['file']['error'] === 4)
				{
					unset($this->request->data['TempUpload']);
				}
			}
			
			if ($this->TempCategory->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved and is ready for review.', __('Temp Category')));
				$this->bypassReferer = true;
				return $this->redirect(array('action' => 'view', $this->TempCategory->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp Category')));
			}
		}
		
		$categoryTypes = $this->TempCategory->CategoryType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('categoryTypes', $categoryTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempCategory->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempCategory->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempCategory->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->TempCategory->TempCategoriesEditor->editorsList(false, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function edit($id = null) 
	{
		$this->TempCategory->contain(array('TempCategoriesDetail', 'Tag', 'AdAccount'));
		if (!$tempCategory = $this->TempCategory->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Category')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempCategory->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Temp Category')));
				return $this->redirect(array('action' => 'view', $this->TempCategory->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp Category')));
			}
		}
		else
		{
			$this->request->data = $tempCategory;
		}
		
		$categoryTypes = $this->TempCategory->CategoryType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('categoryTypes', $categoryTypes);
		
		// get the assessment options
		$assessmentCustRisks = $this->TempCategory->AssessmentCustRisk->typeFormList();
		$assessmentNihRisks = $this->TempCategory->AssessmentNihRisk->typeFormList();
		$sacs = $this->TempCategory->Sac->typeFormList();
		$this->set(compact('assessmentCustRisks', 'assessmentNihRisks', 'sacs'));
		
		$editors = $this->TempCategory->TempCategoriesEditor->editorsList($this->TempCategory->id, AuthComponent::user('org_group_id'), AuthComponent::user('id'));
		$this->set('editors', $editors);
	}
	
	public function toggle($field = null, $id = null)
	{
		if ($this->TempCategory->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Temp Category')));
		}
		else
		{
			$this->Flash->error($this->TempCategory->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function reviewed($id = null) 
	{
		$this->TempCategory->id = $id;
		if (!$this->TempCategory->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Category')));
		}
		if ($category_id = $this->TempCategory->reviewed($id)) 
		{
			$this->Flash->success(__('%s Reviewed', __('Temp Category')));
			$this->bypassReferer = true;
			$this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
		}
		if($this->TempCategory->reviewError)
		{
			$this->Flash->error($this->TempCategory->reviewError);
		}
		else
		{
			$this->Flash->error(__('%s was not reviewed', __('Temp Category')));
		}
		$this->bypassReferer = true;
		$this->redirect(array('action' => 'view', $id));
	}
	
	public function delete($id = null) 
	{
		$this->TempCategory->id = $id;
		if (!$this->TempCategory->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Temp Category')));
		}
		$this->bypassReferer = true;
		if ($this->TempCategory->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('Temp Category')));
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('%s was not deleted', __('Temp Category')));
		$this->redirect(array('action' => 'index'));
	}
}

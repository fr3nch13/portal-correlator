<?php
App::uses('AppController', 'Controller');
/**
 * TempUploads Controller
 *
 * @property TempUpload $TempUpload
 */
class TempUploadsController extends AppController 
{

	public function isAuthorized($user = array())
	{
		// All registered users can add uploads
		if ($this->action === 'add')
		{
			return true;
		}
		
		// The owner of a upload can view, edit and delete it
		if (in_array($this->action, array('view', 'download', 'toggle', 'edit', 'delete'))) 
		{
			$uploadId = $this->request->params['pass'][0];
			if ($this->TempUpload->isOwnedBy($uploadId, AuthComponent::user('id')))
			{
				return true;
			}
		}
		return parent::isAuthorized($user);
	}

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array('TempUpload.user_id' => AuthComponent::user('id'));
		
		$this->TempUpload->recursive = 0;
		$this->paginate['contain'] = array('TempCategory', 'TempReport', 'UploadType');
		$this->TempUpload->searchFields[] = 'TempCategory.name';
		$this->TempUpload->searchFields[] = 'TempReport.name';
		$this->TempUpload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function upload_type($upload_type_id = false) 
	{
		$this->TempUpload->UploadType->id = $upload_type_id;
		if (!$this->TempUpload->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('File Type')));
		}
		$this->Prg->commonProcess();
		
		$conditions = array(
			'TempUpload.upload_type_id' => $upload_type_id,
			'TempUpload.user_id' => AuthComponent::user('id'),
		);
		
		$this->TempUpload->recursive=0;
		$this->paginate['order'] = array('TempUpload.name' => 'asc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function temp_category($temp_category_id = null) 
	{
		if (!$temp_category_id) {
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->TempUpload->TempCategory->id = $temp_category_id;
		if (!$this->TempUpload->TempCategory->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		$this->set('temp_category_id', $temp_category_id);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'TempUpload.temp_category_id' => $temp_category_id,
		);
		
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function category($category_id = null) 
	{
		if (!$category_id) {
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		
		$this->TempUpload->Category->id = $category_id;
		if (!$this->TempUpload->Category->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Category')));
		}
		$this->set('category_id', $category_id);
		$this->set('category', $this->TempUpload->Category->read(null, $category_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'TempUpload.category_id' => $category_id,
		);
		
		$this->TempUpload->recursive = 0;
		$this->paginate['contain'] = array('TempUploadAddedUser');
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function temp_report($temp_report_id = null) 
	{
		if (!$temp_report_id) {
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->TempUpload->TempReport->id = $temp_report_id;
		if (!$this->TempUpload->TempReport->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		$this->set('temp_report_id', $temp_report_id);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'TempUpload.temp_report_id' => $temp_report_id,
		);
		
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function report($report_id = null) 
	{
		if (!$report_id) {
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->TempUpload->Report->id = $report_id;
		if (!$this->TempUpload->Report->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		$this->set('report_id', $report_id);
		$this->set('report', $this->TempUpload->Report->read(null, $report_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'TempUpload.report_id' => $report_id,
		);
		
		$this->TempUpload->recursive = 0;
		$this->paginate['contain'] = array('TempUploadAddedUser');
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function tag($tag_id = null)   
	{
		if (!$tag_id) {
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$this->TempUpload->Tag->id = $tag_id;
		if (!$this->TempUpload->Tag->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$tag = $this->TempUpload->Tag->read(null, $tag_id);
		
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array('TempUpload.user_id' => AuthComponent::user('id'));
		$conditions[] = $this->TempUpload->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'TempUpload');
		
		$this->TempUpload->recursive = 0;
		$this->paginate['contain'] = array('TempCategory', 'TempReport', 'UploadType');
		$this->TempUpload->searchFields[] = 'TempCategory.name';
		$this->TempUpload->searchFields[] = 'TempReport.name';
		$this->TempUpload->searchFields[] = 'UploadType.name';
		$this->TempUpload->searchFields[] = 'TempKeyword.temp_keyword';
		$this->paginate['order'] = array('TempUpload.id' => 'desc');
		$this->paginate['conditions'] = $this->TempUpload->conditions($conditions, $this->passedArgs); 
		$this->set('temp_uploads', $this->paginate());
	}

	public function view($id = null) 
	{
		$this->TempUpload->id = $id;
		if (!$this->TempUpload->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('File')));
		}
		
		// get the counts
		$this->TempUpload->getCounts = array(
			'TempUploadsVector' => array(
				'all' => array(
					'conditions' => array(
						'TempUploadsVector.temp_upload_id' => $id,
					),
				),
			),
			'Tagged' => array( 
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'TempUpload',
						'Tagged.foreign_key' => $id
					),
				),
			),
		);
		
		$this->TempUpload->recursive = 0;
		$this->set('temp_upload', $this->TempUpload->read(null, $id));
	}

	public function download($id = false, $modelClass = false, $filename = false) 
	{
		if(!$params = $this->TempUpload->downloadParams($id))
		{
			throw new NotFoundException($this->TempUpload->modelError);
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}

	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->TempUpload->create();
			// check permissions after the post as well just incase
			if(!$data = $this->TempUpload->checkNewPermissions($this->params['named'], $this->request->data))
			{
				$this->Flash->error($this->TempUpload->modelError);
				return $this->redirect($this->referer());
			}
			$this->request->data = $data;
			
			if ($this->TempUpload->save($this->request->data))
			{
				$redirect = array('action' => 'view', $this->TempUpload->id);
				if($this->TempUpload->saveRedirect)
				{
					$this->bypassReferer = true;
					$redirect = $this->TempUpload->saveRedirect;
				}
				$this->Flash->success(__('The %s(s) has been saved.', __('Temp File')));
				return $this->redirect($redirect);
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Temp File')));
			}
		}
		else
		{
			//// check permission to make sure they're allowed to add a file to a category/report
			// also, fill out some of the fields from the permission check
			$data = $this->TempUpload->checkNewPermissions($this->params['named'], $this->request->data);
			if($data === false)
			{
				$this->Flash->error($this->TempUpload->modelError);
				return $this->redirect($this->referer());
			}
			$this->request->data = $data;
		}
		
		// get the upload types
		$uploadTypes = $this->TempUpload->UploadType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('uploadTypes', $uploadTypes);
	}

	public function edit($id = null) 
	{
		$this->TempUpload->id = $id;
		if (!$this->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('File')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TempUpload->saveAssociated($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('File')));
				$this->redirect(array('action' => 'view', $id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('File')));
			}
		}
		else
		{
			$this->TempUpload->recursive = 0;
			$this->TempUpload->contain(array('%s', __('Tag')));
			$this->request->data = $this->TempUpload->read(null, $id);
		}
		
		// get the upload types
		$uploadTypes = $this->TempUpload->UploadType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('uploadTypes', $uploadTypes);
	}
	
	public function toggle($field = null, $id = null)
	{
		if ($this->TempUpload->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('Temp File')));
		}
		else
		{
			$this->Flash->error($this->TempUpload->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function reviewed($id = null, $category_id = 0, $report_id = 0) 
	{
		$this->TempUpload->id = $id;
		if (!$this->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('File')));
		}
		if ($upload_ids = $this->TempUpload->reviewed($id)) 
		{
			$this->Flash->success(__('%s reviewed', __('File')));
			if($category_id)
			{
				$this->bypassReferer = true;
				$this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			elseif($report_id)
			{
				$this->bypassReferer = true;
				$this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			elseif(count($upload_ids) == 1)
			{
				$this->Flash->success(__('%s reviewed', __('File')));
				$this->bypassReferer = true;
				$this->redirect(array('controller' => 'uploads', 'action' => 'view', array_pop($upload_ids)));
			}
		}
		if($this->TempUpload->reviewError)
		{
			$this->Flash->error(implode("<br />", $this->TempUpload->reviewError));
		}
		else
		{
			$this->Flash->error(__('%s was not reviewed', __('File')));
		}
		$this->redirect(array('action' => 'view', $id));
	}

	public function delete($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->TempUpload->id = $id;

		if (!$this->TempUpload->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('File')));
		}

		if ($this->TempUpload->delete()) 
		{
			$this->Flash->success(__('%s deleted', __('File')));
			$this->redirect($this->referer());
		}
		
		$this->Flash->error(__('%s was not deleted', __('File')));
		$this->redirect($this->referer());
	}
}

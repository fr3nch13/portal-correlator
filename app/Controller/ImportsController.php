<?php
App::uses('AppController', 'Controller');
/**
 * Imports Controller
 *
 * @property Imports $Imports
 */
class ImportsController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		// include just the user information
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('ImportManager');
		$this->paginate['order'] = array('Import.name' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function import_manager($import_manager_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Import.import_manager_id' => $import_manager_id,
		);
		
		// include just the user information
		$this->paginate['order'] = array('Import.name' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function category($category_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Category->recursive = -1;
	 	$this->Import->Vector->Category->cacher = true;
		if(!$category = $this->Import->Vector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listCategoryToImportsIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function report($report_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Report->recursive = -1;
	 	$this->Import->Vector->Report->cacher = true;
		if(!$report = $this->Import->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listReportToImportsIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function upload($upload_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Upload->recursive = -1;
	 	$this->Import->Vector->Upload->cacher = true;
		if(!$upload = $this->Import->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listUploadToImportsIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function import($import_id = false) 
	{
		$this->Import->recursive = -1;
	 	$this->Import->cacher = true;
		if(!$import = $this->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->Prg->commonProcess();
		
	 	$this->Import->cacher = true;
		if(!$import_ids = $this->Import->listImportRelatedIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User', 'ImportManager');
		$this->Import->searchFields[] = 'User.name';
		$this->Import->searchFields[] = 'ImportType.name';
		
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function vector($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->recursive = -1;
	 	$this->Import->Vector->cacher = true;
		if(!$vector = $this->Import->Vector->read(null, $vector_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Vector')));
		}
		$this->set('vector', $vector);
		
		$conditions = array(
			'ImportsVector.vector_id' => $vector_id,
			'ImportsVector.active' => 1,
		);
		
		$this->Import->ImportsVector->recursive = 2;
		$this->paginate['contain'] = array('Import.User');
		$this->paginate['order'] = array('Import.name' => 'asc');
		$this->paginate['conditions'] = $this->Import->ImportsVector->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate('ImportsVector'));
	}
	
//
	public function dump($dump_id = null) 
	{
	/**
	 * Displays a imports related to a dump
	 */
		if (!$dump_id) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->Import->Vector->sqlDumpToImportsRelated($dump_id),
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{ 
		$this->Prg->commonProcess();
		
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Import->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array(
		);
		$conditions[] = $this->Import->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Import');
		
		// include just the user information
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('ImportManager');
		$this->paginate['order'] = array('Import.name' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
//
	public function view($id = null) 
	{
		// get the user information
		$this->Import->recursive = 0;
		if(!$import = $this->Import->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
	}
	
	public function download($id = false, $modelClass = false, $filename = false) 
	{
		if(!$params = $this->Import->downloadParams($id))
		{
			throw new NotFoundException($this->Import->modelError);
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}
	
	public function contents($id = null) 
	{
		if(!$params = $this->Import->downloadParams($id))
		{
			throw new NotFoundException($this->Import->modelError);
		}
		
		$file_path = $params['path']. $params['id'];
		if(!file_exists($file_path))
		{
			throw new NotFoundException(__('File doesn\'t exist: %s', $params['id']));
		}
		
		if(!is_readable($file_path))
		{
			throw new ForbiddenException(__('Unable to read the content of the file: %s', $params['id']));
		}
		
		if(!$content = file_get_contents($file_path))
		{
			throw new InternalErrorException(__('Unable to read the content of the file: %s', $params['id']));
		}
		
		$this->set('content', $content);
		$this->set('vectors', $this->Import->ImportsVector->vectorsForHighlight($id));
	}
	
//
	public function reviewed($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		
		$this->Import->id = $id;
		if (!$this->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		
		if ($import_id = $this->Import->reviewed($id)) 
		{
			$this->Session->setFlash(__('Import reviewed'));
			return $this->redirect(array('controller' => 'imports', 'action' => 'view', $id));
		}
		
		if($this->Import->reviewError)
		{
			$this->Session->setFlash($this->Import->reviewError);
		}
		else
		{
			$this->Session->setFlash(__('Import was not reviewed'));
		}
		
		return $this->redirect(array('action' => 'view', $id));
	}
	
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		// include just the user information
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('ImportManager');
		$this->paginate['order'] = array('Import.name' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function admin_import_manager($import_manager_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Import.import_manager_id' => $import_manager_id,
		);
		
		// include just the user information
		$this->paginate['order'] = array('Import.name' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
//
	public function admin_vector($vector_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays the imports for a vector
	 */
		$this->Import->Vector->id = $vector_id;
		if (!$this->Import->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid vector'));
		}
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ImportsVector.vector_id' => $vector_id,
		);
		
		$this->Import->ImportsVector->recursive = 2;
		$this->paginate['contain'] = array('Import.User');
		$this->paginate['order'] = array('Import.name' => 'asc');
		$this->paginate['conditions'] = $this->Import->ImportsVector->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate('ImportsVector'));
	}
	
	public function admin_category($category_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Category->recursive = -1;
	 	$this->Import->Vector->Category->cacher = true;
		if(!$category = $this->Import->Vector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listCategoryToImportsIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function admin_report($report_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Report->recursive = -1;
	 	$this->Import->Vector->Report->cacher = true;
		if(!$report = $this->Import->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listReportToImportsIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function admin_upload($upload_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Import->Vector->Upload->recursive = -1;
	 	$this->Import->Vector->Upload->cacher = true;
		if(!$upload = $this->Import->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
	 	$this->Import->Vector->cacher = true;
		if(!$import_ids = $this->Import->Vector->listUploadToImportsIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->Import->searchFields[] = 'User.name';
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
	public function admin_import($import_id = false) 
	{
		$this->Import->recursive = -1;
	 	$this->Import->cacher = true;
		if(!$import = $this->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->Prg->commonProcess();
		
	 	$this->Import->cacher = true;
		if(!$import_ids = $this->Import->listImportRelatedIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Import.id' => $import_ids,
		);
		
		$this->Import->recursive = 0;
		$this->paginate['contain'] = array('User', 'ImportManager');
		$this->Import->searchFields[] = 'User.name';
		$this->Import->searchFields[] = 'ImportType.name';
		
		$this->paginate['order'] = array('Import.id' => 'desc');
		$this->paginate['conditions'] = $this->Import->conditions($conditions, $this->passedArgs); 
		$this->set('imports', $this->paginate());
	}
	
//
	public function admin_view($id = null) 
	{
		// get the user information
		$this->Import->recursive = 0;
		if(!$import = $this->Import->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
	}
	
	public function admin_download($id = null, $modelClass = false)  
	{
		if(!$params = $this->Import->downloadParams($id))
		{
			throw new NotFoundException($this->Import->modelError);
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}
	
//
	public function admin_contents($id = null) 
	{
		if(!$params = $this->Import->downloadParams($id))
		{
			throw new NotFoundException($this->Import->modelError);
		}
		
		$file_path = $params['path']. $params['id'];
		if(!file_exists($file_path))
		{
			throw new NotFoundException(__('File doesn\'t exist: %s', $params['id']));
		}
		
		if(!is_readable($file_path))
		{
			throw new ForbiddenException(__('Unable to read the content of the file: %s', $params['id']));
		}
		
		if(!$content = file_get_contents($file_path))
		{
			throw new InternalErrorException(__('Unable to read the content of the file: %s', $params['id']));
		}
		
		$this->set('content', $content);
		$this->set('vectors', $this->Import->ImportsVector->vectorsForHighlight($id));
	}
	
//
	public function admin_reviewed($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		
		$this->Import->id = $id;
		if (!$this->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		
		if ($import_id = $this->Import->reviewed($id)) 
		{
			$this->Session->setFlash(__('Import reviewed'));
			return $this->redirect(array('controller' => 'imports', 'action' => 'view', $id));
		}
		
		if($this->Import->reviewError)
		{
			$this->Session->setFlash($this->Import->reviewError);
		}
		else
		{
			$this->Session->setFlash(__('Import was not reviewed'));
		}
		
		return $this->redirect(array('action' => 'view', $id));
	}
}
?>
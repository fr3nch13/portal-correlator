<?php
App::uses('AppController', 'Controller');
/**
 * Uploads Controller
 *
 * @property Upload $Upload
 */
class UploadsController extends AppController 
{
	public function isAuthorized($user = array())
	{
	/*
	 * Checks permissions for a user when trying to access a file
	 */
		// All registered users can add uploads
		if($this->action === 'add')
		{
			return $this->redirect(array('controller' => 'temp_uploads', 'action' => 'add'));
		}
		
		if(in_array($this->action, array('view', 'download'))) 
		{
			$uploadId = $this->request->params['pass'][0];
			
			if($this->Upload->isOwnedBy($uploadId, AuthComponent::user('id')))
			{
				return true;
			}
			
			$public = $this->Upload->isPublic($uploadId);
			if($public == 2)
			{
				return true;
			}
			elseif($public == 1)
			{
				if($this->Upload->isSameOrgGroup($uploadId, AuthComponent::user('org_group_id')))
				{
					return true;
				}
				$this->Session->setFlash(__('You don\'t have access to that File.'));
				return $this->redirect(array('action' => 'index'));
			}
			elseif($public == 0)
			{
				$this->Session->setFlash(__('You don\'t have access to that File.'));
				return $this->redirect(array('action' => 'index'));
			}
		}
		
		// The owner of a Category can toggle, edit and delete it
		if(in_array($this->action, array('toggle', 'edit', 'delete', 'transfer_vectors'))) 
		{
			$categoryId = $this->request->params['pass'][0];
			if($this->Upload->isOwnedBy($categoryId, AuthComponent::user('id')))
			{
				return true;
			}
		}
		return parent::isAuthorized($user);
	}
//
	public function index() 
	{
	/**
	 * index method
	 *
	 * Displays all global available Uploads
	 */
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				// Global
				array(
					'Upload.public' => 2,
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 2),
						array('Upload.report_id !=' => 0, 'Report.public' => 2),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				//Org Shared
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'), 
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				// Mine
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->Upload->searchFields[] = 'OrgGroup.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
//
	public function index_global() 
	{
	/**
	 * index method
	 *
	 * Displays all global public Uploads
	 */
	 	// add the ability to search the category/report names
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.public' => 2,
			'OR' => array(
				array(
					'Upload.category_id !=' => 0, 
					'Category.public' => 2, 
				),
				array(
					'Upload.report_id !=' => 0, 
					'Report.public' => 2, 
				),
				array(
					'Upload.category_id' => 0, 
					'Upload.report_id' => 0, 
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function index_org() 
	{
	/**
	 * index method
	 *
	 * Displays all org public Uploads
	 */
	 	// add the ability to search the category/report names
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.public' => 1,
			'Upload.org_group_id' => AuthComponent::user('org_group_id'), 
			'OR' => array(
				array(
					'Upload.category_id !=' => 0, 
					'Category.public' => 1, 
					'Category.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Upload.report_id !=' => 0, 
					'Report.public' => 1, 
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Upload.category_id' => 0, 
					'Upload.report_id' => 0, 
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function mine() 
	{
	/**
	 * index method
	 *
	 * Displays private uploads
	 */
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.user_id' => AuthComponent::user('id'),
		);
		
		$this->paginate['contain'] = array('Category', 'Report', 'UploadType');
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function dump($dump_id = null) 
	{
	/**
	 *
	 * Displays a uploads related to a dump
	 */
		if(!$dump_id) {
			throw new NotFoundException(__('Invalid Dump'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		$conditions[] = $this->Upload->Vector->sqlDumpToUploadsRelated($dump_id);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function category($category_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Upload->Vector->Category->recursive = -1;
	 	$this->Upload->Vector->Category->cacher = true;
		if(!$category = $this->Upload->Vector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		// editor/contributor test
		$is_editor = $this->Upload->Category->CategoriesEditor->isEditor($category_id, AuthComponent::user('id'));
		$is_contributor = $this->Upload->Category->CategoriesEditor->isContributor($category_id, AuthComponent::user('id'));
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'Upload.category_id' => $category_id,
			'OR' => array(
				'Upload.public' => 2,
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'UploadAddedUser');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function report($report_id = null) 
	{
		$this->Prg->commonProcess();
		
		$this->Upload->Vector->Report->recursive = -1;
	 	$this->Upload->Vector->Report->cacher = true;
		if(!$report = $this->Upload->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		// editor/contributor test
		$is_editor = $this->Upload->Report->ReportsEditor->isEditor($report_id, AuthComponent::user('id'));
		$is_contributor = $this->Upload->Report->ReportsEditor->isContributor($report_id, AuthComponent::user('id'));
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'Upload.report_id' => $report_id,
			'OR' => array(
				'Upload.public' => 2,
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'UploadAddedUser');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function upload($upload_id = false) 
	{
		$this->Upload->recursive = -1;
	 	$this->Upload->cacher = true;
		if(!$upload = $this->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->Prg->commonProcess();
		
	 	$this->Upload->cacher = true;
		if(!$upload_ids = $this->Upload->listUploadRelatedIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'Upload.id' => $upload_ids,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'Category', 'Report');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function uploadOLD($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->Upload->id = $upload_id;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		// original conditions. list mine and public ones
		$conditions = array(
			'Upload.id !=' => $upload_id,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$conditions[] = $this->Upload->sqlUploadRelated($upload_id);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'Category', 'Report');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function import($import_id = null) 
	{
	/**
	 * Displays Uploads related to an Import
	 */
		if(!$import_id) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		
		$this->Prg->commonProcess();
		
//		$this->set('import', $this->Upload->Import->read(null, $import_id));
		
		$conditions = array(
			$this->Upload->Vector->sqlImportToUploadsRelated($import_id),
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'UploadAddedUser', 'Category', 'Report');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function user($user_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a report's uploads
	 */
		if(!$user_id) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.user_id' => $user_id,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('UploadType', 'Category', 'Report');
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function vector($vector_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays the uploads for a vector
	 */
		$this->Upload->Vector->id = $vector_id;
		if(!$this->Upload->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid vector'));
		}
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UploadsVector.vector_id' => $vector_id,
			'UploadsVector.active' => 1,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->UploadsVector->recursive = -1;
		$this->Upload->UploadsVector->unbindAllModels();
		$this->paginate['order'] = array('Upload.name' => 'asc');
		$this->paginate['fields'] = array('Upload.*', 'Category.*', 'Report.*', 'User.*', 'Vector.*');
		$this->paginate['joins'] = array(
						array(
							'table' => 'vectors',
							'alias' => 'Vector',
							'type' => 'LEFT',
							'conditions' => array(
								'UploadsVector.vector_id = Vector.id',
							),
						),
						array(
							'table' => 'uploads',
							'alias' => 'Upload',
							'type' => 'LEFT',
							'conditions' => array(
								'UploadsVector.upload_id = Upload.id',
							),
						),
						array(
							'table' => 'categories',
							'alias' => 'Category',
							'type' => 'LEFT',
							'conditions' => array(
								'Category.id = Upload.category_id',
							),
						),
						array(
							'table' => 'reports',
							'alias' => 'Report',
							'type' => 'LEFT',
							'conditions' => array(
								'Report.id = Upload.report_id',
							),
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type' => 'LEFT',
							'conditions' => array(
								'User.id = Upload.user_id',
							),
						),
					);
		$this->paginate['conditions'] = $this->Upload->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads', $this->paginate('UploadsVector'));
	}
	
//
	public function tag($tag_id = null)   
	{
		if(!$tag_id)
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		
		$tag = $this->Upload->Tag->read(null, $tag_id);
		if(!$tag)
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				// Global
				array(
					'Upload.public' => 2,
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 2),
						array('Upload.report_id !=' => 0, 'Report.public' => 2),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				//Org Shared
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'), 
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				// Mine
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		$conditions[] = $this->Upload->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Upload');
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->Upload->searchFields[] = 'OrgGroup.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

	public function upload_type($upload_type_id = false) 
	{
	/**
	 * index method
	 *
	 */
		$this->Upload->UploadType->id = $upload_type_id;
		if(!$this->Upload->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid upload type'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.upload_type_id' => $upload_type_id,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.report_id !=' => 0, 'Report.public' => 2),
					array('Upload.category_id' => 0, 'Upload.report_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.report_id !=' => 0, 'Report.public' => 1, 'Report.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.report_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.name' => 'asc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function compare($upload_id_1 = false, $upload_id_2 = false)
	{
	/*
	 * Compares 2 uploads
	 */
		// make sure they exist
		$this->Upload->id = $upload_id_1;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		$this->Upload->id = $upload_id_2;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		// make sure the user can view both categories
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Upload->isOwnedBy($upload_id_1, AuthComponent::user('id')) or $this->Upload->isPublic($upload_id_1)
		)
		and
		(
			$this->Upload->isOwnedBy($upload_id_1, AuthComponent::user('id')) or $this->Upload->isPublic($upload_id_1)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view one of the files.'));
		}
		
		$this->Upload->recursive = 0;
		$this->Upload->contain(array('User', 'UploadType'));
		$upload_1 = $this->Upload->read(null, $upload_id_1);
		$this->Upload->recursive = 0;
		$this->Upload->contain(array('User', 'UploadType'));
		$upload_2 = $this->Upload->read(null, $upload_id_2);
		
		// compare the strings
		$this->Upload->recursive = -1;
		$this->set('comparisons', $this->Upload->compare($upload_id_1, $upload_id_2));
		$this->set('upload_1', $upload_1);
		$this->set('upload_2', $upload_2);
	}
	
//
	public function view($id = null) 
	{
		// get the user information
		$this->Upload->recursive = 0;
		if(!$upload = $this->Upload->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
	}
	
//
	public function download($id = null, $modelClass = false, $filename = false) 
	{
		if(!$params = $this->Upload->downloadParams($id))
		{
			throw new NotFoundException($this->Upload->modelError);
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}
	
//
	public function edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Upload->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('action' => 'view', $this->Upload->id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->Upload->recursive = 0;
			$this->Upload->contain(array('User', 'UploadType'));
			$this->request->data = $this->Upload->read(null, $id);
		}
		
		// get the upload types
		$uploadTypes = $this->Upload->UploadType->typeFormList(AuthComponent::user('org_group_id'));
		$this->set('uploadTypes', $uploadTypes);
	}
	
//
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if($this->Upload->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The File has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->Upload->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function auto_complete($field = false, $user_id = false)
	{
		if(!$field) $field = 'mysource';
		if(!$user_id) $user_id = AuthComponent::user('id');
		
		$terms = $this->Upload->find('all', array(
			'conditions' => array(
				'Upload.user_id' => $user_id,
				'Upload.'.$field.' LIKE' => $this->params['url']['autoCompleteText'].'%'
			),
			'fields' => array('Upload.'.$field),
			'limit' => 20,
			'recursive'=> -1,
		));
		$terms = Set::Extract($terms,'{n}.Upload.'.$field);
		$this->set('terms', $terms);
		$this->layout = 'ajax';	
	}
	
//
	public function transfer_vectors($id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if($this->Upload->transferVectors($id))
		{
			$this->Session->setFlash(__('The File\'s vectors have been transfered.'));
		}
		else
		{
			$this->Session->setFlash($this->Upload->modelError);
		}
		
		return $this->redirect($this->referer());
	}

//
	public function delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;

		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}

		if($this->Upload->delete()) 
		{
			$this->Session->setFlash(__('File deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('File was not deleted'));
		return $this->redirect($this->referer());
	}
	
/*** Admin Functions ***/


	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all public Categories
	 */
		
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function admin_category($category_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a category's uploads
	 */
		if(!$category_id) {
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->Upload->Category->id = $category_id;
		if(!$this->Upload->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->set('category_id', $category_id);
//		$this->set('category', $this->Upload->Category->read(null, $category_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.category_id' => $category_id,
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_report($report_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a report's uploads
	 */
		if(!$report_id) {
			throw new NotFoundException(__('Invalid report'));
		}
		
		$this->Upload->Report->id = $report_id;
		if(!$this->Upload->Report->exists()) {
			throw new NotFoundException(__('Invalid report'));
		}
		$this->set('report_id', $report_id);
//		$this->set('report', $this->Upload->Report->read(null, $report_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.report_id' => $report_id, 
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_upload($upload_id = false) 
	{
	/**
	 * Lists out all uploads related to the upload id based on their vectors
	 */
		$this->Upload->id = $upload_id;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		$this->Prg->commonProcess();
		
		// original conditions. list mine and public ones
		$conditions = array(
			'Upload.id !=' => $upload_id,
		);
		
		$conditions[] = $this->Upload->sqlUploadRelated($upload_id, true);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'User.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_user($user_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a report's uploads
	 */
		if(!$user_id) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.user_id' => $user_id,
		);
		
		$this->paginate['contain'] = array('Category', 'Report', 'UploadType', 'OrgGroup');
		$this->Upload->searchFields[] = 'Category.name';
		$this->Upload->searchFields[] = 'Report.name';
		$this->Upload->searchFields[] = 'UploadType.name';
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_vector($vector_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays the uploads for a vector
	 */
		$this->Upload->Vector->id = $vector_id;
		if(!$this->Upload->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid vector'));
		}
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UploadsVector.vector_id' => $vector_id,
		);
		
		$this->Upload->UploadsVector->recursive = 2;
		$this->paginate['contain'] = array('Upload.User', 'Upload.Category', 'Upload.Report', 'Upload.OrgGroup');
		$this->paginate['order'] = array('Upload.name' => 'asc');
		$this->paginate['conditions'] = $this->Upload->UploadsVector->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate('UploadsVector'));
	}
	
//
	public function admin_tag($tag_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a report's uploads
	 */
		if(!$tag_id) {
			throw new NotFoundException(__('Invalid Tag'));
		}
		
		$this->Upload->Tag->id = $tag_id;
		if(!$this->Upload->Tag->exists()) {
			throw new NotFoundException(__('Invalid Tag'));
		}
		$tag = $this->Upload->Tag->read(null, $tag_id);
		
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->Upload->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Upload');
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'Category', 'Report', 'OrgGroup');
		$this->paginate['order'] = array('Upload.id' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs);
		$this->set('uploads', $this->paginate());

	}

//
	public function admin_group($id = 0)
	{
	/*
	 * List of Users in the whole system filtered by group. 
	 * Used to manage Users
	 */
	 
		$this->Prg->commonProcess();
		
		$conditions = array('Upload.org_group_id' => $id);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User');
		$this->paginate['order'] = array('Upload.id' => 'asc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}


	public function admin_upload_type($upload_type_id = false) 
	{
	/**
	 * index method
	 *
	 */
		$this->Upload->UploadType->id = $upload_type_id;
		if(!$this->Upload->UploadType->exists()) 
		{
			throw new NotFoundException(__('Invalid upload type'));
		}
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.upload_type_id' => $upload_type_id,
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['contain'] = array('User', 'OrgGroup', 'Category', 'Report');
		$this->paginate['order'] = array('Upload.name' => 'asc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_compare($upload_id_1 = false, $upload_id_2 = false)
	{
	/*
	 * Compares 2 uploads
	 */
		// make sure they exist
		$this->Upload->id = $upload_id_1;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		$this->Upload->id = $upload_id_2;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid file'));
		}
		
		
		$this->Upload->recursive = 0;
		$this->Upload->contain(array('User', 'OrgGroup'));
		$upload_1 = $this->Upload->read(null, $upload_id_1);
		$this->Upload->recursive = 0;
		$this->Upload->contain(array('User', 'OrgGroup'));
		$upload_2 = $this->Upload->read(null, $upload_id_2);
		
		// compare the strings
		$this->Upload->recursive = -1;
		$this->set('comparisons', $this->Upload->compare($upload_id_1, $upload_id_2, true));
		$this->set('upload_1', $upload_1);
		$this->set('upload_2', $upload_2);
	}
	
//
	public function admin_view($id = null) 
	{
		$this->Upload->recursive = 0;
		if(!$upload = $this->Upload->read(null, $id))
		{
			throw new NotFoundException(__('Invalid File'));
		}
		$this->set('upload', $upload);
	}

//
	public function admin_download($id = null, $modelClass = false) 
	{
	/**
	 * download method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		$paths = $this->Upload->paths($id);
		
		$file = new File($paths['sys']);
		if(!$file->exists() || !$file->readable()) 
		{
			throw new NotFoundException(__d('cake', 'The requested file was not found'));
		}
		
		$this->response->file($paths['sys']);
		//Return reponse object to prevent controller from trying to render a view
		return $this->response;
	}
	
//
	public function admin_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			// get user information for things like the org group
			if(isset($this->request->data['Upload']['user_id']))
			{
				$this->Upload->User->id = $this->request->data['Upload']['user_id'];
				$org_group_id = $this->Upload->User->field('org_group_id');
				$this->request->data['Upload']['org_group_id'] = $org_group_id;
			}
			if($this->Upload->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The upload has been saved'));
				return $this->redirect(array('action' => 'view', $this->Upload->id));
			}
			else
			{
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->Upload->recursive = 0;
			$this->Upload->contain(array('User', 'Tag'));
			$this->request->data = $this->Upload->read(null, $id);
		}
		
		$this->set('users', $this->Upload->User->find('list', array(
			'recursive' => 0,
			'fields' => array('User.id', 'User.name'),
		)));
		
		// get the category types
		$uploadTypes = $this->Upload->UploadType->typeFormList($this->request->data['User']['org_group_id']);
		$this->set('uploadTypes', $uploadTypes);
	}

//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if($this->Upload->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The File has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->Upload->modelError);
		}
		
		return $this->redirect($this->referer());
	}

//
	public function admin_delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		if(!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->Upload->id = $id;

		if(!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}

		if($this->Upload->delete()) 
		{
			$this->Session->setFlash(__('File deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('File was not deleted'));
		return $this->redirect($this->referer());
	}
}

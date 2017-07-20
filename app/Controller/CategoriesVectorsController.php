<?php
App::uses('AppController', 'Controller');
/**
 * CategoriesVectors Controller
 *
 * @property CategoriesVector $CategoriesVector
 */
class CategoriesVectorsController extends AppController 
{
	public function category($category_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$is_owner = $this->CategoriesVector->Category->isOwnedBy($category_id, AuthComponent::user('id'));
		$is_editor = $this->CategoriesVector->Category->CategoriesEditor->isEditor($category_id, AuthComponent::user('id'));
		$is_contributor = $this->CategoriesVector->Category->CategoriesEditor->isContributor($category_id, AuthComponent::user('id'));
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'CategoriesVector.category_id' => $category_id, 
			'Vector.bad' => 0,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['CategoriesVector.active'] = 1;
			$conditions['OR'] = array(
				'Category.public' => 2,
				array(
					'Category.public' => 1,
					'Category.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Category.public' => 0,
					'Category.user_id' => AuthComponent::user('id'),
				),
			);
		}
		if(!is_null($active))
		{
			$conditions['CategoriesVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate('CategoriesVector'));
	}
	
	public function category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$is_owner = false;
		$is_editor = false;
		$is_contributor = false;
		$category = false;
		
		$this->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->CategoriesVector->Category->isOwnedBy($category_id, AuthComponent::user('id'));
			$is_editor = $this->CategoriesVector->Category->CategoriesEditor->isEditor($category_id, AuthComponent::user('id'));
			$is_contributor = $this->CategoriesVector->Category->CategoriesEditor->isContributor($category_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Category.id !=' => $category_id,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['CategoriesVector.active'] = 1;
			$conditions['OR'] = array(
				'Category.public' => 2,
				array(
					'Category.public' => 1,
					'Category.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Category.public' => 0,
					'Category.user_id' => AuthComponent::user('id'),
				),
			);
		}
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Report->recursive = -1;
	 	$this->CategoriesVector->Vector->Report->cacher = true;
		if(!$report = $this->CategoriesVector->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->CategoriesVector->Vector->ReportsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->ReportsVector->listVectorIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Import->recursive = -1;
	 	$this->CategoriesVector->Vector->Import->cacher = true;
		if(!$import = $this->CategoriesVector->Vector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->CategoriesVector->Vector->ImportsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->ImportsVector->listVectorIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Upload->recursive = -1;
	 	$this->CategoriesVector->Vector->Upload->cacher = true;
		if(!$upload = $this->CategoriesVector->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->CategoriesVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Dump->recursive = -1;
	 	$this->CategoriesVector->Vector->Dump->cacher = true;
		if(!$dump = $this->CategoriesVector->Vector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->CategoriesVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->DumpsVector->listVectorIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function vt_related($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoriesVector.active' => 1, 
			'Vector.bad' => 0,
			' OR' => array(
				'Category.public' => 2,
				array(
					'Category.public' => 1,
					'Category.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Category.public' => 0,
					'Category.user_id' => AuthComponent::user('id'),
				),
			),
			'OR' => $this->CategoriesVector->sqlVirusTotalAllIds($vector_id),
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function vector_type($vector_type_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoriesVector.vector_type_id' => $vector_type_id, 
			'CategoriesVector.active' => 1, 
			'Vector.bad' => 0,
			'Category.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function unique($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$is_owner = false;
		$is_editor = false;
		$is_contributor = false;
		
		$this->CategoriesVector->Vector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->listCategoriesVectorsUnique($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->CategoriesVector->Category->isOwnedBy($category_id, AuthComponent::user('id'));
			$is_editor = $this->CategoriesVector->Category->CategoriesEditor->isEditor($category_id, AuthComponent::user('id'));
			$is_contributor = $this->CategoriesVector->Category->CategoriesEditor->isContributor($category_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'CategoriesVector.vector_id' => $vector_ids,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['CategoriesVector.active'] = 1;
		}
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
//
	public function add($category_id = null) 
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->set('category_id', $category_id);
		$this->set('category_name', $this->CategoriesVector->Category->field('name'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The vectors have been added'));
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
		if($this->CategoriesVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been saved.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoriesVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function multiselect()
	{
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		$redirect = false;
		
		
		// get the vector_ids
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[$id] = $id; }
			$this->request->data['multiple'] = $ids;
			
			// active/inactive need the xref id
			if(!in_array($this->request->data['CategoriesVector']['multiselect_option'], array('active', 'inactive', 'type', 'multitype', 'delete')))
			{
				$this->request->data['multiple'] = $this->CategoriesVector->find('list', array(
					'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
					'conditions' => array('CategoriesVector.id' => $this->request->data['multiple']),
				));
			}
		}
		
		if($this->request->data['CategoriesVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// Vt Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'hexilliontracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_hexilliontracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multihexilliontracking')
		{
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multihexilliontracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		$this->bypassReferer = true;
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->CategoriesVector->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Vectors'), __('Category')));
			$this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Vectors'), __('Category')));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['CategoriesVector']['vector_type_id'])?$this->request->data['CategoriesVector']['vector_type_id']:0);
			if($this->CategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Category.'));
				$this->bypassReferer = true;
				return $this->redirect($this->CategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->CategoriesVector->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		// get the object types
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['CategoriesVector'])?$this->request->data['CategoriesVector']:array());
			
			if($this->CategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Category.'));
				$this->bypassReferer = true;
				return $this->redirect($this->CategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('CategoriesVector.id' => $ids, 'Vector.bad' => 0);
		$this->CategoriesVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('categories_vectors', $this->paginate('CategoriesVector'));
		
		// get the object types
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category\'s Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Category\'s Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
	public function assign_vector_multitypes($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category\'s Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		$this->CategoriesVector->searchFields = array('Vector.vector');
		
		$this->set('category_id', $category_id);
		$this->set('categories_vectors', $this->CategoriesVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => array_merge(array('CategoriesVector.category_id' => $category_id), $this->CategoriesVector->parseCriteria($this->passedArgs)),
		)));
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
	public function assign_dnstracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
	}
	
	public function assign_hexilliontracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
	}
	
	public function assign_whoistracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
	}
	
/*
 * For later
	public function assign_multidnstracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		$this->CategoriesVector->searchFields = array('Vector.vector');
		
		$this->set('category_id', $category_id);
		$this->set('categories_vectors', $this->CategoriesVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => array_merge(array('CategoriesVector.category_id' => $category_id), $this->CategoriesVector->parseCriteria($this->passedArgs)),
		)));
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
*/

	public function delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoriesVector->id = $id;
		if(!$this->CategoriesVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		$this->bypassReferer = true;
		if($this->CategoriesVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this Category.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Category.'));
		$this->redirect($this->referer());
	}
	
/*** Admin Functions ***/
	
//
	public function admin_add($category_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->set('category_id', $category_id);
		$this->set('category_name', $this->CategoriesVector->Category->field('name'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The vectors have been added'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	public function admin_category($category_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$conditions = array(
			'CategoriesVector.category_id' => $category_id, 
			'Vector.bad' => 0,
		);
		
		if(!is_null($active))
		{
			$conditions['CategoriesVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		$this->set('categories_vectors', $this->paginate('CategoriesVector'));
	}
	
	public function admin_category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Category.id !=' => $category_id,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function admin_report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Report->recursive = -1;
	 	$this->CategoriesVector->Vector->Report->cacher = true;
		if(!$report = $this->CategoriesVector->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->CategoriesVector->Vector->ReportsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->ReportsVector->listVectorIds($report_id))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function admin_import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Import->recursive = -1;
	 	$this->CategoriesVector->Vector->Import->cacher = true;
		if(!$import = $this->CategoriesVector->Vector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->CategoriesVector->Vector->ImportsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->ImportsVector->listVectorIds($import_id))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function admin_upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Upload->recursive = -1;
	 	$this->CategoriesVector->Vector->Upload->cacher = true;
		if(!$upload = $this->CategoriesVector->Vector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->CategoriesVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->UploadsVector->listVectorIds($upload_id))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
	public function admin_dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Vector->Dump->recursive = -1;
	 	$this->CategoriesVector->Vector->Dump->cacher = true;
		if(!$dump = $this->CategoriesVector->Vector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->CategoriesVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->DumpsVector->listVectorIds($dump_id))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.active' => 1,
			'CategoriesVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
//
	public function admin_unique($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->CategoriesVector->Category->recursive = -1;
	 	$this->CategoriesVector->Category->cacher = true;
		if(!$category = $this->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->CategoriesVector->Vector->cacher = true;
		if(!$vector_ids = $this->CategoriesVector->Vector->listCategoriesVectorsUnique($category_id))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'CategoriesVector.vector_id' => $vector_ids,
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		
		$this->set('categories_vectors', $this->paginate());
	}
	
//
	public function admin_vector_type($vector_type_id = false) 
	{
	/**
	 * category method
	 * Shows only good vectors associated with this category
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoriesVector.vector_type_id' => $vector_type_id,
			'Vector.bad' => 0
		);
		
		// adjust the search fields
		$this->CategoriesVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->CategoriesVector->recursive = 0;
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['fields'] = array('CategoriesVector.*', 'Category.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('CategoriesVector.vector_id = VectorDetail.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('categories_vectors', $this->paginate());
	}
	
//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if($this->CategoriesVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been saved.'));
		}
		else
		{
			$this->Session->setFlash($this->CategoriesVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function admin_multiselect()
	{
	/*
	 * batch manage multiple items
	 */
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		$redirect = false;
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
			$this->request->data['multiple'] = $this->CategoriesVector->find('list', array(
				'fields' => array('CategoriesVector.vector_id', 'CategoriesVector.vector_id'),
				'conditions' => array('CategoriesVector.id' => $ids),
			));
		}
		
		if($this->request->data['CategoriesVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// Vt Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['CategoriesVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['CategoriesVector'];
			unset($this->request->data['CategoriesVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		$this->bypassReferer = true;
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->CategoriesVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Category.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['CategoriesVector']['vector_type_id'])?$this->request->data['CategoriesVector']['vector_type_id']:0);
			if($this->CategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Category.'));
				$this->bypassReferer = true;
				return $this->redirect($this->CategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->CategoriesVector->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		// get the object types
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['CategoriesVector'])?$this->request->data['CategoriesVector']:array());
			
			if($this->CategoriesVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Category.'));
				$this->bypassReferer = true;
				return $this->redirect($this->CategoriesVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('CategoriesVector.id' => $ids, 'Vector.bad' => 0);
		$this->CategoriesVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('CategoriesVector.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesVector->conditions($conditions, $this->passedArgs);
		$this->set('categories_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
//
	public function admin_assign_vector_type($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category has been saved'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
//
	public function admin_assign_vector_multitypes($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Category\'s Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The Category could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		$this->CategoriesVector->searchFields = array('Vector.vector');
		
		$this->set('category_id', $category_id);
		$this->set('categories_vectors', $this->CategoriesVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => array_merge(array('CategoriesVector.category_id' => $category_id), $this->CategoriesVector->parseCriteria($this->passedArgs)),
		)));
		$this->set('vectorTypes', $this->CategoriesVector->VectorType->typeFormList());
	}
	
	public function admin_assign_dnstracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
	}
	
	public function admin_assign_hexilliontracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
	}
	
	public function admin_assign_whoistracking($category_id = false)
	{
		$this->CategoriesVector->Category->id = $category_id;
		if(!$this->CategoriesVector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid Category'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->CategoriesVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'categories', 'action' => 'view', $category_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['CategoriesVector']['category_id'] = $category_id;
		}
		
		// get the object types
		$this->set('category_id', $category_id);
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
		$this->CategoriesVector->id = $id;
		if(!$this->CategoriesVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if($this->CategoriesVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this Category.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Category.'));
		$this->redirect($this->referer());
	}
	
	public function admin_stats()
	{
		$results = $this->CategoriesVector->vectorsByType();
	}
}

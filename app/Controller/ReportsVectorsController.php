<?php
App::uses('AppController', 'Controller');
/**
 * ReportsVectors Controller
 *
 * @property ReportsVector $ReportsVector
 */
class ReportsVectorsController extends AppController 
{
//
	public function report($report_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$is_owner = $this->ReportsVector->Report->isOwnedBy($report_id, AuthComponent::user('id'));
		$is_editor = $this->ReportsVector->Report->ReportsEditor->isEditor($report_id, AuthComponent::user('id'));
		$is_contributor = $this->ReportsVector->Report->ReportsEditor->isContributor($report_id, AuthComponent::user('id'));
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'ReportsVector.report_id' => $report_id, 
			'Vector.bad' => 0,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['ReportsVector.active'] = 1;
			$conditions['OR'] = array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			);
		}
		if(!is_null($active))
		{
			$conditions['ReportsVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('reports_vectors', $this->paginate('ReportsVector'));
	}
	
//
	public function report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$is_editor = false;
		$is_contributor = false;
		$is_owner = false;
		$report = false;
		
		$this->ReportsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->listVectorIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->ReportsVector->Report->isOwnedBy($report_id, AuthComponent::user('id'));
			$is_editor = $this->ReportsVector->Report->ReportsEditor->isEditor($report_id, AuthComponent::user('id'));
			$is_contributor = $this->ReportsVector->Report->ReportsEditor->isContributor($report_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Report.id !=' => $report_id,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['ReportsVector.active'] = 1;
			$conditions['OR'] = array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			);
		}
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->CategoriesVector->Category->recursive = -1;
	 	$this->ReportsVector->Vector->CategoriesVector->Category->cacher = true;
		if(!$category = $this->ReportsVector->Vector->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->ReportsVector->Vector->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'OR' => array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->ImportsVector->Import->recursive = -1;
	 	$this->ReportsVector->Vector->ImportsVector->Import->cacher = true;
		if(!$import = $this->ReportsVector->Vector->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ReportsVector->Vector->ImportsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->ImportsVector->listVectorIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'OR' => array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->UploadsVector->Upload->recursive = -1;
	 	$this->ReportsVector->Vector->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->ReportsVector->Vector->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->ReportsVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'OR' => array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->DumpsVector->Dump->recursive = -1;
	 	$this->ReportsVector->Vector->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->ReportsVector->Vector->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->ReportsVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->DumpsVector->listVectorIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'OR' => array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function vt_related($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportsVector.active' => 1, 
			'Vector.bad' => 0,
			' OR' => array(
				'Report.public' => 2,
				array(
					'Report.public' => 1,
					'Report.org_group_id' => AuthComponent::user('org_group_id'),
				),
				array(
					'Report.public' => 0,
					'Report.user_id' => AuthComponent::user('id'),
				),
			),
			'OR' => $this->ReportsVector->sqlVirusTotalAllIds($vector_id),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function vector_type($vector_type_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportsVector.vector_type_id' => $vector_type_id, 
			'ReportsVector.active' => 1, 
			'Vector.bad' => 0,
			'Report.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function unique($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$is_owner = false;
		$is_editor = false;
		$is_contributor = false;
		
		$this->ReportsVector->Vector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->listReportsVectorsUnique($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->ReportsVector->Report->isOwnedBy($report_id, AuthComponent::user('id'));
			$is_editor = $this->ReportsVector->Report->ReportsEditor->isEditor($report_id, AuthComponent::user('id'));
			$is_contributor = $this->ReportsVector->Report->ReportsEditor->isContributor($report_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		$this->set('is_editor', $is_editor);
		$this->set('is_contributor', $is_contributor);
		
		$conditions = array(
			'ReportsVector.vector_id' => $vector_ids,
		);
		
		if(!$is_owner and !$is_editor)
		{
			$conditions['ReportsVector.active'] = 1;
		}
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function add($report_id = null) 
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		
		$this->set('report_id', $report_id);
		$this->set('report_name', $this->ReportsVector->Report->field('name'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s have been added', __('Vectors')));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be added. Please, try again.', __('Vectors')));
			}
		}
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
		if($this->ReportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('Vector')));
		}
		else
		{
			$this->Session->setFlash($this->ReportsVector->modelError);
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
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[$id] = $id; }
			$this->request->data['multiple'] = $ids;
			
			if(!in_array($this->request->data['ReportsVector']['multiselect_option'], array('active', 'inactive', 'type', 'multitype', 'delete')))
			{
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => $this->request->data['multiple']),
					'recursive' => -1,
				));
			}
		}
		if($this->request->data['ReportsVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// VT Tracking
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'hexilliontracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_hexilliontracking');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multihexilliontracking')
		{
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multihexilliontracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		$this->bypassReferer = true;
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->ReportsVector->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Vectors'), __('Report')));
			$this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Vectors'), __('Report')));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportsVector']['vector_type_id'])?$this->request->data['ReportsVector']['vector_type_id']:0);
			if($this->ReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Report.'));
				$this->bypassReferer = true;
				return $this->redirect($this->ReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The %s were NOT updated for this %s.', __('Vectors'), __('Report')));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->ReportsVector->Vector->find('list', array(
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
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportsVector'])?$this->request->data['ReportsVector']:array());
			if($this->ReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Report.'));
				$this->bypassReferer = true;
				return $this->redirect($this->ReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The %s were NOT updated for this %s.', __('Vectors'), __('Report')));
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
		
		$conditions = array('ReportsVector.id' => $ids, 'Vector.bad' => 0);
		$this->ReportsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Report')));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Report')));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_multitypes($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Report')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s %s have been updated.', __('Report\'s'), __('Vectors')));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could NOT be updated. Please, try again.', __('Report\'s'), __('Vectors')));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		$this->ReportsVector->searchFields = array('Vector.vector');
		
		$this->set('report_id', $report_id);
		$this->set('reports_vectors', $this->ReportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->ReportsVector->conditions(array('ReportsVector.report_id' => $report_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function assign_dnstracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if (!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
	}
	
	public function assign_hexilliontracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
	}
	
	public function assign_whoistracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if (!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
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
	 	$this->ReportsVector->recursive = -1;
		$this->ReportsVector->id = $id;
		if(!$this->ReportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if($this->ReportsVector->delete($id, false)) {
			$this->Session->setFlash(__('The %s was removed from this %s.', __('Vector'), __('Report')));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The %s was NOT removed from this %s.', __('Vector'), __('Report')));
		$this->redirect($this->referer());
	}
	
//
	public function admin_report($report_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$conditions = array(
			'ReportsVector.report_id' => $report_id, 
			'Vector.bad' => 0,
		);
		
		if(!is_null($active))
		{
			$conditions['ReportsVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('reports_vectors', $this->paginate('ReportsVector'));
	}
	
	public function admin_report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->ReportsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->listVectorIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Report.id !=' => $report_id,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('reports_vectors', $this->paginate());
	}
	
	public function admin_category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->CategoriesVector->Category->recursive = -1;
	 	$this->ReportsVector->Vector->CategoriesVector->Category->cacher = true;
		if(!$category = $this->ReportsVector->Vector->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->ReportsVector->Vector->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
	public function admin_import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->ImportsVector->Import->recursive = -1;
	 	$this->ReportsVector->Vector->ImportsVector->Import->cacher = true;
		if(!$import = $this->ReportsVector->Vector->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ReportsVector->Vector->ImportsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->ImportsVector->listVectorIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
	public function admin_upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->UploadsVector->Upload->recursive = -1;
	 	$this->ReportsVector->Vector->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->ReportsVector->Vector->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->ReportsVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
	public function admin_dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Vector->DumpsVector->Dump->recursive = -1;
	 	$this->ReportsVector->Vector->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->ReportsVector->Vector->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->ReportsVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->DumpsVector->listVectorIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.active' => 1,
			'ReportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}
	
	public function admin_unique($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ReportsVector->Report->recursive = -1;
	 	$this->ReportsVector->Report->cacher = true;
		if(!$report = $this->ReportsVector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->ReportsVector->Vector->cacher = true;
		if(!$vector_ids = $this->ReportsVector->Vector->listReportsVectorsUnique($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ReportsVector.vector_id' => $vector_ids,
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		
		$this->set('reports_vectors', $this->paginate());
	}
	
//
	public function admin_vector_type($vector_type_id = false) 
	{
	/**
	 * category method
	 * Shows only good vectors associated with this report
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportsVector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 0
		);
		
		// adjust the search fields
		$this->ReportsVector->searchFields = array('Report.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ReportsVector->recursive = 0;
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ReportsVector.*', 'Report.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ReportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
	}

	public function admin_add($report_id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid report'));
		}
		
		$this->set('report_id', $report_id);
		$this->set('report_name', $this->ReportsVector->Report->field('name'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The vectors have been added'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if($this->ReportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ReportsVector->modelError);
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
		if($this->request->data['ReportsVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.ReportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.ReportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		// Vector type detection
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				//only get the ones that are marked as a 1
				$ids = array();
				foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => $ids),
					'recursive' => -1,
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->redirect(array('admin' => false, 'controller' => 'vectors', 'action' => 'multiselect_vectortype'));
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				//only get the ones that are marked as a 1
				$ids = array();
				foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => $ids),
					'recursive' => -1,
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->redirect(array('admin' => false, 'controller' => 'vectors', 'action' => 'multiselect_multivectortype'));
		}
		// DNS Tracking
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_dnstracking'));
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking'));
		}
		// WHOIS Tracking
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_whoistracking'));
		}
		elseif($this->request->data['ReportsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['ReportsVector'];
			unset($this->request->data['ReportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ReportsVector->find('list', array(
					'fields' => array('ReportsVector.vector_id', 'ReportsVector.vector_id'),
					'conditions' => array('ReportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking'));
		}
		
		if($this->ReportsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Report.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Report.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportsVector']['vector_type_id'])?$this->request->data['ReportsVector']['vector_type_id']:0);
			if($this->ReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Report.'));
				return $this->redirect($this->ReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Report.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->ReportsVector->Vector->find('list', array(
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
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportsVector'])?$this->request->data['ReportsVector']:array());
			if($this->ReportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Report.'));
				return $this->redirect($this->ReportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Report.'));
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
		
		$conditions = array('ReportsVector.id' => $ids, 'Vector.bad' => 0);
		$this->ReportsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ReportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsVector->conditions($conditions, $this->passedArgs);
		$this->set('reports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_type($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report has been saved'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Report could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_multitypes($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Report\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Report could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		$this->ReportsVector->searchFields = array('Vector.vector');
		
		$this->set('report_id', $report_id);
		$this->set('reports_vectors', $this->ReportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->ReportsVector->conditions(array('ReportsVector.report_id' => $report_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->ReportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_dnstracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if (!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
	}
	
	public function admin_assign_hexilliontracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if(!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ReportsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
	}
	
	public function admin_assign_whoistracking($report_id = false)
	{
		$this->ReportsVector->Report->id = $report_id;
		if (!$this->ReportsVector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid Report'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReportsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'reports', 'action' => 'view', $report_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ReportsVector']['report_id'] = $report_id;
		}
		
		// get the object types
		$this->set('report_id', $report_id);
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
		$this->ReportsVector->id = $id;
		if(!$this->ReportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if($this->ReportsVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this Report.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Report.'));
		$this->redirect($this->referer());
	}
}

<?php
App::uses('AppController', 'Controller');
/**
 * UploadsVectors Controller
 *
 * @property UploadsVector $UploadsVector
 */
class UploadsVectorsController extends AppController 
{
//
	public function upload($upload_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->UploadsVector->Upload->recursive = -1;
	 	$this->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$is_owner = $this->UploadsVector->Upload->isOwnedBy($upload_id, AuthComponent::user('id'));
		$this->set('is_owner', $is_owner);
		
		$conditions = array(
			'UploadsVector.upload_id' => $upload_id, 
			'Vector.bad' => 0,
		);
		
		if(!$is_owner)
		{
			$conditions['UploadsVector.active'] = 1;
			$conditions['OR'] = array(
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
			);
		}
		if(!is_null($active))
		{
			$conditions['UploadsVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.filename', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = -1;
		$this->UploadsVector->unbindAllModels();
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'vectors', 'alias' => 'Vector', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Vector.id'),
			),
			array(
				'table' => 'uploads', 'alias' => 'Upload', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.upload_id = Upload.id'),
			),
			array(
				'table' => 'vector_types', 'alias' => 'VectorType', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_type_id = VectorType.id'),
			),
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
			array(
				'table' => 'categories', 'alias' => 'Category', 'type' => 'LEFT',
				'conditions' => array('Upload.category_id = Category.id'),
			),
			array(
				'table' => 'reports', 'alias' => 'Report', 'type' => 'LEFT',
				'conditions' => array('Upload.report_id = Report.id'),
			),
		);
		
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->UploadsVector->Upload->recursive = -1;
	 	$this->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$is_owner = false;
		
		$this->UploadsVector->cacher = true;
		if(!$vector_ids = $this->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->UploadsVector->Upload->isOwnedBy($upload_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		
		$conditions = array(
			'UploadsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Upload.id !=' => $upload_id,
		);
		
		if(!$is_owner)
		{
			$conditions['UploadsVector.active'] = 1;
			$conditions['OR'] = array(
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
			);
		}
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.filename', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = -1;
		$this->UploadsVector->unbindAllModels();
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'vectors', 'alias' => 'Vector', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Vector.id'),
			),
			array(
				'table' => 'uploads', 'alias' => 'Upload', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.upload_id = Upload.id'),
			),
			array(
				'table' => 'vector_types', 'alias' => 'VectorType', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_type_id = VectorType.id'),
			),
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
			array(
				'table' => 'categories', 'alias' => 'Category', 'type' => 'LEFT',
				'conditions' => array('Upload.category_id = Category.id'),
			),
			array(
				'table' => 'reports', 'alias' => 'Report', 'type' => 'LEFT',
				'conditions' => array('Upload.report_id = Report.id'),
			),
		);
		
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function category_related($category_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlCategoryToUploadsVectorsRelated($category_id),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
			'OR' => array(
				'Upload.public' => 2,
				'OR' => array(
					array('Upload.category_id !=' => 0, 'Category.public' => 2),
					array('Upload.upload_id !=' => 0, 'Upload.public' => 2),
					array('Upload.category_id' => 0, 'Upload.upload_id' => 0),
				),
				array(
					'Upload.public' => 1,
					'Upload.org_group_id' => AuthComponent::user('org_group_id'),
					'OR' => array(
						array('Upload.category_id !=' => 0, 'Category.public' => 1, 'Category.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.upload_id !=' => 0, 'Upload.public' => 1, 'Upload.org_group_id' => AuthComponent::user('org_group_id')),
						array('Upload.category_id' => 0, 'Upload.upload_id' => 0),
					),
				),
				array(
					'Upload.public' => 0,
					'Upload.user_id' => AuthComponent::user('id'),
				),
			),
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function import_related($import_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlImportToUploadsVectorsRelated($import_id),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
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
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function report_related($report_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlReportToUploadsVectorsRelated($report_id),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
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
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function dump_related($dump_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlDumpToUploadsVectorsRelated($dump_id),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
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
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
	public function vt_related($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UploadsVector.active' => 1, 
			'Vector.bad' => 0,
			' OR' => array(
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
			'OR' => $this->UploadsVector->sqlVirusTotalAllIds($vector_id),
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name');
		$this->UploadsVector->recursive = 0;
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function vector_type($vector_type_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UploadsVector.vector_type_id' => $vector_type_id, 
			'UploadsVector.active' => 1, 
			'Vector.bad' => 0,
			'Upload.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function add($upload_id = null) 
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		$this->set('upload_id', $upload_id);
		$this->set('upload_name', $this->UploadsVector->Upload->field('filename'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
//
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if($this->UploadsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadsVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function multiselect()
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
			$this->request->data['multiple'] = $ids;
			
			if(!in_array($this->request->data['UploadsVector']['multiselect_option'], array('active', 'inactive', 'type', 'multitype', 'delete')))
			{
				$this->request->data['multiple'] = $this->UploadsVector->find('list', array(
					'fields' => array('UploadsVector.vector_id', 'UploadsVector.vector_id'),
					'conditions' => array('UploadsVector.id' => $this->request->data['multiple']),
					'recursive' => -1,
				));
			}
		}
		if($this->request->data['UploadsVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// VT Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'hexilliontracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_hexilliontracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multihexilliontracking')
		{
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multihexilliontracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->UploadsVector->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Vectors'), __('Upload')));
			$this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Vectors'), __('Upload')));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['UploadsVector']['vector_type_id'])?$this->request->data['UploadsVector']['vector_type_id']:0);
			if($this->UploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Upload.'));
				return $this->redirect($this->UploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Upload.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->UploadsVector->Vector->find('list', array(
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
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['UploadsVector'])?$this->request->data['UploadsVector']:array());
			if($this->UploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this File.'));
				return $this->redirect($this->UploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this File.'));
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
		
		$conditions = array(
		'UploadsVector.id' => $ids, 
		'Vector.bad' => 0,
		);
		
		$this->UploadsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_multitypes($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Upload\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Upload could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		$this->UploadsVector->searchFields = array('Vector.vector');
		
		$this->set('upload_id', $upload_id);
		$this->set('uploads_vectors', $this->UploadsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->UploadsVector->conditions(array('UploadsVector.upload_id' => $upload_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function assign_dnstracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if (!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
	}
	
	public function assign_hexilliontracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
	}
	
	public function assign_whoistracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if (!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
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
		$this->UploadsVector->id = $id;
		if(!$this->UploadsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if($this->UploadsVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this File.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this File.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_upload($upload_id = false) 
	{
	/**
	 * upload method
	 * Shows only good vectors associated with this upload
	 * @return void
	 */
		// get the upload details
		$this->set('upload', $this->UploadsVector->Upload->read(null, $upload_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array('UploadsVector.upload_id' => $upload_id, 'Vector.bad' => 0);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
			array(
				'table' => 'categories', 'alias' => 'Category', 'type' => 'LEFT',
				'conditions' => array('Upload.category_id = Category.id'),
			),
			array(
				'table' => 'reports', 'alias' => 'Report', 'type' => 'LEFT',
				'conditions' => array('Upload.report_id = Report.id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function admin_category_related($category_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlCategoryToUploadsVectorsRelated($category_id, true),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}

//
	public function admin_upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$is_owner = false;
		
		$this->UploadsVector->cacher = true;
		if(!$vector_ids = $this->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id'), true))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$is_owner = $this->UploadsVector->Upload->isOwnedBy($upload_id, AuthComponent::user('id'));
		}
		$this->set('is_owner', $is_owner);
		
		$conditions = array(
			'UploadsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Upload.id !=' => $upload_id,
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.filename', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = -1;
		$this->UploadsVector->unbindAllModels();
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'vectors', 'alias' => 'Vector', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Vector.id'),
			),
			array(
				'table' => 'uploads', 'alias' => 'Upload', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.upload_id = Upload.id'),
			),
			array(
				'table' => 'vector_types', 'alias' => 'VectorType', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_type_id = VectorType.id'),
			),
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
			array(
				'table' => 'categories', 'alias' => 'Category', 'type' => 'LEFT',
				'conditions' => array('Upload.category_id = Category.id'),
			),
			array(
				'table' => 'reports', 'alias' => 'Report', 'type' => 'LEFT',
				'conditions' => array('Upload.report_id = Report.id'),
			),
		);
		
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function admin_import_related($import_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlImportToUploadsVectorsRelated($import_id, true),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function admin_report_related($report_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlReportToUploadsVectorsRelated($report_id, true),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function admin_dump_related($dump_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			$this->UploadsVector->Vector->sqlDumpToUploadsVectorsRelated($dump_id, true),
			'UploadsVector.active' => 1,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Upload.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
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
			'UploadsVector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 0
		);
		
		// adjust the search fields
		$this->UploadsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->UploadsVector->recursive = 0;
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['fields'] = array('UploadsVector.*', 'Upload.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Category.*', 'Report.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('UploadsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
	}
	
//
	public function admin_add($upload_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		$this->set('upload_id', $upload_id);
		$this->set('upload_name', $this->UploadsVector->Upload->field('filename'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if($this->UploadsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->UploadsVector->modelError);
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
			$this->request->data['multiple'] = $this->UploadsVector->find('list', array(
				'fields' => array('UploadsVector.vector_id', 'UploadsVector.vector_id'),
				'conditions' => array('UploadsVector.id' => $ids),
				'recursive' => -1,
			));
		}
		if($this->request->data['UploadsVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// VT Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['UploadsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['UploadsVector'];
			unset($this->request->data['UploadsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->UploadsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Upload.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Upload.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['UploadsVector']['vector_type_id'])?$this->request->data['UploadsVector']['vector_type_id']:0);
			if($this->UploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this Upload.'));
				return $this->redirect($this->UploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Upload.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['UploadsVector'])?$this->request->data['UploadsVector']:array());
			if($this->UploadsVector->multiselect($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated for this File.'));
				return $this->redirect($this->UploadsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this File.'));
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
		
		$conditions = array(
		'UploadsVector.id' => $ids, 
		'Vector.bad' => 0,
		);
		
		$this->UploadsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('UploadsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->UploadsVector->conditions($conditions, $this->passedArgs);
		$this->set('uploads_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_type($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_multitypes($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Upload\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Upload could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		$this->UploadsVector->searchFields = array('Vector.vector');
		
		$this->set('upload_id', $upload_id);
		$this->set('uploads_vectors', $this->UploadsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->UploadsVector->conditions(array('UploadsVector.upload_id' => $upload_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->UploadsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_dnstracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if (!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
	}
	
	public function admin_assign_hexilliontracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if(!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->UploadsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
	}
	
	public function admin_assign_whoistracking($upload_id = false)
	{
		$this->UploadsVector->Upload->id = $upload_id;
		if (!$this->UploadsVector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid Upload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UploadsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'uploads', 'action' => 'view', $upload_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['UploadsVector']['upload_id'] = $upload_id;
		}
		
		// get the object types
		$this->set('upload_id', $upload_id);
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
		$this->UploadsVector->id = $id;
		if(!$this->UploadsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if($this->UploadsVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this File.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this File.'));
		$this->redirect($this->referer());
	}
}

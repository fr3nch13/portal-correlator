<?php
App::uses('AppController', 'Controller');
/**
 * ImportsVectors Controller
 *
 * @property ImportsVector $ImportsVector
 */
class ImportsVectorsController extends AppController 
{
//
	public function import($import_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$conditions = array(
			'ImportsVector.import_id' => $import_id, 
			'Vector.bad' => 0,
		);
		if(!is_null($active))
		{
			$conditions['ImportsVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('imports_vectors', $this->paginate('ImportsVector'));
	}
	
	public function import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ImportsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->listVectorIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Import.id !=' => $import_id,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->CategoriesVector->Category->recursive = -1;
	 	$this->ImportsVector->Vector->CategoriesVector->Category->cacher = true;
		if(!$category = $this->ImportsVector->Vector->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->ImportsVector->Vector->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->Report->recursive = -1;
	 	$this->ImportsVector->Vector->Report->cacher = true;
		if(!$report = $this->ImportsVector->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->ImportsVector->Vector->ReportsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->ReportsVector->listVectorIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->UploadsVector->Upload->recursive = -1;
	 	$this->ImportsVector->Vector->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->ImportsVector->Vector->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->ImportsVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->DumpsVector->Dump->recursive = -1;
	 	$this->ImportsVector->Vector->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->ImportsVector->Vector->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->ImportsVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->DumpsVector->listVectorIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
//
	public function vt_related($vector_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ImportsVector.active' => 1, 
			'Vector.bad' => 0,
			'OR' => $this->ImportsVector->sqlVirusTotalAllIds($vector_id),
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
//
	public function vector_type($vector_type_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ImportsVector.vector_type_id' => $vector_type_id, 
			'ImportsVector.active' => 1, 
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
//
	public function unique($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ImportsVector->Vector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->listImportsVectorsUnique($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->set('imports_vectors', $this->paginate());
	}
	
//
	public function toggle($field = null, $id = null)
	{
		if ($this->ImportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ImportsVector->modelError);
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
			if(!in_array($this->request->data['ImportsVector']['multiselect_option'], array('active', 'inactive')))
			{
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => $this->request->data['multiple']),
				));
			}
		}
		
		if($this->request->data['ImportsVector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multivectortype');
		}
		// Vt Tracking
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'vttracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'hexilliontracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_hexilliontracking');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multihexilliontracking')
		{
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multihexilliontracking');
		}
		// WHOIS Tracking
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			$redirect = array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking');
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->ImportsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Category.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Category.'));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.ImportsVector');
			$multiselect_value = (isset($this->request->data['ImportsVector']['vector_type_id'])?$this->request->data['ImportsVector']['vector_type_id']:0);
			
			if($this->ImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.ImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Import.'));
				return $this->redirect($this->ImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
//
	public function multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.ImportsVector');
			$multiselect_value = (isset($this->request->data['ImportsVector'])?$this->request->data['ImportsVector']:array());
			
			if($this->ImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.ImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this File.'));
				return $this->redirect($this->ImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this File.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.ImportsVector');
		
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
			'ImportsVector.id' => $ids, 
			'Vector.bad' => 0,
		);
		
		$this->ImportsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_type($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function assign_vector_multitypes($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Import\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The Import could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		$this->ImportsVector->searchFields = array('Vector.vector');
		
		$this->set('import_id', $import_id);
		$this->set('imports_vectors', $this->ImportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->ImportsVector->conditions(array('ImportsVector.import_id' => $import_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function assign_dnstracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
	}
	
	public function assign_hexilliontracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if(!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ImportsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
	}
	
	public function assign_whoistracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
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
		$this->ImportsVector->id = $id;
		if (!$this->ImportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->ImportsVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this Import.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this Import.'));
		$this->redirect($this->referer());
	}
	
	public function admin_import($import_id = false, $active = null) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$conditions = array(
			'ImportsVector.import_id' => $import_id, 
			'Vector.bad' => 0,
		);
		if(!is_null($active))
		{
			$conditions['ImportsVector.active'] = ($active?1:0);
		}
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('imports_vectors', $this->paginate('ImportsVector'));
	}
	
	public function admin_import_related($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ImportsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->listVectorIds($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
			'Import.id !=' => $import_id,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function admin_category_related($category_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->CategoriesVector->Category->recursive = -1;
	 	$this->ImportsVector->Vector->CategoriesVector->Category->cacher = true;
		if(!$category = $this->ImportsVector->Vector->CategoriesVector->Category->read(null, $category_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Category')));
		}
		$this->set('category', $category);
		
		$this->ImportsVector->Vector->CategoriesVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->CategoriesVector->listVectorIds($category_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function admin_report_related($report_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->Report->recursive = -1;
	 	$this->ImportsVector->Vector->Report->cacher = true;
		if(!$report = $this->ImportsVector->Vector->Report->read(null, $report_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Report')));
		}
		$this->set('report', $report);
		
		$this->ImportsVector->Vector->ReportsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->ReportsVector->listVectorIds($report_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function admin_upload_related($upload_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->UploadsVector->Upload->recursive = -1;
	 	$this->ImportsVector->Vector->UploadsVector->Upload->cacher = true;
		if(!$upload = $this->ImportsVector->Vector->UploadsVector->Upload->read(null, $upload_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Upload')));
		}
		$this->set('upload', $upload);
		
		$this->ImportsVector->Vector->UploadsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->UploadsVector->listVectorIds($upload_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function admin_dump_related($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Vector->DumpsVector->Dump->recursive = -1;
	 	$this->ImportsVector->Vector->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->ImportsVector->Vector->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->ImportsVector->Vector->DumpsVector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->DumpsVector->listVectorIds($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Import.name', 'Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
	public function admin_unique($import_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->ImportsVector->Import->recursive = -1;
	 	$this->ImportsVector->Import->cacher = true;
		if(!$import = $this->ImportsVector->Import->read(null, $import_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Import')));
		}
		$this->set('import', $import);
		
		$this->ImportsVector->Vector->cacher = true;
		if(!$vector_ids = $this->ImportsVector->Vector->listImportsVectorsUnique($import_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'ImportsVector.active' => 1,
			'ImportsVector.vector_id' => $vector_ids,
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		
		$this->set('imports_vectors', $this->paginate());
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
			'ImportsVector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 0
		);
		
		// adjust the search fields
		$this->ImportsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->ImportsVector->recursive = 0;
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['fields'] = array('ImportsVector.*', 'Import.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('ImportsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
	}
	
//
	public function admin_add($import_id = null) 
	{
	/**
	 * add method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid import'));
		}
		
		$this->set('import_id', $import_id);
		$this->set('import_name', $this->ImportsVector->Import->field('filename'));
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->add($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been added'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The Vectors could not be added. Please, try again.'));
			}
		}
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle a user's boolean settings (like active)
	 */
		if ($this->ImportsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ImportsVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
//
	public function admin_multiselect()
	{
	/*
	 * batch manage multiple items
	 */
		if (!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		if($this->request->data['ImportsVector']['multiselect_option'] == 'type')
		{
			$this->Session->write('Multiselect.ImportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_types'));
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multitype')
		{
			$this->Session->write('Multiselect.ImportsVector', $this->request->data);
			$this->redirect(array('action' => 'multiselect_vector_multitypes'));
		}
		// Vector type detection
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'vectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				//only get the ones that are marked as a 1
				$ids = array();
				foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => $ids),
					'recursive' => -1,
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			$this->redirect(array('admin' => false, 'controller' => 'vectors', 'action' => 'multiselect_vectortype'));
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multivectortype')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				//only get the ones that are marked as a 1
				$ids = array();
				foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => $ids),
					'recursive' => -1,
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			$this->redirect(array('admin' => false, 'controller' => 'vectors', 'action' => 'multiselect_multivectortype'));
		}
		// DNS Tracking
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'dnstracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_dnstracking'));
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multidnstracking')
		{
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_multidnstracking'));
		}
		// WHOIS Tracking
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'whoistracking')
		{
			// change the request data
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_whoistracking'));
		}
		elseif($this->request->data['ImportsVector']['multiselect_option'] == 'multiwhoistracking')
		{
			$this->request->data['Vector'] = $this->request->data['ImportsVector'];
			unset($this->request->data['ImportsVector']);
			if(isset($this->request->data['multiple']))
			{
				$this->request->data['multiple'] = $this->ImportsVector->find('list', array(
					'fields' => array('ImportsVector.vector_id', 'ImportsVector.vector_id'),
					'conditions' => array('ImportsVector.id' => array_keys($this->request->data['multiple'])),
				));
			}
			$this->Session->write('Multiselect.Vector', $this->request->data);
			return $this->redirect(array('controller' => 'vectors', 'action' => 'multiselect_multiwhoistracking'));
		}
		
		if ($this->ImportsVector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated for this Import.'));
			$this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
		$this->redirect($this->referer());
	}
	
//
	public function admin_multiselect_vector_types()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.ImportsVector');
			$multiselect_value = (isset($this->request->data['ImportsVector']['vector_type_id'])?$this->request->data['ImportsVector']['vector_type_id']:0);
			
			if($this->ImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.ImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this Import.'));
				return $this->redirect($this->ImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this Import.'));
			}
		}
		
		// get the object types
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vector_multitypes()
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$sessionData = $this->Session->read('Multiselect.ImportsVector');
			$multiselect_value = (isset($this->request->data['ImportsVector'])?$this->request->data['ImportsVector']:array());
			
			if($this->ImportsVector->multiselect($sessionData, $multiselect_value)) 
			{
				$this->Session->delete('Multiselect.ImportsVector');
				$this->Session->setFlash(__('The Vectors were updated for this File.'));
				return $this->redirect($this->ImportsVector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated for this File.'));
			}
		}

		// get the list of vectors
		$sessionData = $this->Session->read('Multiselect.ImportsVector');
		
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
			'ImportsVector.id' => $ids, 
			'Vector.bad' => 0,
		);
		$this->ImportsVector->recursive = 0;
		$this->paginate['contain'] = array('Vector', 'VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ImportsVector.id' => 'desc');
		$this->paginate['conditions'] = $this->ImportsVector->conditions($conditions, $this->passedArgs);
		$this->set('imports_vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_type($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignVectorType($this->request->data)) 
			{
				$this->Session->setFlash(__('The File has been saved'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The File could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_vector_multitypes($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->saveAll($this->request->data)) 
			{
				$this->Session->setFlash(__('The Import\'s Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The Import could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		$this->ImportsVector->searchFields = array('Vector.vector');
		
		$this->set('import_id', $import_id);
		$this->set('imports_vectors', $this->ImportsVector->find('all', array(
			'recursive' => 0,
			'contain' => array('Vector', 'VectorType'),
			'conditions' => $this->ImportsVector->conditions(array('ImportsVector.import_id' => $import_id), $this->passedArgs),
		)));
		$this->set('vectorTypes', $this->ImportsVector->VectorType->typeFormList());
	}
	
	public function admin_assign_dnstracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignDnsTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
	}
	
	public function admin_assign_hexilliontracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if(!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ImportsVector->assignHexillionTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
	}
	
	public function admin_assign_whoistracking($import_id = false)
	{
		$this->ImportsVector->Import->id = $import_id;
		if (!$this->ImportsVector->Import->exists()) 
		{
			throw new NotFoundException(__('Invalid Import'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportsVector->assignWhoisTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect(array('controller' => 'imports', 'action' => 'view', $import_id));
			}
			else
			{
				$this->Session->setFlash(__('The  Vectors could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data['ImportsVector']['import_id'] = $import_id;
		}
		
		// get the object types
		$this->set('import_id', $import_id);
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
		$this->ImportsVector->id = $id;
		if (!$this->ImportsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->ImportsVector->delete($id, false)) {
			$this->Session->setFlash(__('The Vector was removed from this File.'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The Vector was NOT removed from this File.'));
		$this->redirect($this->referer());
	}
}

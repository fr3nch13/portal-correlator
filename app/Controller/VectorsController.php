<?php
App::uses('AppController', 'Controller');
/**
 * Vectors Controller
 *
 * @property Vector $Vector
 */
class VectorsController extends AppController 
{
	
	public function db_block_overview()
	{
		$stats = $this->Vector->dashboardOverviewStats();
		$this->set(compact('stats'));
	}
	
	public function db_block_vector_type()
	{
		$conditions = array('Vector.bad' => false);
		$conditions['Vector.vector_type_id >'] = 0;
		
		$vectors = $this->Vector->find('all', array(
			'contain' => array('VectorType'),
			'conditions' => $conditions,
		));
		
		$vectorTypes = $this->Vector->VectorType->find('all');
		
		$this->set(compact('vectors', 'vectorTypes'));
	}
	
	public function db_block_type()
	{
		$conditions = array('Vector.bad' => false);
		$conditions['Vector.type !='] = '';
		
		$stats = array(
			'total' => array('name' => __('Total with a %s Assigned', __('Type')), 'value' => $this->Vector->find('count', array('conditions' => $conditions))),
		);
		
		$types = $this->Vector->EX_listTypes();
		
		foreach($types as $type => $typeNice)
		{
			$count = $this->Vector->find('count', array(
				'conditions' => array(
					'Vector.type' => $type,
				),
			));
			
			$stats['type_'. $type] = array('name' => __('Type: %s', $typeNice), 'value' => (int)$count);
		}
		
		$stats = Hash::sort($stats, '{s}.value', 'desc');
		
		$this->set(compact('stats'));
	}
	
	public function dashboard()
	{
	}

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = [
			'Vector.bad' => false, 
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$page_subtitle = $this->get('page_subtitle');
		$page_description = $this->get('page_description');
		
		$this->set(compact(['page_subtitle', 'page_description']));
		
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		if(!isset($this->paginate['contain']))
			$this->paginate['contain'] = ['VectorSourceFirst', 'VectorSourceLast', 'VectorType'];
//			$this->paginate['contain'] = ['VectorType'];
			
		if(!isset($this->paginate['order']))
			$this->paginate['order'] = ['Vector.id' => 'desc'];
		if(!isset($this->paginate['cacher']))
			$this->paginate['cacher'] = true;
		if(!isset($this->paginate['recache']))
			$this->paginate['recache'] = [
				'path' => $this->here,
			];
		$this->set('vectors', $this->paginate());
	}
	
	public function hostnames($remote_local = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0, 
			'Vector.type' => 'hostname'
		);
		
		$lookup_type = '';
		if($remote_local)
		{
			$exclude = false;
			if($remote_local == 'local') 
			{
				$lookup_type = __('Local ');
			}
			elseif($remote_local == 'remote') 
			{
				$lookup_type = __('Remote ');
				$exclude = true;
			}
			
			// get the list of items that are considered local from the app config
			$conditions = $this->Vector->Hostname->mergeConditions($conditions, $this->Vector->Hostname->getInternalHostConditions($exclude));
		}
		
		$this->set('remote_local', $remote_local);
		$this->set('lookup_type', $lookup_type);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->paginate['cacher'] = true;
		$this->paginate['recache'] = array(
						'path' => $this->here,
					);
		$this->set('vectors', $this->paginate());
	}
	
	public function ipaddresses($remote_local = false) 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0, 
			'Vector.type' => 'ipaddress'
		);
		
		$lookup_type = '';
		if($remote_local)
		{
			$exclude = false;
			if($remote_local == 'local') 
			{
				$lookup_type = __('Local ');
			}
			elseif($remote_local == 'remote') 
			{
				$lookup_type = __('Remote ');
				$exclude = true;
			}
			
			// get the list of items that are considered local from the app config
			$conditions = $this->Vector->Ipaddress->mergeConditions($conditions, $this->Vector->Ipaddress->getInternalHostConditions($exclude));
		}
		
		$this->set('remote_local', $remote_local);
		$this->set('lookup_type', $lookup_type);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->paginate['cacher'] = true;
		$this->paginate['recache'] = array(
						'path' => $this->here,
					);
		$this->set('vectors', $this->paginate());
	}
	
	public function vector_type($vector_type_id = false) 
	{
	/**
	 * Shows only good vectors associated with this vector type
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$conditions = array(
			'Vector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname', 'Ipaddress', 'Geoip');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function type($type = false) 
	{
	/**
	 * Shows only good vectors associated with this vector type
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$conditions = array(
			'Vector.type' => $type, 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function combined_view($combined_view_id = false) 
	{
		$this->Vector->Category->CombinedView->recursive = -1;
	 	$this->Vector->Category->CombinedView->cacher = true;
		if(!$combinedView = $this->Vector->Category->CombinedView->read(null, $combined_view_id))
		{
			throw new NotFoundException(__('Unknown %s', __('View')));
		}
		$this->set('combinedView', $combinedView);
		
		$conditions = $this->Vector->combinedViewConditions($combined_view_id);
		
		$page_subtitle = __('Combined View');
		$this->set(compact(['page_subtitle', 'page_description']));
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this->index();
	}
	
	public function review()
	{
	/*
	 * Review vectors after an item is saved and vectors were found in it
	 */
		if($this->request->is('post') || $this->request->is('put'))
		{
			if($this->Vector->saveReviewed($this->request->data))
			{
				$redirect = array('action' => 'index');
				if($this->Vector->reviewRedirect)
				{
					$redirect = $this->Vector->reviewRedirect;
				}
				$this->Session->setFlash(__('The %s have been saved', __('Vectors')));
				
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Vectors')));
			}
		}
		else
		{
			$this->request->data = $this->Vector->getVectorsForReview();
			$this->set('reviewItems', $this->Vector->reviewItems);
		}
	}
	
	public function view($id = null) 
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$type = $this->Vector->field('type');
		
		if(in_array($type, $this->Vector->vtTypeList()))
		{
			if($type == 'hostname')
			{
				$this->Vector->getCounts['NslookupHostname'] = array(
					'all' => array(
						'recursive' => 0,
						'conditions' => array(
							'NslookupHostname.vector_hostname_id' => $id,
							'VectorHostname.bad' => 0,
						),
					),
				);
				$this->Vector->getCounts['DnsTransactionLog'] = array(
					'all' => array(
						'conditions' => array(
							'DnsTransactionLog.vector_id' => $id,
						),
					),
				);
			}
			elseif($type == 'ipaddress')
			{
				$this->Vector->getCounts['NslookupIpaddress'] = array(
					'all' => array(
						'recursive' => 0,
						'conditions' => array(
							'NslookupIpaddress.vector_ipaddress_id' => $id,
							'VectorIpaddress.bad' => 0,
						),
					),
				);
				$this->Vector->getCounts['DnsTransactionLog'] = array(
					'all' => array(
						'conditions' => array(
							'DnsTransactionLog.vector_id' => $id,
						),
					),
				);
			}
			
		}
		
		$this->Vector->recursive = 0;
		$this->set('vector', $this->Vector->read(null, $id));
		$this->set('whoiser_compile_states', $this->Vector->WhoiserTransaction->compile_states);
	}
	
	public function edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->recursive = 0;
		$this->Vector->contain(array('Tag', 'VectorDetail', 'Hostname', 'Ipaddress', 'HashSignature'));
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vector has been saved'));
				return $this->redirect(array('action' => 'view', $this->Vector->id));
			}
			else
			{
				$this->Session->setFlash(__('The Vector could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->Vector->read(null, $id);
		}
		
		// get the category types
		$vectorTypes = $this->Vector->VectorType->typeFormList();
		$this->set('vectorTypes', $vectorTypes);
	}
	
	public function tag($tag_id = null)  
	{
		if(!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Vector->Tag->read(null, $tag_id);
		if(!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0, 
		);
		$conditions[] = $this->Vector->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Vector');
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_dns() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				array(
					'Ipaddress.id > ' => 0,
					'Ipaddress.dns_auto_lookup > ' => 0,
				),
				array(
					'Hostname.id > ' => 0,
					'Hostname.dns_auto_lookup > ' => 0,
				),
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress', 'Hostname');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_dns_hostnames() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			array(
				'Hostname.id > ' => 0,
				'Hostname.dns_auto_lookup > ' => 0,
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname');
		$this->paginate['order'] = array('Hostname.dns_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_dns_ipaddresses() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			array(
				'Ipaddress.id > ' => 0,
				'Ipaddress.dns_auto_lookup > ' => 0,
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress');
		$this->paginate['order'] = array('Ipaddress.dns_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_dns_off($vector_id = null) 
	{
		$this->Vector->id = $vector_id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->contain(array('Hostname', 'Ipaddress'));
		if(!$vector = $this->Vector->read(null, $vector_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$return = false;
		
		if($vector['Ipaddress']['id'])
		{
			$this->Vector->Ipaddress->id = $vector['Ipaddress']['id'];
			$return = $this->Vector->Ipaddress->saveField('dns_auto_lookup', 0);
		}
		elseif($vector['Hostname']['id'])
		{
			$this->Vector->Hostname->id = $vector['Hostname']['id'];
			$return = $this->Vector->Hostname->saveField('dns_auto_lookup', 0);
		}
		
		if($return)
		{
			$this->Session->setFlash(__('The %s Tracking for the %s has been turned off.', __('DNS'), __('Vector')));
		}
		else
		{
			$this->Session->setFlash(__('Unable to turn off the %s Tracking for the %s.', __('DNS'), __('Vector')));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function auto_tracking_whois() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				array(
					'Ipaddress.id > ' => 0,
					'Ipaddress.whois_auto_lookup > ' => 0,
				),
				array(
					'Hostname.id > ' => 0,
					'Hostname.whois_auto_lookup > ' => 0,
				),
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress', 'Hostname');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_whois_hostnames() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			array(
				'Hostname.id > ' => 0,
				'Hostname.whois_auto_lookup > ' => 0,
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname');
		$this->paginate['order'] = array('Hostname.whois_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_whois_ipaddresses() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			array(
				'Ipaddress.id > ' => 0,
				'Ipaddress.whois_auto_lookup > ' => 0,
			),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress');
		$this->paginate['order'] = array('Ipaddress.whois_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_whois_off($vector_id = null) 
	{
		$this->Vector->id = $vector_id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->contain(array('Hostname', 'Ipaddress'));
		if(!$vector = $this->Vector->read(null, $vector_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$return = false;
		
		if($vector['Ipaddress']['id'])
		{
			$this->Vector->Ipaddress->id = $vector['Ipaddress']['id'];
			$return = $this->Vector->Ipaddress->saveField('whois_auto_lookup', 0);
		}
		elseif($vector['Hostname']['id'])
		{
			$this->Vector->Hostname->id = $vector['Hostname']['id'];
			$return = $this->Vector->Hostname->saveField('whois_auto_lookup', 0);
		}
		
		if($return)
		{
			$this->Session->setFlash(__('The %s Tracking for the %s has been turned off.', __('WHOIS'), __('Vector')));
		}
		else
		{
			$this->Session->setFlash(__('Unable to turn off the %s Tracking for the %s.', __('WHOIS'), __('Vector')));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function auto_tracking_vt() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0,
			'VectorDetail.vt_lookup > ' => 0,
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'VectorDetail');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_vt_hostnames() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0,
			'VectorDetail.vt_lookup > ' => 0,
			'Vector.type' => 'hostname',
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'VectorDetail');
		$this->paginate['order'] = array('Hostname.vt_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_vt_ipaddresses() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0,
			'VectorDetail.vt_lookup > ' => 0,
			'Vector.type' => 'ipaddress',
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'VectorDetail');
		$this->paginate['order'] = array('Ipaddress.vt_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_vt_hashes() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0,
			'VectorDetail.vt_lookup > ' => 0,
			'Vector.type' => array_keys($this->Vector->EX_listTypes('hash')),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Ipaddress', 'VectorDetail');
		$this->paginate['order'] = array('Ipaddress.vt_checked' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function auto_tracking_vt_off($vector_id = null) 
	{
		$this->Vector->id = $vector_id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->contain(array('VectorDetail'));
		if(!$vector = $this->Vector->read(null, $vector_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$return = false;
		
		if($vector['VectorDetail']['id'])
		{
			$this->Vector->VectorDetail->id = $vector['VectorDetail']['id'];
			$return = $this->Vector->VectorDetail->saveField('vt_lookup', 0);
		}
		
		if($return)
		{
			$this->Session->setFlash(__('The %s Tracking for the %s has been turned off.', __('VirusTotal'), __('Vector')));
		}
		else
		{
			$this->Session->setFlash(__('Unable to turn off the %s Tracking for the %s.', __('VirusTotal'), __('Vector')));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function vtview($id = null)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		// make sure it's a valid type to have VirusTotal details
		$type = $this->Vector->field('type');
		if(!in_array($type, $this->Vector->vtTypeList()))
		{
			$this->Session->setFlash(__('The %s isn\'t a valid type for %s Details', __('Vector'), __('VirusTotal')));
			return $this->redirect(array('action' => 'view', $this->Vector->id));
		}
		
		$this->Vector->getCounts = array();
		
		$this->Vector->getCounts['VtDetectedUrl'] = array(
			'all' => array('conditions' => array('VtDetectedUrl.vector_lookup_id' => $id), 'cacher' => true),
		);
		$this->Vector->getCounts['VtNtRecord'] = array(
			'all' => array('conditions' => array('VtNtRecord.vector_lookup_id' => $id), 'cacher' => true),
		);
		$this->Vector->getCounts['VtRelatedSample'] = array(
			'all' => array('conditions' => array('VtRelatedSample.vector_lookup_id' => $id), 'cacher' => true),
		);
		$this->Vector->getCounts['Vector'] = array(
			'related' => array('conditions' => array('OR' => $this->Vector->sqlVirusTotalAllIds($id)), 'recursive' => -1, 'cacher' => true),
		);
		
		$this->Vector->getCounts['CategoriesVector'] = array(
			'related' => array(
				'recursive' => 0,
				'conditions' => array(
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
					'OR' => $this->Vector->CategoriesVector->sqlVirusTotalAllIds($id),
				),
				'cacher' => '+1 day', 'recache' => true,
			),
		);
		
		$this->Vector->getCounts['ImportsVector'] = array(
			'related' => array(
				'recursive' => 0,
				'conditions' => array(
					'ImportsVector.active' => 1, 
					'Vector.bad' => 0,
					'OR' => $this->Vector->ImportsVector->sqlVirusTotalAllIds($id),
				),
				'cacher' => '+1 day', 'recache' => true, 
			),
		);
		
		$this->Vector->getCounts['ReportsVector'] = array(
			'related' => array(
				'recursive' => 0,
				'conditions' => array(
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
					'OR' => $this->Vector->ReportsVector->sqlVirusTotalAllIds($id),
				),
				'cacher' => '+1 day', 'recache' => true, 
			),
		);
		
		$this->Vector->getCounts['UploadsVector'] = array(
			'related' => array(
				'recursive' => 0,
				'conditions' => array(
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
					'OR' => $this->Vector->UploadsVector->sqlVirusTotalAllIds($id),
				),
				'cacher' => '+1 day', 'recache' => true, 
			),
		);
		
		$this->Vector->recursive = 0;
		
		$this->set('raw_files', $this->Vector->vtGetRawFiles($id));
		$this->set('vector', $this->Vector->read(null, $id));
	}
	
	public function vt_related($id = false) 
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$conditions = array('OR' => $this->Vector->sqlVirusTotalAllIds($id));
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function vt_raw_files($id = false) 
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Prg->commonProcess();
		
		$this->set('raw_files', $this->Vector->vtGetRawFiles($id));
	}
	
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
			$this->request->data['multiple'] = $this->Vector->find('list', array(
				'fields' => array('Vector.id', 'Vector.id'),
				'conditions' => array('Vector.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['Vector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['Vector']['multiselect_option'] == 'vectortype')
		{
			$redirect = array('action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multivectortype')
		{
			$redirect = array('action' => 'multiselect_multivectortype');
		}
		// VT Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'vttracking')
		{
			$redirect = array('action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'dnstracking')
		{
			$redirect = array('action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multidnstracking')
		{
			$redirect = array('action' => 'multiselect_multidnstracking');
		}
		// Whois Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'whoistracking')
		{
			$redirect = array('action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multiwhoistracking')
		{
			$redirect = array('action' => 'multiselect_multiwhoistracking');
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->bypassReferer = true;
			return $this->redirect($redirect);
		}
		
		if($this->Vector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated.'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated.'));
		return $this->redirect($this->referer());
	}
	
	public function multiselect_vectortype()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['type'])?$this->request->data['Vector']['type']:0);
			if($this->Vector->multiselect_vectortype($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
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
		$this->set('types', $this->Vector->EX_listTypes());
		$this->set('sessionData', $this->Session->read('Multiselect.Vector'));
	}
	
	public function multiselect_multivectortype()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector'])?$this->request->data['Vector']:array());
			if($this->Vector->multiselect_multivectortype($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated (1).'));
			}
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = -1;
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		
		$vectors = $this->paginate();
		
		foreach($vectors as $i => $vector)
		{
			$vectors[$i]['Vector']['discovered_type'] = $this->Vector->validateType($vector['Vector']['vector']);
		}
		
		$this->set('vectors', $vectors);
		
		// get the object types
		$this->set('types', $this->Vector->EX_listTypes());
	}
	
	public function multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['vector_type_id'])?$this->request->data['Vector']['vector_type_id']:0);
			
			if($this->Vector->multiselect($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
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
		$this->set('vectorTypes', $this->Vector->VectorType->typeFormList());
	}
	
	public function multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector'])?$this->request->data['Vector']:array());
			if($this->Vector->multiselect($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated (1).'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->Vector->VectorType->typeFormList());
	}
	
	public function multiselect_vttracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['vt_lookup'])?$this->request->data['Vector']['vt_lookup']:0);
			if($this->Vector->multiselect_vttracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The %s Tracking was updated for these %s %s.', __('VirusTotal'), $this->Vector->modelResults, __('Vectors')));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$flash = __('The %s Tracking was NOT updated for these %s.', __('VirusTotal'), __('Vectors'));
				if($this->Vector->modelError) $flash = $this->Vector->modelError;
				$this->Session->setFlash($flash);
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
	}
	
	public function multiselect_dnstracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['dns_auto_lookup'])?$this->request->data['Vector']['dns_auto_lookup']:0);
			if($this->Vector->multiselect_dnstracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The DNS Tracking was updated for these Vectors.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The DNS Tracking was NOT updated for these Vectors.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
					'Vector.type' => array('hostname', 'ipaddress'),
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		if(!$selected_vectors)
		{
			Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
			$this->Session->setFlash(__('None of the selected Vectors are valid hostnames/ip addresses.'));
			return $this->redirect(unserialize($sessionData['Vector']['multiselect_referer']));
		}
	}
	
	public function multiselect_multidnstracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->multiselect_multidnstracking($sessionData, $this->request->data))
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0, 
			'Vector.type' => array('hostname', 'ipaddress'),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['limit'] = count($sessionData['multiple']);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}
	
	public function multiselect_hexilliontracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['hexillion_auto_lookup'])?$this->request->data['Vector']['hexillion_auto_lookup']:0);
			if($this->Vector->multiselect_hexilliontracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Hexillion Tracking was updated for these Vectors.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Hexillion Tracking was NOT updated for these Vectors.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
					'Vector.type' => array('hostname', 'ipaddress'),
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		if(!$selected_vectors)
		{
			Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
			$this->Session->setFlash(__('None of the selected Vectors are valid hostnames/ip addresses.'));
			return $this->redirect(unserialize($sessionData['Vector']['multiselect_referer']));
		}
	}
	
	public function multiselect_multihexilliontracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->multiselect_multihexilliontracking($sessionData, $this->request->data))
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0, 
			'Vector.type' => array('hostname', 'ipaddress'),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['limit'] = count($sessionData['multiple']);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}
	
	public function multiselect_whoistracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['whois_auto_lookup'])?$this->request->data['Vector']['whois_auto_lookup']:0);
			if($this->Vector->multiselect_whoistracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The WHOIS Tracking was updated for these Vectors.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The WHOIS Tracking was NOT updated for these Vectors.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
					'Vector.type' => array('hostname', 'ipaddress'),
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		if(!$selected_vectors)
		{
			Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
			$this->Session->setFlash(__('None of the selected Vectors are valid hostnames/ip addresses.'));
			return $this->redirect(unserialize($sessionData['Vector']['multiselect_referer']));
		}
	}
	
	public function multiselect_multiwhoistracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->multiselect_multiwhoistracking($sessionData, $this->request->data))
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0, 
			'Vector.type' => array('hostname', 'ipaddress'),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}
	
	public function update_dns($id = false, $hash = false)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$flashMsg = [];
		
		if($this->Vector->updateDns($id)) 
		{
			$flashMsg[] = __('DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updateDnsDbapi($id)) 
		{
			$flashMsg[] = __('2nd DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updateVirusTotal($id)) 
		{
			$flashMsg[] = __('3rd DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updatePassiveTotal($id)) 
		{
			$flashMsg[] = __('4th DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		$this->Session->setFlash(implode("\n -- \n", $flashMsg));
		return $this->redirect($this->referer());
	}
	
	public function update_vt($id = false, $hash = false)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->vt_user_id = AuthComponent::user('id');
		
		if($result = $this->Vector->updateVirusTotalReport($id)) 
		{
			$this->Session->setFlash(__('The %s Reports have been updated for this %s', __('VirusTotal'), __('Vector')));
		}
		else
		{
			$this->Session->setFlash($this->Vector->modelError);
		}
		return $this->redirect($this->referer());
	}
	
	public function update_geoip($id = false, $hash = false)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		if($this->Vector->Geoip->lookupVectorId($id, false, true)) 
		{
			$this->Session->setFlash(__('Geoip updated for this %s', __('Vector')));
		}
		else
		{
			$this->Session->setFlash($this->Vector->Geoip->modelError);
		};
		return $this->redirect($this->referer());
	}
	
	public function update_whois($id = false, $hash = false)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$this->Vector->whois_user_id = AuthComponent::user('id');
		
		if($result = $this->Vector->updateWhois($id)) 
		{
			$this->Session->setFlash($result);
		}
		else
		{
			$this->Session->setFlash($this->Vector->modelError);
		}
		return $this->redirect($this->referer());
	}
	
	public function update_type($id = false, $type = false)
	{
	/** Rescans a vector either by id, or by type to see if we can update the vector types **/
		
		$params = array();
		if($id)
		{
			$params = array(
				'conditions' => array(
					'Vector.id' => $id,
				),
			);
			
			if(!$vector = $this->Vector->find('first', $params))
			{
				$this->Flash->warning(__('No valid %s found. - 1', __('Vectors')));
				return $this->redirect($this->referer());
			}
			
			if(!$type)
			{
				$type = $vector['Vector']['type'];
			}
			
			$vectors[$vector['Vector']['id']] = $vector['Vector']['vector'];
		}
		elseif($type)
		{
			$type = $this->Vector->cleanString($type);
			$type = trim($type);
			$type = strtolower($type);
			$params = array(
				'conditions' => array(
					'Vector.type' => $type,
				),
			);
			
			if(!$vectors = $this->Vector->find('list', $params))
			{
				$this->Flash->warning(__('No valid %s found. - 2', __('Vectors')));
				return $this->redirect($this->referer());
			}
		}
		else
		{
			$this->Flash->warning(__('No valid %s found. - 3', __('Vectors')));
			return $this->redirect($this->referer());
		}
		
		if(!$vectors)
		{
			$this->Flash->warning(__('No valid %s found. - 4', __('Vectors')));
			return $this->redirect($this->referer());
		}
		
		$fixedCnt = 0;
		foreach($vectors as $vectorId => $vector)
		{
			$thisType = $this->Vector->EX_discoverType($vector);
			if($thisType != $type)
			{
				if($vectorDetails = $this->Vector->fixType($vectorId, $thisType) and !$this->Vector->modelError)
				{
					$fixedCnt++;
				}
			}
		}
		
		if(!$fixedCnt)
		{
			$this->Flash->success(__('No %s need to be updated.', __('Vectors')));
		}
		else
		{
			$this->Flash->success(__('%s %s were updated.', $fixedCnt, __('Vectors')));
		}
		
		return $this->redirect($this->referer());
	}

	/*** Comparison Methods ***/
	
//
	public function compare_category_report($category_id = false, $report_id = false)
	{
	/*
	 * Compares a category and a report
	 */
		// make sure they exist
		$this->Vector->Category->id = $category_id;
		if(!$this->Vector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->Vector->Report->id = $report_id;
		if(!$this->Vector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid report'));
		}
		
		// make sure the user can view both category and report
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Category->isOwnedBy($category_id, AuthComponent::user('id')) or $this->Vector->Category->isPublic($category_id)
		)
		and
		(
			$this->Vector->Report->isOwnedBy($report_id, AuthComponent::user('id')) or $this->Vector->Report->isPublic($report_id)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the category or report.'));
		}
		
		$this->Vector->Category->recursive = 0;
		$this->Vector->Category->contain(array('User', 'CategoryType'));
		$category = $this->Vector->Category->read(null, $category_id);
		
		$this->Vector->Report->recursive = 0;
		$this->Vector->Report->contain(array('User', 'ReportType'));
		$report = $this->Vector->Report->read(null, $report_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareCategoryReport($category_id, $report_id));
		$this->set('category', $category);
		$this->set('report', $report);
	}
	
//
	public function compare_category_upload($category_id = false, $upload_id = false)
	{
	/*
	 * Compares a category and an upload
	 */
		// make sure they exist
		$this->Vector->Category->id = $category_id;
		if(!$this->Vector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->Vector->Upload->id = $upload_id;
		if(!$this->Vector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		// make sure the user can view both category and upload
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Category->isOwnedBy($category_id, AuthComponent::user('id')) or $this->Vector->Category->isPublic($category_id)
		)
		and
		(
			$this->Vector->Upload->isOwnedBy($upload_id, AuthComponent::user('id')) or $this->Vector->Upload->isPublic($upload_id)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the category or upload.'));
		}
		
		$this->Vector->Category->recursive = 0;
		$this->Vector->Category->contain(array('User', 'CategoryType'));
		$category = $this->Vector->Category->read(null, $category_id);
		
		$this->Vector->Upload->recursive = 0;
		$this->Vector->Upload->contain(array('User', 'UploadType'));
		$upload = $this->Vector->Upload->read(null, $upload_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareCategoryUpload($category_id, $upload_id));
		$this->set('category', $category);
		$this->set('upload', $upload);
	}
	
//
	public function compare_report_upload($report_id = false, $upload_id = false)
	{
	/*
	 * Compares a report and an upload
	 */
		// make sure they exist
		$this->Vector->Report->id = $report_id;
		if(!$this->Vector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid report'));
		}
		
		$this->Vector->Upload->id = $upload_id;
		if(!$this->Vector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		// make sure the user can view both report and upload
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Report->isOwnedBy($report_id, AuthComponent::user('id')) or $this->Vector->Report->isPublic($report_id)
		)
		and
		(
			$this->Vector->Upload->isOwnedBy($upload_id, AuthComponent::user('id')) or $this->Vector->Upload->isPublic($upload_id)
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the report or upload.'));
		}
		
		$this->Vector->Report->recursive = 0;
		$this->Vector->Report->contain(array('User', 'ReportType'));
		$report = $this->Vector->Report->read(null, $report_id);
		
		$this->Vector->Upload->recursive = 0;
		$this->Vector->Upload->contain(array('User', 'UploadType'));
		$upload = $this->Vector->Upload->read(null, $upload_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareReportUpload($report_id, $upload_id));
		$this->set('report', $report);
		$this->set('upload', $upload);
	}
	
//
	public function compare_category_dump($category_id = false, $dump_id = false)
	{
	/*
	 * Compares a category and an dump
	 */
		// make sure they exist
		$this->Vector->Category->id = $category_id;
		if(!$this->Vector->Category->exists()) 
		{
			throw new NotFoundException(__('Invalid category'));
		}
		
		$this->Vector->Dump->id = $dump_id;
		if(!$this->Vector->Dump->exists()) 
		{
			throw new NotFoundException(__('Invalid dump'));
		}
		
		// make sure the user can view both category and dump
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Category->isOwnedBy($category_id, AuthComponent::user('id')) or $this->Vector->Category->isPublic($category_id)
		)
		and
		(
			$this->Vector->Dump->isOwnedBy($dump_id, AuthComponent::user('id'))
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the category or dump.'));
		}
		
		$this->Vector->Category->recursive = 0;
		$this->Vector->Category->contain(array('User', 'CategoryType'));
		$category = $this->Vector->Category->read(null, $category_id);
		
		$this->Vector->Dump->recursive = 0;
		$this->Vector->Dump->contain(array('User'));
		$dump = $this->Vector->Dump->read(null, $dump_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareCategoryDump($category_id, $dump_id));
		$this->set('category', $category);
		$this->set('dump', $dump);
	}
	
//
	public function compare_report_dump($report_id = false, $dump_id = false)
	{
	/*
	 * Compares a report and an dump
	 */
		// make sure they exist
		$this->Vector->Report->id = $report_id;
		if(!$this->Vector->Report->exists()) 
		{
			throw new NotFoundException(__('Invalid report'));
		}
		
		$this->Vector->Dump->id = $dump_id;
		if(!$this->Vector->Dump->exists()) 
		{
			throw new NotFoundException(__('Invalid dump'));
		}
		
		// make sure the user can view both report and dump
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Report->isOwnedBy($report_id, AuthComponent::user('id')) or $this->Vector->Report->isPublic($report_id)
		)
		and
		(
			$this->Vector->Dump->isOwnedBy($dump_id, AuthComponent::user('id'))
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the report or dump.'));
		}
		
		$this->Vector->Report->recursive = 0;
		$this->Vector->Report->contain(array('User', 'ReportType'));
		$report = $this->Vector->Report->read(null, $report_id);
		
		$this->Vector->Dump->recursive = 0;
		$this->Vector->Dump->contain(array('User'));
		$dump = $this->Vector->Dump->read(null, $dump_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareReportDump($report_id, $dump_id));
		$this->set('report', $report);
		$this->set('dump', $dump);
	}
	
//
	public function compare_upload_dump($upload_id = false, $dump_id = false)
	{
	/*
	 * Compares an upload and a dump
	 */
		// make sure they exist
		$this->Vector->Upload->id = $upload_id;
		if(!$this->Vector->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid upload'));
		}
		
		$this->Vector->Dump->id = $dump_id;
		if(!$this->Vector->Dump->exists()) 
		{
			throw new NotFoundException(__('Invalid dump'));
		}
		
		// make sure the user can view both upload and dump
		$allowed = false;
		if(AuthComponent::user('admin'))
		{
			$allowed = true;
		}
		elseif(
		(
			$this->Vector->Upload->isOwnedBy($upload_id, AuthComponent::user('id')) or $this->Vector->Upload->isPublic($upload_id)
		)
		and
		(
			$this->Vector->Dump->isOwnedBy($dump_id, AuthComponent::user('id'))
		)) $allowed = true;
		if(!$allowed)
		{
			throw new NotFoundException(__('Unable to view either the upload or dump.'));
		}
		
		$this->Vector->Upload->recursive = 0;
		$this->Vector->Upload->contain(array('User', 'UploadType'));
		$upload = $this->Vector->Upload->read(null, $upload_id);
		
		$this->Vector->Dump->recursive = 0;
		$this->Vector->Dump->contain(array('User'));
		$dump = $this->Vector->Dump->read(null, $dump_id);
		
		// compare the strings
		$this->Vector->recursive = -1;
		$this->set('comparisons', $this->Vector->compareUploadDump($upload_id, $dump_id));
		$this->set('upload', $upload);
		$this->set('dump', $dump);
	}

//
	public function admin_index() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname', 'Ipaddress', 'Geoip');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}

//
	public function admin_good() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 0, 
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname', 'Ipaddress', 'Geoip');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}

//
	public function admin_bad() 
	{
	/**
	 * index method
	 * Shows only good vectors
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.bad' => 1, 
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorSourceFirst', 'VectorSourceLast', 'VectorType', 'Hostname', 'Ipaddress', 'Geoip');
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
	public function admin_type($type = false) 
	{
		$this->redirect(array('admin' => false, 'action' => 'type', $type));
	}
	
//
	public function admin_vector_type($vector_type_id = false) 
	{
	/**
	 * Shows all vectors associated with this vector type
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.vector_type_id' => $vector_type_id, 
		);
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
//
	public function admin_vector_type_good($vector_type_id = false) 
	{
	/**
	 * Shows only good vectors associated with this vector type
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 0,
		);
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}
	
//
	public function admin_vector_type_bad($vector_type_id = false) 
	{
	/**
	 * Shows only bad vectors associated with this vector type
	 * @return void
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.vector_type_id' => $vector_type_id, 
			'Vector.bad' => 1,
		);
		
		// adjust the search fields
		$this->Vector->searchFields = array('Vector.vector');
		
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs); 
		$this->set('vectors', $this->paginate());
	}

	public function admin_view($id = null) 
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		// get the counts
		$this->Vector->getCounts = array(
			'CategoriesVector' => array(
				'related' => array(
					'recursive' => 0,
					'contain' => array('Vector'),
					'conditions' => array(
						'CategoriesVector.vector_id' => $id,
						'Vector.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'ReportsVector' => array(
				'related' => array(
					'recursive' => 0,
					'contain' => array('Vector'),
					'conditions' => array(
						'ReportsVector.vector_id' => $id,
						'Vector.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'UploadsVector' => array(
				'related' => array(
					'recursive' => 0,
					'contain' => array('Vector'),
					'conditions' => array(
						'UploadsVector.vector_id' => $id,
						'Vector.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'ImportsVector' => array(
				'related' => array(
					'recursive' => 0,
					'contain' => array('Vector'),
					'conditions' => array(
						'ImportsVector.vector_id' => $id,
						'Vector.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'Tagged' => array(
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'Vector',
						'Tagged.foreign_key' => $id
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'NslookupHostname' => array(
				'all' => array(
					'recursive' => 0,
					'conditions' => array(
						'NslookupHostname.vector_hostname_id' => $id,
						'VectorHostname.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'NslookupIpaddress' => array(
				'all' => array(
					'recursive' => 0,
					'conditions' => array(
						'NslookupIpaddress.vector_ipaddress_id' => $id,
						'VectorIpaddress.bad' => 0,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'VectorSource' => array(
				'all' => array(
					'conditions' => array(
						'VectorSource.vector_id' => $id,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'DnsTransactionLog' => array(
				'all' => array(
					'conditions' => array(
						'DnsTransactionLog.vector_id' => $id,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'Whois' => array(
				'all' => array(
					'recursive' => 0,
					'conditions' => array(
						'Whois.vector_id' => $id,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
			'WhoisTransactionLog' => array(
				'all' => array(
					'conditions' => array(
						'WhoisTransactionLog.vector_id' => $id,
					),
					'cacher' => '+1 day', 'recache' => true,
				),
			),
		);
		
		$this->Vector->recursive = 1;
		$this->set('vector', $this->Vector->read(null, $id));
		$this->set('whoiser_compile_states', $this->Vector->WhoiserTransaction->compile_states);
	}
	
//
	public function admin_tag($tag_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a vectors tagged with a specific tag
	 */
		if(!$tag_id) 
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		
		$this->Vector->Tag->id = $tag_id;
		if(!$this->Vector->Tag->exists()) 
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		$tag = $this->Vector->Tag->read(null, $tag_id);
		
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->Vector->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Vector');
		
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());

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
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Vector has been saved'));
				return $this->redirect(array('action' => 'view', $this->Vector->id));
			}
			else
			{
				$this->Session->setFlash(__('The Vector could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->Vector->recursive = 0;
			$this->Vector->contain(array('Tag', 'Hostname', 'Ipaddress'));
			$this->request->data = $this->Vector->read(null, $id);
		}
		
		// get the category types
		$vectorTypes = $this->Vector->VectorType->typeFormList();
		$this->set('vectorTypes', $vectorTypes);
	}

//
	public function admin_toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
		if($this->Vector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The Vector has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->Vector->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
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
			$this->request->data['multiple'] = $this->Vector->find('list', array(
				'fields' => array('Vector.id', 'Vector.id'),
				'conditions' => array('Vector.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['Vector']['multiselect_option'] == 'type')
		{
			$redirect = array('action' => 'multiselect_vector_types');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multitype')
		{
			$redirect = array('action' => 'multiselect_vector_multitypes');
		}
		// Vector type detection
		elseif($this->request->data['Vector']['multiselect_option'] == 'vectortype')
		{
			$redirect = array('action' => 'multiselect_vectortype');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multivectortype')
		{
			$redirect = array('action' => 'multiselect_multivectortype');
		}
		// VT Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'vttracking')
		{
			$redirect = array('action' => 'multiselect_vttracking');
		}
		// DNS Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'dnstracking')
		{
			$redirect = array('action' => 'multiselect_dnstracking');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multidnstracking')
		{
			$redirect = array('action' => 'multiselect_multidnstracking');
		}
		// Whois Tracking
		elseif($this->request->data['Vector']['multiselect_option'] == 'whoistracking')
		{
			$redirect = array('action' => 'multiselect_whoistracking');
		}
		elseif($this->request->data['Vector']['multiselect_option'] == 'multiwhoistracking')
		{
			$redirect = array('action' => 'multiselect_multiwhoistracking');
		}
		if($redirect)
		{
			Cache::write('Multiselect_Vector_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->Vector->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The Vectors were updated.'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The Vectors were NOT updated.'));
		return $this->redirect($this->referer());
	}
	
//
	public function admin_multiselect_vectortype()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['type'])?$this->request->data['Vector']['type']:0);
			if($this->Vector->multiselect_vectortype($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors have been updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
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
		$this->set('types', $this->Vector->EX_listTypes());
		$this->set('sessionData', $this->Session->read('Multiselect.Vector'));
	}
	
	public function admin_multiselect_multivectortype()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector'])?$this->request->data['Vector']:array());
			if($this->Vector->multiselect_multivectortype($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated (1).'));
			}
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = -1;
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		
		$vectors = $this->paginate();
		
		foreach($vectors as $i => $vector)
		{
			$vectors[$i]['Vector']['discovered_type'] = $this->Vector->validateType($vector['Vector']['vector']);
		}
		
		$this->set('vectors', $vectors);
		
		// get the object types
		$this->set('types', $this->Vector->EX_listTypes());
	}
//
	public function admin_multiselect_vector_types()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['vector_type_id'])?$this->request->data['Vector']['vector_type_id']:0);
			
			if($this->Vector->multiselect($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
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
		$this->set('vectorTypes', $this->Vector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vector_multitypes()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector'])?$this->request->data['Vector']:array());
			if($this->Vector->multiselect($sessionData, $multiselect_value, AuthComponent::user('id'))) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated (1).'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0,
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
		
		// get the object types
		$this->set('vectorTypes', $this->Vector->VectorType->typeFormList());
	}
	
//
	public function admin_multiselect_vttracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['vt_lookup'])?$this->request->data['Vector']['vt_lookup']:0);
			if($this->Vector->multiselect_vttracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The %s Tracking was updated for these %s %s.', __('VirusTotal'), $this->Vector->modelResults, __('Vectors')));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$flash = __('The %s Tracking was NOT updated for these %s.', __('VirusTotal'), __('Vectors'));
				if($this->Vector->modelError) $flash = $this->Vector->modelError;
				$this->Session->setFlash($flash);
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
	}
	
//
	public function admin_multiselect_dnstracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['dns_auto_lookup'])?$this->request->data['Vector']['dns_auto_lookup']:0);
			if($this->Vector->multiselect_dnstracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The DNS Tracking was updated for these Vectors.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The DNS Tracking was NOT updated for these Vectors.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
					'Vector.type' => array('hostname', 'ipaddress'),
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		if(!$selected_vectors)
		{
			Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
			$this->Session->setFlash(__('None of the selected Vectors are valid hostnames/ip addresses.'));
			return $this->redirect(unserialize($sessionData['Vector']['multiselect_referer']));
		}
	}
	
//
	public function admin_multiselect_multidnstracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->multiselect_multidnstracking($sessionData, $this->request->data))
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0, 
			'Vector.type' => array('hostname', 'ipaddress'),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}
	
//
	public function admin_multiselect_whoistracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['Vector']['whois_auto_lookup'])?$this->request->data['Vector']['whois_auto_lookup']:0);
			if($this->Vector->multiselect_whoistracking($sessionData, $multiselect_value)) 
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The WHOIS Tracking was updated for these Vectors.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The WHOIS Tracking was NOT updated for these Vectors.'));
			}
		}
		
		$selected_vectors = array();
		if(isset($sessionData['multiple']))
		{
			$selected_vectors = $this->Vector->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Vector.id' => $sessionData['multiple'],
					'Vector.type' => array('hostname', 'ipaddress'),
				),
				'fields' => array('Vector.id', 'Vector.vector'),
				'sort' => array('Vector.vector' => 'asc'),
			));
		}
		
		$this->set('selected_vectors', $selected_vectors);
		
		if(!$selected_vectors)
		{
			Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
			$this->Session->setFlash(__('None of the selected Vectors are valid hostnames/ip addresses.'));
			return $this->redirect(unserialize($sessionData['Vector']['multiselect_referer']));
		}
	}
	
//
	public function admin_multiselect_multiwhoistracking()
	{
		$sessionData = Cache::read('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->Vector->multiselect_multiwhoistracking($sessionData, $this->request->data))
			{
				Cache::delete('Multiselect_Vector_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The Vectors were updated.'));
				return $this->redirect($this->Vector->multiselectReferer());
			}
			else
			{
				$this->Session->setFlash(__('The Vectors were NOT updated.'));
			}
		}

		$this->Prg->commonProcess();
		
		$conditions = array(
			'Vector.id' => $sessionData['multiple'], 
			'Vector.bad' => 0, 
			'Vector.type' => array('hostname', 'ipaddress'),
		);
		
		$this->Vector->recursive = 0;
		$this->paginate['contain'] = array('VectorType', 'Hostname', 'Ipaddress');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Vector.id' => 'desc');
		$this->paginate['conditions'] = $this->Vector->conditions($conditions, $this->passedArgs);
		$this->set('vectors', $this->paginate());
	}

//
	public function admin_update_dns($id = false, $hash = false)
	{
	/*
	 * updates the dns records for the vector
	 */
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		// return no matter what
		$referer = $this->referer();
		if($hash)
		{
			if(stripos($referer, '#'))
			{
				$referer = explode('#', $referer);
				$referer = $referer[0];
			}
			$referer .= '#'. $hash;
		}
		
		$flashMsg = array();
		
		if($this->Vector->updateDns($id)) 
		{
			$flashMsg[] = __('DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updateDnsDbapi($id)) 
		{
			$flashMsg[] = __('2nd DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updateVirusTotal($id)) 
		{
			$flashMsg[] = __('3rd DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		if($this->Vector->updatePassiveTotal($id)) 
		{
			$flashMsg[] = __('4th DNS updated for vector');
		}
		else
		{
			$flashMsg[] = $this->Vector->modelError;
		}
		
		$this->Session->setFlash(implode("\n -- \n", $flashMsg));
		return $this->redirect($referer);
	}
	
//
	public function admin_update_vt($id = false, $hash = false)
	{
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		// return no matter what
		$referer = $this->referer();
		if($hash)
		{
			if(stripos($referer, '#'))
			{
				$referer = explode('#', $referer);
				$referer = $referer[0];
			}
			$referer .= '#'. $hash;
		}
		
		$this->Vector->vt_user_id = AuthComponent::user('id');
		
		if($result = $this->Vector->updateVirusTotalReport($id)) 
		{
			$this->Session->setFlash(__('The %s Reports have been updated for this %s', __('VirusTotal'), __('Vector')));
		}
		else
		{
			$this->Session->setFlash($this->Vector->modelError);
		}
		return $this->redirect($referer);
	}
	
//
	public function admin_update_geoip($id = false, $hash = false)
	{
	/*
	 * updates the dns record for the vector
	 */
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		// return no matter what
		$referer = $this->referer();
		if($hash)
		{
			if(stripos($referer, '#'))
			{
				$referer = explode('#', $referer);
				$referer = $referer[0];
			}
			$referer .= '#'. $hash;
		}
		
		if($this->Vector->Geoip->lookupVectorId($id, false, true)) 
		{
			$this->Session->setFlash(__('Geoip updated for vector'));
		}
		else
		{
			$this->Session->setFlash($this->Vector->Geoip->modelError);
		};
		return $this->redirect($referer);
	}
	
//
	public function admin_update_whois($id = false, $hash = false)
	{
	/*
	 * updates the whois records for the vector
	 */
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		// return no matter what
		$referer = $this->referer();
		if($hash)
		{
			if(stripos($referer, '#'))
			{
				$referer = explode('#', $referer);
				$referer = $referer[0];
			}
			$referer .= '#'. $hash;
		}
		
		if($this->Vector->updateWhois($id)) 
		{
			$this->Session->setFlash(__('Whois updated for vector'));
		}
		else
		{
			$this->Session->setFlash($this->Vector->modelError);
		};
		return $this->redirect($referer);
	}

//
	public function admin_delete($id = null) 
	{
		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Vector->id = $id;
		if(!$this->Vector->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		if($this->Vector->delete()) {
			$this->Session->setFlash(__('Vector deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Vector was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function admin_stats()
	{
//		$stats = $this->Vector->stats();
		
		$db = $this->Vector->getDataSource();
		
		$subQuery = $db->buildStatement(
			array(
				'fields'	 => array('*'),
				'table'		 => $db->fullTableName($this->Vector->Whois),
				'conditions' => '`Vector`.`id` = `Whois`.`vector_id`',
				'alias'		 => '`Whois`',
			),
			$this->Vector->Whois
		);
		$subQuery = ' NOT EXISTS (' . $subQuery . ') ';
		$subQueryExpression = $db->expression($subQuery);
		
		

		
		$subQuery2 = $db->buildStatement(
			array(
				'fields'	 => array('`Vector`.`id`'),
				'table'		 => $db->fullTableName($this->Vector),
				'conditions' => array(
					'Vector.type' => 'hostname',
//					'Hostname.whois_auto_lookup' => 0,
					$subQueryExpression,
				),
				'alias'		 => '`Vector`',
			),
			$this->Vector
		);
		// get the categories themselves
		
		$subQuery2 = ' `Hostname`.`vector_id` IN (' . $subQuery2 . ') ';
		$subQuery2Expression = $db->expression($subQuery2);
		
		$count = $this->Vector->Hostname->find('count', array(
			'recursive' => 0,
			'fields' => array('Hostname.id'),
			'conditions' => array(
				$subQuery2Expression,
			),
		));
	}
}

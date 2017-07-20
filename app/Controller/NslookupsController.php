<?php
App::uses('AppController', 'Controller');
/**
 * Nslookups Controller
 *
 * @property Nslookup $Nslookup
 *
 * All urls to this controller are /dnsrecords/* indstead of /nslookups/*
 * This url change was made after this controller/model was created
 * see Config/routes.php for the route that dynamically changed the urls
 */
class NslookupsController extends AppController 
{
	public function db_block_overview()
	{
		$stats = $this->Nslookup->dashboardOverviewStats();
		$this->set(compact('stats'));
	}
	
	public function db_block_sources()
	{
		$sources = $this->Nslookup->find('all', array(
			'fields' => array('DISTINCT Nslookup.source', 'DISTINCT Nslookup.source'),
		));
		
		$stats = array();
		foreach($sources as $source)
		{
			$sourceNice = $source['Nslookup']['source'];
			if(!$sourceNice)
				$sourceNice = __('[None]');
			$stats['source_'. $source['Nslookup']['source']] = array(
				'name' => Inflector::humanize($sourceNice),
				'value' => $this->Nslookup->find('count', array('conditions' => array('Nslookup.source' => $source['Nslookup']['source']))),
			);
		}
		$this->set(compact('stats'));
	}
		
	
	public function dashboard()
	{
	}

	public function index($remote_local = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorHostname.bad' => 0,
			'VectorIpaddress.bad' => 0,
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
			$conditions = $this->Nslookup->mergeConditions($conditions, $this->Nslookup->VectorHostname->Hostname->getInternalHostConditions($exclude, 'VectorHostname', 'Vector'));
			$conditions = $this->Nslookup->mergeConditions($conditions, $this->Nslookup->VectorIpaddress->Ipaddress->getInternalHostConditions($exclude, 'VectorIpaddress', 'Vector'));
		}
		
		$this->set('remote_local', $remote_local);
		$this->set('lookup_type', $lookup_type);
		
		$this->Nslookup->recursive = 1;
		$this->paginate['contain'] = array('VectorHostname', 'VectorIpaddress');
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs); 
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = true;
		$this->set('nslookups', $this->paginate());
	}

	public function hostname($id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorHostname.bad' => 0,
			'Nslookup.vector_hostname_id' => $id,
		);
		
		$this->Nslookup->recursive = 0;
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs);
		$this->set('nslookups', $this->paginate());
	}

	public function ipaddress($id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorIpaddress.bad' => 0,
			'Nslookup.vector_ipaddress_id' => $id,
		);
		
		$this->Nslookup->recursive = 0;
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs);
		$this->set('nslookups', $this->paginate());
	}
	
	public function category($category_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorHostname.bad' => 0,
			'VectorIpaddress.bad' => 0,
		);
		
		$this->Nslookup->VectorHostname->cacher = true;
		$this->Nslookup->VectorIpaddress->cacher = true;
		
		if(!$vector_hostname_ids = $this->Nslookup->VectorHostname->sqlCategoryToNslookupRelated($category_id)
		and !$vector_ipaddress_ids = $this->Nslookup->VectorIpaddress->sqlCategoryToNslookupRelated($category_id))
		{
			$this->paginate['empty'] = true;
		}
		else
		{
			$conditions['OR'] = array(
				$this->Nslookup->VectorHostname->sqlCategoryToNslookupRelated($category_id),
				$this->Nslookup->VectorIpaddress->sqlCategoryToNslookupRelated($category_id),
			);
		}
		
		
		$this->paginate['recursive'] = $this->Nslookup->recursive = 0;
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs);
		
		$this->paginate['cacher'] = true;
		$this->paginate['cacher_path'] = $this->here;
		$this->paginate['recache'] = array(
			'model_name' => $this->modelClass,
			'model_use' => $this->modelClass,
		);
		
		$this->set('nslookups', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Nslookup->id = $id;
		if (!$this->Nslookup->exists()) 
		{
			throw new NotFoundException(__('Invalid nslookup'));
		}
		// get the counts
		$this->Nslookup->getCounts = array(
			'NslookupLog' => array(
				'all' => array(
					'conditions' => array(
						'NslookupLog.nslookup_id' => $id
					),
				),
			),
		);
		
		$this->Nslookup->recursive = 0;
		$this->set('nslookup', $this->Nslookup->read(null, $id));
	}
	
	public function admin_index() 
	{
		$this->Nslookup->recursive = 0;
		$this->set('nslookups', $this->paginate());
	}

	public function admin_hostname($id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorHostname.bad' => 0,
			'Nslookup.vector_hostname_id' => $id,
		);
		
		$this->Nslookup->recursive = 0;
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs);
		$this->set('nslookups', $this->paginate());
	}

	public function admin_ipaddress($id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'VectorIpaddress.bad' => 0,
			'Nslookup.vector_ipaddress_id' => $id,
		);
		
		$this->Nslookup->recursive = 0;
		$this->paginate['order'] = array('Nslookup.id' => 'desc');
		$this->paginate['conditions'] = $this->Nslookup->conditions($conditions, $this->passedArgs);
		$this->set('nslookups', $this->paginate());
	}
}

<?php
App::uses('AppController', 'Controller');
/**
 * VectorSourcesController Controller
 *
 * @property VectorSource $VectorSource
 */
class VectorSourcesController extends AppController 
{
//
	public function vector($vector_id = false) 
	{
	/**
	 * category method
	 * Shows only good vectors associated with this category
	 * @return void
	 */
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->VectorSource->searchFields = array(
			'VectorSource.source_type',
			'VectorSource.source',
			'VectorSource.sub_source',
		);
		
		$conditions = array(
			'VectorSource.vector_id' => $vector_id, 
			'Vector.bad' => 0,
		);
		
		$this->VectorSource->recursive = 0;
		$this->paginate['contain'] = array('Vector');
		$this->paginate['order'] = array('VectorSource.id' => 'desc');
		$this->paginate['conditions'] = $this->VectorSource->conditions($conditions, $this->passedArgs);
		$this->set('vector_sources', $this->paginate());
	}
//
	public function admin_vector($vector_id = false) 
	{
	/**
	 * category method
	 * Shows only good vectors associated with this category
	 * @return void
	 */
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->VectorSource->searchFields = array('VectorSource.source_type');
		
		$conditions = array(
			'VectorSource.vector_id' => $vector_id, 
			'Vector.bad' => 0,
		);
		
		$this->VectorSource->recursive = 0;
		$this->paginate['contain'] = array('Vector');
		$this->paginate['order'] = array('VectorSource.id' => 'desc');
		$this->paginate['conditions'] = $this->VectorSource->conditions($conditions, $this->passedArgs);
		$this->set('vector_sources', $this->paginate());
	}
}
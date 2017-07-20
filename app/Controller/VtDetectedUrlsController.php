<?php
App::uses('AppController', 'Controller');
/**
 * VtDetectedUrls Controller
 *
 * @property VtDetectedUrl $VtDetectedUrl
 */
class VtDetectedUrlsController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->VtDetectedUrl->recursive = 0;
		$this->paginate['order'] = array('VtDetectedUrl.id' => 'desc');
		$this->paginate['conditions'] = $this->VtDetectedUrl->conditions($conditions, $this->passedArgs); 
		$this->set('vt_detected_urls', $this->paginate());
	}
	
	public function vector($vector_lookup_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtDetectedUrl->VectorLookup->id = $vector_lookup_id;
		if (!$this->VtDetectedUrl->VectorLookup->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$conditions = array(
			'VtDetectedUrl.vector_lookup_id' => $vector_lookup_id,
		);
		
		$this->VtDetectedUrl->recursive = 0;
		$this->paginate['order'] = array('VtDetectedUrl.id' => 'desc');
		$this->paginate['conditions'] = $this->VtDetectedUrl->conditions($conditions, $this->passedArgs); 
		$this->set('vt_detected_urls', $this->paginate());
	}
}
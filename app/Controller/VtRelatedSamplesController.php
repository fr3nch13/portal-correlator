<?php
App::uses('AppController', 'Controller');
/**
 * VtRelatedSamples Controller
 *
 * @property VtRelatedSample $VtRelatedSample
 */
class VtRelatedSamplesController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->VtRelatedSample->recursive = 0;
		$this->paginate['order'] = array('VtRelatedSample.id' => 'desc');
		$this->paginate['conditions'] = $this->VtRelatedSample->conditions($conditions, $this->passedArgs); 
		$this->set('vt_related_samples', $this->paginate());
	}
	
	public function type($vt_related_sample_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtRelatedSample->id = $vt_related_sample_id;
		if (!$this->VtRelatedSample->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Related Sample')));
		}
		
		$type = $this->VtRelatedSample->field('type');
		$this->set('type', $type);
		
		$conditions = array(
			'VtRelatedSample.type' => $type,
		);
		
		$this->VtRelatedSample->recursive = 0;
		$this->paginate['order'] = array('VtRelatedSample.id' => 'desc');
		$this->paginate['conditions'] = $this->VtRelatedSample->conditions($conditions, $this->passedArgs); 
		$this->set('vt_related_samples', $this->paginate());
	}
	
	public function vector($vector_lookup_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtRelatedSample->VectorLookup->id = $vector_lookup_id;
		if (!$this->VtRelatedSample->VectorLookup->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$conditions = array(
			'VtRelatedSample.vector_lookup_id' => $vector_lookup_id,
		);
		
		$this->VtRelatedSample->recursive = 0;
		$this->paginate['order'] = array('VtRelatedSample.id' => 'desc');
		$this->paginate['conditions'] = $this->VtRelatedSample->conditions($conditions, $this->passedArgs); 
		$this->set('vt_related_samples', $this->paginate());
	}
}
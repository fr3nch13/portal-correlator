<?php
App::uses('AppController', 'Controller');
/**
 * VtNtRecords Controller
 *
 * @property VtNtRecord $VtNtRecord
 */
class VtNtRecordsController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->VtNtRecord->recursive = 0;
		$this->paginate['order'] = array('VtNtRecord.id' => 'desc');
		$this->paginate['conditions'] = $this->VtNtRecord->conditions($conditions, $this->passedArgs); 
		$this->set('vt_nt_records', $this->paginate());
	}
	
	public function protocol($vt_nt_record_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtNtRecord->id = $vt_nt_record_id;
		if (!$this->VtNtRecord->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Network Record')));
		}
		
		$protocol = $this->VtNtRecord->field('protocol');
		$this->set('protocol', $protocol);
		
		$conditions = array(
			'VtNtRecord.protocol' => $protocol,
		);
		
		$this->VtNtRecord->recursive = 0;
		$this->paginate['order'] = array('VtNtRecord.id' => 'desc');
		$this->paginate['conditions'] = $this->VtNtRecord->conditions($conditions, $this->passedArgs); 
		$this->set('vt_nt_records', $this->paginate());
	}
	
	public function src_port($vt_nt_record_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtNtRecord->id = $vt_nt_record_id;
		if (!$this->VtNtRecord->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Network Record')));
		}
		
		$src_port = $this->VtNtRecord->field('src_port');
		$this->set('src_port', $src_port);
		
		$conditions = array(
			'VtNtRecord.src_port' => $src_port,
		);
		
		$this->VtNtRecord->recursive = 0;
		$this->paginate['order'] = array('VtNtRecord.id' => 'desc');
		$this->paginate['conditions'] = $this->VtNtRecord->conditions($conditions, $this->passedArgs); 
		$this->set('vt_nt_records', $this->paginate());
	}
	
	public function dst_port($vt_nt_record_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtNtRecord->id = $vt_nt_record_id;
		if (!$this->VtNtRecord->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Network Record')));
		}
		
		$dst_port = $this->VtNtRecord->field('dst_port');
		$this->set('dst_port', $dst_port);
		
		$conditions = array(
			'VtNtRecord.dst_port' => $dst_port,
		);
		
		$this->VtNtRecord->recursive = 0;
		$this->paginate['order'] = array('VtNtRecord.id' => 'desc');
		$this->paginate['conditions'] = $this->VtNtRecord->conditions($conditions, $this->passedArgs); 
		$this->set('vt_nt_records', $this->paginate());
	}
	
	public function vector($vector_lookup_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->VtNtRecord->VectorLookup->id = $vector_lookup_id;
		if (!$this->VtNtRecord->VectorLookup->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Vector')));
		}
		
		$conditions = array(
			'VtNtRecord.vector_lookup_id' => $vector_lookup_id,
		);
		
		$this->VtNtRecord->recursive = 0;
		$this->paginate['order'] = array('VtNtRecord.id' => 'desc');
		$this->paginate['conditions'] = $this->VtNtRecord->conditions($conditions, $this->passedArgs); 
		$this->set('vt_nt_records', $this->paginate());
	}
}
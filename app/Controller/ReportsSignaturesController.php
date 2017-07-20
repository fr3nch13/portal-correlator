<?php
App::uses('AppController', 'Controller');
/**
 * ReportsSignatures Controller
 *
 * @property ReportsSignature $ReportsSignature
 */
class ReportsSignaturesController extends AppController 
{	
//
	public function temp_report($temp_report_id = false) 
	{
		// get the report details
		$this->set('temp_report', $this->ReportsSignature->TempReport->read(null, $temp_report_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportsSignature.temp_report_id' => $temp_report_id, 
		);
		
		// adjust the search fields
		$this->ReportsSignature->searchFields = array(
			'Signature.name', 
			'Signature.signature', 
			'SignatureSource.name',
		);
		$this->ReportsSignature->recursive = 0;
		$this->paginate['order'] = array('ReportsSignature.id' => 'desc');
		
		$this->paginate['conditions'] = $this->ReportsSignature->conditions($conditions, $this->passedArgs);
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['ReportsSignature.active'] = true;
			$conditions['Signature.active'] = true;
			
			$yara_signatures = $this->ReportsSignature->Signature->YaraSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->ReportsSignature->sqlSignatureIds($conditions),
				),
			));
			$snort_signatures = $this->ReportsSignature->Signature->SnortSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->ReportsSignature->sqlSignatureIds($conditions),
				),
			));
			$reports_signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$reports_signatures = $this->paginate();
		}
		$this->set('reports_signatures', $reports_signatures);
	}
	
//
	public function report($report_id = false) 
	{
		// get the report details
		$this->ReportsSignature->Report->recursive = -1;
		$this->set('report', $this->ReportsSignature->Report->read(null, $report_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportsSignature.report_id' => $report_id, 
		);
		
		// adjust the search fields
		$this->ReportsSignature->searchFields = array(
			'Signature.name', 
			'Signature.signature', 
			'SignatureSource.name',
		);
		$this->ReportsSignature->recursive = 0;
		$this->paginate['contain'] = array('Report', 'Signature', 'SignatureSource');
		$this->paginate['order'] = array('ReportsSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsSignature->conditions($conditions, $this->passedArgs);
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['ReportsSignature.active'] = true;
			$conditions['Signature.active'] = true;
			
			$yara_signatures = $this->ReportsSignature->Signature->YaraSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->ReportsSignature->sqlSignatureIds($conditions),
				),
			));
			$snort_signatures = $this->ReportsSignature->Signature->SnortSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->ReportsSignature->sqlSignatureIds($conditions),
				),
			));
			$reports_signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$reports_signatures = $this->paginate();
		}
		$this->set('reports_signatures', $reports_signatures);
	}
	
//
	public function delete($id = null) 
	{
		$this->ReportsSignature->id = $id;
		if (!$this->ReportsSignature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature')));
		}
		if ($this->ReportsSignature->delete($id, false)) 
		{
			$this->Session->setFlash(__('The %s was deleted', __('Signature')));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The %s was NOT deleted.', __('Signature')));
		$this->redirect($this->referer());
	}
}
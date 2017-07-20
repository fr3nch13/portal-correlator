<?php
App::uses('AppController', 'Controller');
/**
 * ReportsEditors Controller
 *
 * @property ReportsEditor $ReportsEditor
 */
class ReportsEditorsController extends AppController 
{
//
	public function report($report_id = false) 
	{
	/**
	 * report method
	 * Shows only editors associated with this report
	 * @return void
	 */
		// get the report details
		$this->set('report', $this->ReportsEditor->Report->read(null, $report_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->ReportsEditor->searchFields = array('User.name');
		
		$conditions = array(
			'ReportsEditor.report_id' => $report_id, 
		);
		
		$this->ReportsEditor->recursive = 0;
		$this->paginate['order'] = array('ReportsEditor.id' => 'desc');
		$this->paginate['conditions'] = $this->ReportsEditor->conditions($conditions, $this->passedArgs);
		$this->set('reports_editors', $this->paginate());
	}
}

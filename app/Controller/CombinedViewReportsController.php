<?php
App::uses('AppController', 'Controller');

class CombinedViewReportsController extends AppController 
{
	public function remove($report_id = false, $combined_view_id = null) 
	{
		if($xref = $this->CombinedViewReport->find('first', [
			'conditions' => [
				'CombinedViewReport.combined_view_id' => $combined_view_id,
				'CombinedViewReport.report_id' => $report_id,
			]
		]))
		{
			$this->CombinedViewReport->id = $xref['CombinedViewReport']['id'];
		}
		
		$this->bypassReferer = true;
		if($this->CombinedViewReport->delete($this->CombinedViewReport->id)) 
		{
			$this->Flash->success(__('Removed %s from %s.', __('Report'), __('View')));
			$this->redirect(['controller' => 'combined_views', 'action' => 'view', $combined_view_id, 'tab' => 'reports']);
		}
		$this->Flash->error(__('The %s was NOT removed from the %s.', __('Report'), __('View')));
		$this->redirect(['controller' => 'combined_views', 'action' => 'view', $combined_view_id, 'tab' => 'reports']);
	}
}

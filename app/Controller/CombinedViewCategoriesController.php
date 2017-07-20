<?php
App::uses('AppController', 'Controller');

class CombinedViewCategoriesController extends AppController 
{
	public function remove($category_id = false, $combined_view_id = null) 
	{
		if($xref = $this->CombinedViewCategory->find('first', [
			'conditions' => [
				'CombinedViewCategory.combined_view_id' => $combined_view_id,
				'CombinedViewCategory.category_id' => $category_id,
			]
		]))
		{
			$this->CombinedViewCategory->id = $xref['CombinedViewCategory']['id'];
		}
		
		$this->bypassReferer = true;
		if($this->CombinedViewCategory->delete($this->CombinedViewCategory->id)) 
		{
			$this->Flash->success(__('Removed %s from %s.', __('Category'), __('View')));
			$this->redirect(['controller' => 'combined_views', 'action' => 'view', $combined_view_id, 'tab' => 'categories']);
		}
		$this->Flash->error(__('The %s was NOT removed from the %s.', __('Category'), __('View')));
		$this->redirect(['controller' => 'combined_views', 'action' => 'view', $combined_view_id, 'tab' => 'categories']);
	}
}

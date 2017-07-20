<?php
App::uses('AppController', 'Controller');
/**
 * CategoriesEditors Controller
 *
 * @property CategoriesEditor $CategoriesEditor
 */
class CategoriesEditorsController extends AppController 
{
//
	public function category($category_id = false) 
	{
	/**
	 * category method
	 * Shows only editors associated with this category
	 * @return void
	 */
		// get the category details
		$this->set('category', $this->CategoriesEditor->Category->read(null, $category_id));
		
		$this->Prg->commonProcess();
		
		// adjust the search fields
		$this->CategoriesEditor->searchFields = array('User.name');
		
		$conditions = array(
			'CategoriesEditor.category_id' => $category_id, 
		);
		
		$this->CategoriesEditor->recursive = 0;
		$this->paginate['order'] = array('CategoriesEditor.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesEditor->conditions($conditions, $this->passedArgs);
		$this->set('categories_editors', $this->paginate());
	}
}

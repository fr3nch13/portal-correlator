<?php
App::uses('AppController', 'Controller');
/**
 * CategoriesSignatures Controller
 *
 * @property CategoriesSignature $CategoriesSignature
 */
class CategoriesSignaturesController extends AppController 
{
	
//
	public function temp_category($temp_category_id = false) 
	{
		// get the category details
		$this->set('temp_category', $this->CategoriesSignature->TempCategory->read(null, $temp_category_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoriesSignature.temp_category_id' => $temp_category_id, 
		);
		
		// adjust the search fields
		$this->CategoriesSignature->searchFields = array(
			'Signature.name', 
			'Signature.signature', 
			'SignatureSource.name'
		);
		
		$this->CategoriesSignature->recursive = 0;
		$this->paginate['order'] = array('CategoriesSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesSignature->conditions($conditions, $this->passedArgs);
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['CategoriesSignature.active'] = true;
			$conditions['CategoriesSignature.category_id'] = $category_id;
			$conditions['Signature.active'] = true;
			
			$yara_signatures = $this->CategoriesSignature->Signature->YaraSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->CategoriesSignature->sqlSignatureIds($conditions),
				),
			));
			$snort_signatures = $this->CategoriesSignature->Signature->SnortSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->CategoriesSignature->sqlSignatureIds($conditions),
				),
			));
			$categories_signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$categories_signatures = $this->paginate();
		}
		$this->set('categories_signatures', $categories_signatures);
	}
	
//
	public function category($category_id = false) 
	{
		// get the category details
		$this->CategoriesSignature->Category->recursive = -1;
		$this->set('category', $this->CategoriesSignature->Category->read(null, $category_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'CategoriesSignature.category_id' => $category_id, 
		);
		
		// adjust the search fields
		$this->CategoriesSignature->searchFields = array(
			'Signature.name', 
			'Signature.signature', 
			'SignatureSource.name'
		);
		
		$this->CategoriesSignature->recursive = 0;
		$this->paginate['contain'] = array('Category', 'Signature', 'SignatureSource');
		$this->paginate['order'] = array('CategoriesSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->CategoriesSignature->conditions($conditions, $this->passedArgs);
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['CategoriesSignature.active'] = true;
			$conditions['CategoriesSignature.category_id'] = $category_id;
			$conditions['Signature.active'] = true;
			
			$yara_signatures = $this->CategoriesSignature->Signature->YaraSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->CategoriesSignature->sqlSignatureIds($conditions),
				),
			));
			$snort_signatures = $this->CategoriesSignature->Signature->SnortSignature->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					$this->CategoriesSignature->sqlSignatureIds($conditions),
				),
			));
			$categories_signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$categories_signatures = $this->paginate();
		}
		$this->set('categories_signatures', $categories_signatures);
	}
	
//
	public function delete($id = null) 
	{
		$this->CategoriesSignature->id = $id;
		if (!$this->CategoriesSignature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature')));
		}
		if ($this->CategoriesSignature->delete($id, false)) 
		{
			$this->Session->setFlash(__('The %s was deleted', __('Signature')));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The %s was NOT deleted.', __('Signature')));
		$this->redirect($this->referer());
	}
}
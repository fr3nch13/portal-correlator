<?php
App::uses('AppController', 'Controller');
/**
 * Signature Sources Controller
 *
 * @property SignatureSource $SignatureSource
 */
class SignatureSourcesController extends AppController 
{
//
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		// include just the user information
		$this->SignatureSource->recursive = 0;
		
		$this->paginate['order'] = array('SignatureSource.id' => 'desc');
		$this->paginate['conditions'] = $this->SignatureSource->conditions($conditions, $this->passedArgs); 
		$this->set('signature_sources', $this->paginate());
	}

//
	public function auto_complete($field = false)
	{
		Configure::write('debug', 0);
		
		$terms = array();
		if(isset($this->request->query[$field]))
		{
			$terms = $this->SignatureSource->find('list', array(
				'conditions' => array(
					'SignatureSource.'. $field.' LIKE' => $this->request->query[$field].'%'
				),
				'fields' => array('SignatureSource.id', 'SignatureSource.'. $field),
				'limit' => 20,
				'recursive'=> -1,
			));
		}
		
		$this->set('terms', $terms);
		$this->layout = 'ajax';	
	}
//
	public function view($id = null) 
	{
		$this->SignatureSource->id = $id;
		if (!$this->SignatureSource->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature Source')));
		}
		
		// get the counts
		$this->SignatureSource->getCounts = array(
			'Signature' => array(
				'all' => array(
					'conditions' => array(
						'Signature.signature_source_id' => $id,
					),
				),
			),
		);
		
		// get the user information
		$this->SignatureSource->recursive = 0;
		$this->set('signature_source', $this->SignatureSource->read(null, $id));
	}
	
	public function admin_edit($id = null) 
	{
		$this->SignatureSource->id = $id;
		if (!$this->SignatureSource->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature Source')));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->SignatureSource->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been updated', __('Signature Source')));
				return $this->redirect(array('action' => 'view', $this->SignatureSource->id, 'admin' => false));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be updated. Please, try again.', __('Signature Source')));
			}
		}
		else
		{
			$this->SignatureSource->recursive = 0;
			$this->request->data = $this->SignatureSource->read(null, $id);
		}
	}
//
	public function admin_delete($id = null) 
	{
		$this->SignatureSource->id = $id;
		if (!$this->SignatureSource->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature Source')));
		}
		
		$referer = $this->referer();
		if(preg_match('/sources\/view/i', $referer))
		{
			$referer = array('action' => 'index');
		}
		
		if ($this->SignatureSource->delete($id, false)) 
		{
			$this->Session->setFlash(__('The %s was deleted', __('Signature Source')));
			$this->redirect($referer);
		}
		$this->Session->setFlash(__('The %s was NOT deleted.', __('Signature Source')));
		$this->redirect($referer);
	}
}
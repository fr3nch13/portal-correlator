<?php
App::uses('AppController', 'Controller');
/**
 * Signatures Controller
 *
 * @property Signature $Signature
 */
class SignaturesController extends AppController 
{

//
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Signature->recursive = 0;
		$this->paginate['contain'] = array('SignatureSource');
		$this->paginate['order'] = array('Signature.id' => 'desc');
		$this->paginate['conditions'] = $this->Signature->conditions($conditions, $this->passedArgs); 
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['Signature.active'] = true;
			
			$this->Signature->YaraSignature->recursive = 2;
			$this->Signature->YaraSignature->contain(
				'Signature', 
				'Signature.SignatureSource', 
				'Signature.SignatureAddedUser',
				'Signature.Tag', 
				'YaraSignatureMeta',
				'YaraSignatureString',
				'YaraSignatureCondition',
				'SignatureSource',
				'Tag'
			);
			$yara_signatures = $this->Signature->YaraSignature->find('all', array(
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			
			$this->Signature->SnortSignature->recursive = 2;
			$this->Signature->SnortSignature->contain(
				'Signature', 
				'Signature.SignatureSource', 
				'Signature.SignatureAddedUser',
				'Signature.Tag',
				'SnortSignatureIndex',
				'SignatureSource',
				'Tag'
			);
			$snort_signatures = $this->Signature->SnortSignature->find('all', array(
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			$signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$signatures = $this->paginate();
		}
		$this->set('signatures', $signatures);
	}

//
	public function signature_source($signature_source_id = false) 
	{
		if (!$signature_source_id) 
		{
			throw new NotFoundException(__('Invalid %s Type', __('Signature Source')));
		}
		
		// get the signature source details
		$this->set('signature_source', $this->Signature->SignatureSource->read(null, $signature_source_id));
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Signature.signature_source_id' => $signature_source_id,
		);
		
		$this->Signature->recursive = -1;
		$this->paginate['order'] = array('Signature.id' => 'desc');
		$this->paginate['conditions'] = $this->Signature->conditions($conditions, $this->passedArgs); 
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['Signature.active'] = true;
			
			$yara_signatures = $this->Signature->YaraSignature->find('all', array(
				'recursive' => 1,
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			$snort_signatures = $this->Signature->SnortSignature->find('all', array(
				'recursive' => 1,
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			$signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$signatures = $this->paginate();
		}
		
		$this->set('signatures', $signatures);
	}
	
	public function tag($tag_id = null)  
	{
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		
		$tag = $this->Signature->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid Tag'));
		}
		
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions[] = $this->Signature->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Signature');
		
		$this->Signature->recursive = 0;
		$this->paginate['contain'] = array('SignatureSource');
		$this->paginate['order'] = array('Signature.id' => 'desc');
		$this->paginate['conditions'] = $this->Signature->conditions($conditions, $this->passedArgs); 
		
		// exporting
		if(isset($this->request->params['ext']))
		{
			$conditions = $this->paginate['conditions'];
			
			$conditions['Signature.active'] = true;
			
			$this->Signature->YaraSignature->recursive = 2;
			$this->Signature->YaraSignature->contain(
				'Signature', 
				'Signature.SignatureSource', 
				'Signature.SignatureAddedUser',
				'Signature.Tag', 
				'YaraSignatureMeta',
				'YaraSignatureString',
				'YaraSignatureCondition',
				'SignatureSource',
				'Tag'
			);
			$yara_signatures = $this->Signature->YaraSignature->find('all', array(
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			
			$this->Signature->SnortSignature->recursive = 2;
			$this->Signature->SnortSignature->contain(
				'Signature', 
				'Signature.SignatureSource', 
				'Signature.SignatureAddedUser',
				'Signature.Tag',
				'SnortSignatureIndex',
				'SignatureSource',
				'Tag'
			);
			$snort_signatures = $this->Signature->SnortSignature->find('all', array(
				'conditions' => $conditions,
				'order' => $this->paginate['order'],
			));
			$signatures = ($yara_signatures + $snort_signatures);
		}
		else
		{
			$signatures = $this->paginate();
		}
		$this->set('signatures', $signatures);
	}
	
	public function view($id = null) 
	{
		$this->Signature->id = $id;
		if (!$this->Signature->exists()) 
		{
			return $this->redirect(array('action' => 'index'));
		}
		
		// get the counts
		$this->Signature->getCounts = array(
			'ReportsSignature' => array(
				'all' => array(
					'recursive' => 0,
					'conditions' => array(
						'ReportsSignature.signature_id' => $id,
						'ReportsSignature.active' => 1,
						'OR' => array(
							'Report.public' => 2,
							array(
								'Report.public' => 1,
								'Report.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Report.public' => 0,
								'Report.user_id' => AuthComponent::user('id'),
							),
						),
					),
				),
			),
			'CategoriesSignature' => array(
				'all' => array(
					'recursive' => 0,
					'conditions' => array(
						'CategoriesSignature.signature_id' => $id,
						'OR' => array(
							'Category.public' => 2,
							array(
								'Category.public' => 1,
								'Category.org_group_id' => AuthComponent::user('org_group_id'),
							),
							array(
								'Category.public' => 0,
								'Category.user_id' => AuthComponent::user('id'),
							),
						),
					),
				),
			),
			'Tagged' => array(
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'Signature',
						'Tagged.foreign_key' => $id
					),
				),
			),
		);
		
		$this->Signature->recursive = 0;
		$this->set('signature', $this->Signature->read(null, $id));
	}

	public function add() 
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->request->data['Signature']['added_user_id'] = AuthComponent::user('id');
			$this->request->data['Signature']['org_group_id'] = AuthComponent::user('org_group_id');
			if ($ids = $this->Signature->add($this->request->data)) 
			{
				$_signature = __('Signatures');
				$_have = __('have');
				if(count($ids) == 1)
				{
					$_signature = __('Signature');
					$_have = __('has');
				}
				$this->Session->setFlash(__('The %s %s been added', $_signature, $_have));
				$redirect = array('action' => 'index');
				if($this->Signature->modelRedirect) $redirect = $this->Signature->modelRedirect;
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be added. Reason: %s', __('Signature'), $this->Signature->modelError));
			}
		}
		else
		{
			$this->request->data['Signature'] = $this->request->params['named'];
		}
	}
	
	public function admin_edit($id = null) 
	{
		$this->Signature->id = $id;
		if (!$this->Signature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature')));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Signature->update($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been updated', __('Signature')));
				return $this->redirect(array('action' => 'view', $this->Signature->id, 'admin' => false));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be updated. Please, try again.', __('Signature')));
			}
		}
		else
		{
			$this->Signature->recursive = 1;
			$this->Signature->contain(array(
				'SignatureSource',
				'Tag'
			));
			$this->request->data = $this->Signature->read(null, $id);
			$this->request->data['Signature']['signatures'] = $this->request->data['Signature']['signature'];
		}
	}
//
	public function admin_delete($id = null) 
	{
		$this->Signature->id = $id;
		if (!$this->Signature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Signature')));
		}
		if ($this->Signature->delete($id, true)) 
		{
			$this->Session->setFlash(__('The %s was deleted', __('Signature')));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The %s was NOT deleted.', __('Signature')));
		$this->redirect($this->referer());
	}
}
<?php
App::uses('AppController', 'Controller');
/**
 * SnortSignatures Controller
 *
 * @property SnortSignature $Signature
 */
class SnortSignaturesController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->SnortSignature->recursive = 0;
		$this->paginate['contain'] = array('Signature', 'SignatureSource');
		$this->paginate['order'] = array('SnortSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->SnortSignature->conditions($conditions, $this->passedArgs); 
		
		if(isset($this->request->params['ext']))
		{
			$this->SnortSignature->recursive = 2;
			$this->SnortSignature->contain(
				'Signature', 
				'Signature.SignatureSource',
				'Signature.SignatureAddedUser',
				'Signature.Tag', 
				'SnortSignatureIndex',
				'SignatureSource',
				'Tag'
			);
			
			$snort_signatures = $this->SnortSignature->find('all', array(
				'conditions' => $this->paginate['conditions'],
				'order' => $this->paginate['order'],
			));
		}
		else
		{
			$snort_signatures = $this->paginate();
		}
		
		$this->set('snort_signatures', $snort_signatures);
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->SnortSignature->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions[] = $this->SnortSignature->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'SnortSignature');
		
		$this->SnortSignature->recursive = 0;
		$this->paginate['contain'] = array('Signature', 'SignatureSource');
		$this->paginate['order'] = array('SnortSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->SnortSignature->conditions($conditions, $this->passedArgs); 
		
		if(isset($this->request->params['ext']))
		{
			$this->SnortSignature->recursive = 2;
			$this->SnortSignature->contain(
				'Signature', 
				'Signature.SignatureSource',
				'Signature.SignatureAddedUser',
				'Signature.Tag', 
				'SnortSignatureIndex',
				'SignatureSource',
				'Tag'
			);
			
			$snort_signatures = $this->SnortSignature->find('all', array(
				'conditions' => $this->paginate['conditions'],
				'order' => $this->paginate['order'],
			));
		}
		else
		{
			$snort_signatures = $this->paginate();
		}
		
		$this->set('snort_signatures', $snort_signatures);
	}
	
	public function compiled($id = false)
	{
		//Configure::write('debug', 0);
		
		$this->SnortSignature->id = $id;
		if (!$this->SnortSignature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Snort'), __('Signature')));
		}
		
		$this->SnortSignature->recursive = 2;
		$this->SnortSignature->contain(
			'Signature', 
			'Signature.SignatureSource', 
			'Signature.Category', 
			'Signature.Report', 
			'Signature.SignatureAddedUser',
			'Signature.Tag',
			'SnortSignatureIndex',
			'Tag'
		);
		$snort_signature = $this->SnortSignature->read(null, $id);
		$this->set('snort_signature', $snort_signature);
		$this->set('compiled', $this->SnortSignature->compileSignature($snort_signature));
		
	}
}
<?php
App::uses('AppController', 'Controller');
/**
 * YaraSignatures Controller
 *
 * @property YaraSignature $YaraSignature
 */
class YaraSignaturesController extends AppController 
{
//
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->YaraSignature->recursive = 0;
		$this->paginate['contain'] = array('Signature', 'SignatureSource');
		$this->paginate['order'] = array('YaraSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->YaraSignature->conditions($conditions, $this->passedArgs); 
		
		if(isset($this->request->params['ext']))
		{
			$this->YaraSignature->recursive = 2;
			$this->YaraSignature->contain(
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
			
			$yara_signatures = $this->YaraSignature->find('all', array(
				'conditions' => $this->paginate['conditions'],
				'order' => $this->paginate['order'],
			));
		}
		else
		{
			$yara_signatures = $this->paginate();
		}
		
		$this->set('yara_signatures', $yara_signatures);
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->YaraSignature->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->YaraSignature->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'YaraSignature');
		
		$this->YaraSignature->recursive = 0;
		$this->paginate['contain'] = array('Signature', 'SignatureSource');
		$this->paginate['order'] = array('YaraSignature.id' => 'desc');
		$this->paginate['conditions'] = $this->YaraSignature->conditions($conditions, $this->passedArgs);

		if(isset($this->request->params['ext']))
		{
			$this->YaraSignature->recursive = 2;
			$this->YaraSignature->contain(
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
			 
			$yara_signatures = $this->YaraSignature->find('all', array(
				'conditions' => $this->paginate['conditions'],
				'order' => $this->paginate['order'],
			));
		}
		else
		{
			$yara_signatures = $this->paginate();
		}
		
		$this->set('yara_signatures', $yara_signatures);
	}
	
	public function compiled($id = false)
	{
		//Configure::write('debug', 0);
		
		$this->YaraSignature->id = $id;
		if (!$this->YaraSignature->exists()) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Yara'), __('Signature')));
		}
		
		$this->YaraSignature->recursive = 2;
		$this->YaraSignature->contain(
			'Signature', 
			'Signature.SignatureSource', 
			'Signature.Category', 
			'Signature.Report', 
			'Signature.SignatureAddedUser',
			'Signature.Tag',
			'YaraSignatureMeta',
			'YaraSignatureString',
			'YaraSignatureCondition',
			'Tag'
		);
		$yara_signature = $this->YaraSignature->read(null, $id);
		$this->set('yara_signature', $yara_signature);
		$this->set('compiled', $this->YaraSignature->compileSignature($yara_signature));
		
	}
}
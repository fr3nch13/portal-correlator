<?php
App::uses('AppController', 'Controller');
/**
 * Dumps Controller
 *
 * @property Dump $Dump
 */
class DumpsController extends AppController 
{

	public function isAuthorized($user = array())
	{
	/*
	 * Checks permissions for a user when trying to access a dump
	 */
		// All registered users can add dumps
		if ($this->action === 'add')
		{
			return true;
		}
		
		// The only the owner of a dump can view and delete it
		if (in_array($this->action, array('view', 'download', 'delete'))) 
		{
			$dumpId = $this->request->params['pass'][0];
			$this->Dump->id = $dumpId;
			if (!$this->Dump->exists()) 
			{
				throw new NotFoundException(__('Invalid dump'));
			}
			if ($this->Dump->isOwnedBy($dumpId, AuthComponent::user('id')))
			{
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
	public function index() 
	{
	/**
	 * index method
	 *
	 * Displays all public Categories
	 */
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Dump.user_id' => AuthComponent::user('id')
		);
		
		$this->paginate['order'] = array('Dump.id' => 'desc');
		$this->paginate['conditions'] = $this->Dump->conditions($conditions, $this->passedArgs); 
		$this->set('dumps', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Dump->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Dump.user_id' => AuthComponent::user('id')
		);
		$conditions[] = $this->Dump->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Dump');
		
		$this->paginate['order'] = array('Dump.id' => 'desc');
		$this->paginate['conditions'] = $this->Dump->conditions($conditions, $this->passedArgs); 
		$this->set('dumps', $this->paginate());
	}
	
//
	public function view($id = null) 
	{
		// get the user information
		$this->Dump->recursive = 0;
		if(!$dump = $this->Dump->read(null, $id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
	}
	
	public function download($id = false, $modelClass = false, $filename = false) 
	{
		if(!$params = $this->Dump->downloadParams($id))
		{
			throw new NotFoundException($this->Dump->modelError);
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}
	
	public function add() 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post'))
		{
			$this->Dump->create();
			$this->request->data['Dump']['user_id'] = AuthComponent::user('id');
			
			if ($this->Dump->saveAssociated($this->request->data))
			{
				$redirect = array('action' => 'view', $this->Dump->id);
				
				if($this->Dump->sessionVectorId)
				{
					$redirect = $this->Dump->sessionRedirect;
				}
				
				$this->Session->setFlash(__('The dump has been saved'));
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The dump could not be saved. Please, try again.'));
			}
		}
	}
	
	public function delete($id = null) 
	{
/**
 * delete method
 *
 * @param string $id
 * @return void
 */
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		if ($this->Dump->delete($id)) 
		{
			$this->Session->setFlash(__('Dump deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Dump was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

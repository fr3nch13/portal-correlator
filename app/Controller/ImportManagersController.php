<?php
App::uses('AppController', 'Controller');
/**
 * ImportManagers Controller
 *
 * @property ImportManager $ImportManager
 */
class ImportManagersController extends AppController 
{

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
//			'ImportManager.org_group_id' => AuthComponent::user('org_group_id'),
		);
		
		// include just the user information
		$this->paginate['order'] = array('ImportManager.name' => 'desc');
		$this->paginate['conditions'] = $this->ImportManager->conditions($conditions, $this->passedArgs); 
		$this->set('importManagers', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->ImportManager->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$conditions[] = $this->ImportManager->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'ImportManager');
		
		// include just the user information
		$this->paginate['order'] = array('ImportManager.name' => 'desc');
		$this->paginate['conditions'] = $this->ImportManager->conditions($conditions, $this->passedArgs); 
		$this->set('importManagers', $this->paginate());
	}
	
	public function menu()
	{
		if ($this->request->is('requested')) 
		{
			$this->render = false;
			$import_managers = $this->ImportManager->find('list', array(
				'fields' => array('ImportManager.id', 'ImportManager.name'),
			));
			
			// format for the menu_items
			$items = array(
				array(
					'title' => __('All'),
					'url' => array('controller' => 'imports', 'action' => 'index', 'admin' => false, 'plugin' => false)
				)
			);
			
			foreach($import_managers as $import_manager_id => $import_manager_name)
			{
				$items[] = array(
					'title' => $import_manager_name,
					'url' => array('controller' => 'import_managers', 'action' => 'view', $import_manager_id, 'admin' => false, 'plugin' => false)
				);
			}
			return $items;
		}
		else
		{
			return $this->redirect(array('action' => 'index'));
		}
	}
	
	public function view($id = null) 
	{
		$this->ImportManager->id = $id;
		if (!$this->ImportManager->exists()) 
		{
			throw new NotFoundException(__('Invalid Import Manager'));
		}
		
		// get the counts
		$this->ImportManager->getCounts = array(
			'Import' => array(
				'all' => array(
					'conditions' => array(
						'Import.import_manager_id' => $id, 
					)
				),
			),
			'Tagged' => array( 
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'ImportManager',
						'Tagged.foreign_key' => $id
					),
				),
			),
		);
		
		$this->ImportManager->recursive = 0;
		$this->ImportManager->contain(array('Tag'));
		$this->set('import_manager', $this->ImportManager->read(null, $id));
	}

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$this->ImportManager->recursive = 0;
		$this->paginate['contain'] = array('OrgGroup');
		
		// include just the user information
		$this->paginate['order'] = array('ImportManager.name' => 'desc');
		$this->paginate['conditions'] = $this->ImportManager->conditions($conditions, $this->passedArgs); 
		$this->set('importManagers', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
		$this->ImportManager->id = $id;
		if (!$this->ImportManager->exists()) 
		{
			throw new NotFoundException(__('Invalid Import Manager'));
		}
		
		// get the counts
		$this->ImportManager->getCounts = array(
			'Import' => array(
				'all' => array(
					'conditions' => array(
						'Import.import_manager_id' => $id, 
					)
				),
			),
			'ImportManagerLog' => array(
				'all' => array(
					'conditions' => array(
						'ImportManagerLog.import_manager_id' => $id, 
					)
				),
			),
			'Tagged' => array(
				'all' => array(
					'conditions' => array(
						'Tagged.model' => 'ImportManager',
						'Tagged.foreign_key' => $id
					),
				),
			),
		);
		
		$this->ImportManager->recursive = 0;
		$this->ImportManager->contain(array('Tag'));
		$this->set('import_manager', $this->ImportManager->read(null, $id));
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post')) 
		{
			$this->ImportManager->create();
			if ($this->ImportManager->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Import Manager has been saved'));
				return $this->redirect(array('action' => 'view', $this->ImportManager->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Import Manager could not be saved. Please, try again.'));
			}
		}
		
		$vectorTypes = $this->ImportManager->Import->Vector->VectorType->typeFormList();
		$this->set('vectorTypes', $vectorTypes);
	}
	
	public function admin_edit($id = null) 
	{
		$this->ImportManager->id = $id;
		if (!$this->ImportManager->exists()) 
		{
			throw new NotFoundException(__('Invalid Import Manager'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ImportManager->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The Import Manager has been saved'));
				return $this->redirect(array('action' => 'view', $this->ImportManager->id));
			} 
			else 
			{
				$this->Session->setFlash(__('The Import Manager could not be saved. Please, try again.'));
			}
		} 
		else 
		{
			$this->ImportManager->recursive = 0;
			$this->ImportManager->contain(array('Tag'));
			$this->request->data = $this->ImportManager->read(null, $id);
		}
		
		$vectorTypes = $this->ImportManager->Import->Vector->VectorType->typeFormList();
		$this->set('vectorTypes', $vectorTypes);
	}
	
	public function admin_toggle($field = null, $id = null)
	{
		if ($this->ImportManager->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The File Group has been updated.'));
		}
		else
		{
			$this->Session->setFlash($this->ImportManager->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_delete($id = null) 
	{
		$this->ImportManager->id = $id;
		if (!$this->ImportManager->exists()) 
		{
			throw new NotFoundException(__('Invalid Import Manager'));
		}
		if ($this->ImportManager->delete($id, false)) 
		{
			$this->Session->setFlash(__('Import Manager deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Import Manager was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

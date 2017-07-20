<?php
App::uses('AppController', 'Controller');
/**
 * OrgGroups Controller
 *
 * @property OrgGroups $OrgGroup
 */
class OrgGroupsController extends AppController 
{
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->paginate['order'] = array('OrgGroup.name' => 'asc');
		$this->paginate['conditions'] = $this->OrgGroup->conditions($conditions, $this->passedArgs); 
		$this->set('org_groups', $this->paginate());
	}

	public function admin_view($id = 0)
	{
		$this->OrgGroup->recursive = 0;
		$this->set('org_group', $this->OrgGroup->read(null, $id));
	}
	
	public function admin_add() 
	{
	}
	
	public function admin_edit($id = null) 
	{
	}
}

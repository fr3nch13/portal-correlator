<?php
App::uses('AppController', 'Controller');
/**
 * DumpsVectors Controller
 *
 * @property DumpsVector $DumpsVector
 */
class DumpsVectorsController extends AppController 
{
	public function dump($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->DumpsVector->Dump->recursive = -1;
	 	$this->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		// adjust the search fields
		$this->DumpsVector->searchFields = array('Vector.vector');
		
		$conditions = array('DumpsVector.dump_id' => $dump_id, 'Vector.bad' => 0);
		
		if(!$this->DumpsVector->Dump->isOwnedBy($dump_id, AuthComponent::user('id')))
		{
			$conditions['DumpsVector.active'] = 1;
		}
		
		// adjust the search fields
		$this->DumpsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->DumpsVector->recursive = 0;
		$this->paginate['order'] = array('DumpsVector.id' => 'desc');
		$this->paginate['fields'] = array('DumpsVector.*', 'Dump.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->DumpsVector->conditions($conditions, $this->passedArgs);
		$this->set('dumps_vectors', $this->paginate());
	}
	
//
	public function unique($dump_id = false) 
	{
		$this->Prg->commonProcess();
		
		$this->DumpsVector->Dump->recursive = -1;
	 	$this->DumpsVector->Dump->cacher = true;
		if(!$dump = $this->DumpsVector->Dump->read(null, $dump_id))
		{
			throw new NotFoundException(__('Unknown %s', __('Dump')));
		}
		$this->set('dump', $dump);
		
		$this->DumpsVector->Vector->cacher = true;
		if(!$vector_ids = $this->DumpsVector->Vector->listDumpsVectorsUnique($dump_id, AuthComponent::user('org_group_id'), AuthComponent::user('id')))
		{
			$this->paginate['empty'] = true;
		}
		
		$conditions = array(
			'DumpsVector.active' => 1,
			'DumpsVector.vector_id' => $vector_ids,
		);
		
		// adjust the search fields
		$this->DumpsVector->searchFields = array('Vector.vector', 'Vector.type', 'VectorType.name', 'Geoip.country_iso');
		$this->DumpsVector->recursive = 0;
		$this->paginate['order'] = array('DumpsVector.id' => 'desc');
		$this->paginate['fields'] = array('DumpsVector.*', 'Dump.*', 'Vector.*', 'VectorType.*', 'Hostname.*', 'Ipaddress.*', 'VectorDetail.*', 'Geoip.*');
		$this->paginate['joins'] = array(
			array(
				'table' => 'hostnames', 'alias' => 'Hostname', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Hostname.vector_id'),
			),
			array(
				'table' => 'ipaddresses', 'alias' => 'Ipaddress', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Ipaddress.vector_id'),
			),
			array(
				'table' => 'vector_details', 'alias' => 'VectorDetail', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = VectorDetail.vector_id'),
			),
			array(
				'table' => 'geoips', 'alias' => 'Geoip', 'type' => 'LEFT',
				'conditions' => array('DumpsVector.vector_id = Geoip.vector_id'),
			),
		);
		
		$this->paginate['conditions'] = $this->DumpsVector->conditions($conditions, $this->passedArgs);
		
		$this->set('dumps_vectors', $this->paginate());
	}
	
	public function toggle($field = null, $id = null)
	{
		if ($this->DumpsVector->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('Vector')));
		}
		else
		{
			$this->Session->setFlash($this->DumpsVector->modelError);
		}
		
		return $this->redirect($this->referer());
	}

	public function delete($id = null) 
	{
		$this->DumpsVector->id = $id;
		if (!$this->DumpsVector->exists()) {
			throw new NotFoundException(__('Invalid association'));
		}
		if ($this->DumpsVector->delete()) {
			$this->Session->setFlash(__('The %s was removed from this %s.', __('Vector'), __('Dump')));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('The %s was NOT removed from this %s.', __('Vector'), __('Dump')));
		$this->redirect($this->referer());
	}
}

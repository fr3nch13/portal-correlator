<?php
App::uses('AppModel', 'Model');
/**
 * Uuid Model
 *
 */
class Uuid extends AppModel 
{
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'uuid';
	
	public function addUpdate($model = false, $model_id = false, $modified = false)
	{
		$this->Behaviors->detach('Tags.Taggable');
		
		if($model and $model_id)
		{
			if(!$modified) $modified = date('Y-m-d H:i:s');
			
			// check if it exists, then update it's modified date
			$id = $this->field('id', array('model' => $model, 'model_id' => $model_id));
			
			if($id)
			{
				$this->id = $id;
				$this->saveField('modified', $modified);
			}
			else
			{
				$this->create();
				$this->save(array('Uuid' => array('uuid' => $this->generate(), 'model' => $model, 'model_id' => $model_id, 'modified' => $modified)));
			}
		}
	}
	
	public function deleted($model = false, $model_id = false, $modified = false)
	{
		$this->Behaviors->detach('Tags.Taggable');
		
		if($model and $model_id)
		{
			if(!$modified) $modified = date('Y-m-d H:i:s');
			
			$id = $this->field('id', array('model' => $model, 'model_id' => $model_id));
			if($id)
			{
				$this->id = $id;
				$this->save(array('Uuid' => array('deleted' => 1, 'modified' => $modified)));
			} 
		}
		return true;
	}
	
	public function sync()
	{
	/*
	 * Syncs two databases together
	 * for later use
	 */
	 	// find when the last sync happened
	 	
		// first find and remove all deleted items
		
		// remove the uuids marked as deleted
		
		// add/update all of the rest of the existing records
		
		// update the last sync time
	}
	
	public function generate()
	{
		return String::uuid();
	}
}

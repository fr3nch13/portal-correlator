<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */


App::uses('CommonAppController', 'Utilities.Controller');
class AppController extends CommonAppController
{
	public $components = array(
		'Auth' => array(
			'loginRedirect' => array('controller' => 'categories', 'action' => 'index'),
		),
	);
	
	public $helpers = array(
		'Local',
	);
	
	// tracks the hash mark and returns it if set
	public $hash = false;
	
	public function beforeRender()
	{
		$this->loadModel('Vector');
		$this->set('vtTypeList', $this->Vector->vtTypeList());
		return parent::beforeRender();
	}
	
	public function assign_vttracking($id = false)
	{
		$names = $this->{$this->modelClass}->guessNames($this->modelClass);
		extract($names);
		
		$this->{$thisName}->{$parent1Name}->id = $id;
		if(!$this->{$thisName}->{$parent1Name}->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', $parent1Name));
		}
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->{$thisName}->assignVtTracking($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s %s have been updated.', $this->{$thisName}->modelResults, Inflector::pluralize($parent2Name) ));
				return $this->redirect(array('controller' => $controller, 'action' => 'view', $id));
			}
			else
			{
				$flash = __('The  %s could not be updated. Please, try again.', Inflector::pluralize($parent2Name));
				if($this->{$thisName}->modelError) $flash = $this->{$thisName}->modelError;
				$this->Session->setFlash($flash);
			}
		}
		
		// get the object types
		$this->set('id', $id);
		$this->set('controller', $controller);
		$this->set('thisName', $thisName);
		$this->set('parent1Name', $parent1Name);
		$this->set('parent2Name', $parent2Name);
		
		$this->render('/Elements/assign_vttracking');
	}
	
	public function scopedResults($scope = 'org', $conditions = [], $fismaSystemConditions = [])
	{
		$scopeName = '';
		if($scope == 'org')
		{
			$scopeName = __('ORG/IC');
		}
		elseif($scope == 'division')
		{
			$scopeName = __('Division');
		}
		elseif($scope == 'branch')
		{
			$scopeName = __('Branch');
		}
		elseif($scope == 'sac')
		{
			$scopeName = __('SAC');
		}
		elseif($scope == 'owner')
		{
			$scopeName = __('System Owner');
		}
		elseif(in_array($scope, array('system', 'fisma_system')))
		{
			$scopeName = __('FISMA System');
		}
		
		$results = $this->{$this->modelClass}->scopedResults($scope, $conditions, [], false, $fismaSystemConditions);
		
		$this->set(compact(array(
			'scope', 'scopeName', 'results',
		)));
		
		return $results;
	}
}
<?php
App::uses('AppController', 'Controller');
App::uses('UtilitiesAppController', 'Utilities.Controller');

class MainController extends AppController 
{
	public function index()
	{
	}
	
	public function search($action = false)
	{	
		$this->Prg->commonProcess();
	}
	
	public function tool_duplicates()
	{
		$total_before = 0;
		$total_after = 0;
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if(isset($this->request->data['Main']['text']))
			{
				$lines = explode("\n", $this->request->data['Main']['text']);
				$total_before = count($lines);
				foreach($lines as $i => $line)
				{
					$lines[$i] = trim($line);
					if(!$lines[$i])
						unset($lines[$i]);
				}
				
				$lines = array_flip($lines);
				$lines = array_flip($lines);
				sort($lines);
				$total_after = count($lines);
				$this->request->data['Main']['text'] = implode("\n", $lines);
			}
		}
		
		$this->set(compact(['total_before', 'total_after']));
	}
}
<?php
App::uses('AppModel', 'Model');

class Main extends AppModel 
{
	public $uses = false;
	
	public $actsAs = [
		'Utilities.HttpRequest',
		'Utilities.Email',
	];
	
	public $subjectPrefix = false;
	
	public $emailTo = 'user@example.com';
	
	public function sendEmail($subject = false, $body = false, $to = false)
	{
		if(is_array($body))
			$body = implode("\n", $body);
		
		$subject = $this->subjectPrefix. ' - '. $subject;
		
		if(!$to)
			$to = $this->emailTo;
		
		$this->Email_reset();
		$this->Email_set('emailFormat', 'text');
		$this->Email_set('to', $to);
		$this->Email_set('from', $to);
		$this->Email_set('subject', $subject);
		$this->Email_set('body', $body);
		
		return $this->Email_execute();
	}
}
<?php namespace Bozboz\Enquiry;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Mail\Mailer;
use Illuminate\Config\Repository AS Config;

class Process
{
	private $validator;
	private $validation;
	private $mailer;
	private $config;

	public function __construct(Validator $validator, Mailer $mailer, Config $config)
	{
		$this->validator = $validator;
		$this->mailer = $mailer;
		$this->config = $config;
	}

	public function make(Array $input, Array $rules)
	{
		$this->validation = $this->validator->make($input, $rules);
		if ($this->validation->passes()) {
			$this->sendEmail($input);
		}
		return $this;
	}

	private function sendEmail(array $fields)
	{
		if(isset($fields['_token']))
			unset($fields['_token']);

		$this->mailer->send(array('emails.enquiry', 'emails.enquiry-text'), array('fields' => $fields), function($message)
		{
			$config = $this->config;
			$message->to($config->get('app.enquiry_recipient_address'),	$config->get('app.enquiry_recipient_name'))->subject('Contact Enquiry');
		});
	}

	public function fails()
	{
		return $this->validation->fails();
	}

	public function validator()
	{
		return $this->validation;
	}
}

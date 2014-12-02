<?php namespace Bozboz\Enquiry;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Mail\Mailer;
use Illuminate\Config\Repository AS Config;
use DateTime;

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

	/**
	 * Validate (on given $rules) and send an enquiry, comprised of given $input
	 *
	 * @param  Array  $input
	 * @param  Array  $rules
	 * @return $this
	 */
	public function make(Array $input, Array $rules)
	{
		$this->validation = $this->validator->make($input, $rules);
		if ($this->validation->passes()) {
			$this->sendEmail($input);
		}
		return $this;
	}

	/**
	 * Send enquiry email email
	 *
	 * @param  array  $fields
	 * @return void
	 */
	private function sendEmail(array $fields)
	{
		if(isset($fields['_token']))
			unset($fields['_token']);

		$views = ['emails.enquiry', 'emails.enquiry-text'];

		$vars = [
			'fields' => $fields,
			'time' => new DateTime
		];

		$this->mailer->send($views, $vars, function($message)
		{
			$config = $this->config;
			$message
				->to($config->get('app.enquiry_recipient_address'),	$config->get('app.enquiry_recipient_name'))
				->subject('Contact Enquiry');
		});
	}

	/**
	 * Determine if validation has failed
	 *
	 * @return boolean
	 */
	public function fails()
	{
		return $this->validation->fails();
	}

	/**
	 * Get validation errors
	 *
	 * @return Illuminate\Support\MessageBag
	 */
	public function getErrors()
	{
		return $this->validation->errors();
	}
}

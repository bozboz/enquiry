<?php namespace Bozboz\Enquiry;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Mail\Mailer;
use Illuminate\Config\Repository AS Config;
use DateTime;

class Process
{
	private $validator;
	private $mailer;
	private $config;

	private $recipientName;
	private $recipientAddress;
	private $validation;

	public function __construct(Validator $validator, Mailer $mailer, Config $config)
	{
		$this->validator = $validator;
		$this->mailer = $mailer;
		$this->config = $config;
	}

	public function to($recipientAddress, $recipientName = null)
	{
		$this->recipientAddress = $recipientAddress;
		$this->recipientName = $recipientName;

		return $this;
	}

	/**
	 * Validate (on given $rules) and send an enquiry, comprised of given $input
	 *
	 * @param  Array  $input
	 * @param  Array  $rules
	 * @param  mixed  $views
	 * @param  string  $subject
	 * @return $this
	 */
	public function make(Array $input, Array $rules, $views = null, $subject = null)
	{
		$this->validation = $this->validator->make($input, $rules);
		if ($this->validation->passes()) {
			$this->sendEmail($input, $views, $subject);
		}
		return $this;
	}

	/**
	 * Send enquiry email email
	 *
	 * @param  array  $fields
	 * @return void
	 */
	private function sendEmail(array $fields, $views, $subject)
	{
		if(isset($fields['_token']))
			unset($fields['_token']);

		$views = $views ?: ['emails.enquiry', 'emails.enquiry-text'];

		$vars = [
			'fields' => $fields,
			'time' => new DateTime
		];

		$this->mailer->send($views, $vars, function($message) use ($subject)
		{
			$config = $this->config;
			$message->to(
				$this->recipientAddress ?: $config->get('app.enquiry_recipient_address'),
				$this->recipientName ?: $config->get('app.enquiry_recipient_name')
			)->subject($subject ?: 'Contact Enquiry');
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
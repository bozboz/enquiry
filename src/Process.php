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
	private $replyName;
	private $replyAddress;
	private $validation;
	private $subject;

	public function __construct(Validator $validator, Mailer $mailer, Config $config)
	{
		$this->validator = $validator;
		$this->mailer = $mailer;
		$this->config = $config;
	}

	/**
	 * Override the default recipient on the enquiry
	 *
	 * @param  string  $recipientAddress
	 * @param  string  $recipientName
	 * @return $this
	 */
	public function to($recipientAddress, $recipientName = null)
	{
		$this->recipientAddress = $recipientAddress;
		$this->recipientName = $recipientName;

		return $this;
	}

	/**
	 * Override the default reply-to on the enquiry
	 *
	 * @param  string  $recipientAddress
	 * @param  string  $recipientName
	 * @return $this
	 */
	public function replyTo($replyAddress, $replyName = null)
	{
		$this->replyAddress = $replyAddress;
		$this->replyName = $replyName;

		return $this;
	}

	/**
	 * Override the default subject on the enquiry
	 *
	 * @param  string  $subject
	 * @return $this
	 */
	public function withSubject($subject)
	{
		$this->subject = $subject;

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
			$this->sendEmail($input, $views, $this->subject ?: $subject);
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

		$this->mailer->send($views, $vars, function($message) use ($subject, $vars)
		{
			$config = $this->config;
			$message->to(
				$this->recipientAddress ?: $config->get('app.enquiry_recipient_address'),
				$this->recipientName ?: $config->get('app.enquiry_recipient_name')
			)->replyTo(
				$this->replyAddress ?: $config->get('mail.from.address'),
				$this->replyName ?: $config->get('mail.from.name')
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

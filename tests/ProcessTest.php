<?php namespace Bozboz\Enquiry;

use Mockery;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $mailer;
	private $config;

	public function setup()
	{
		$this->validator = Mockery::mock('Illuminate\Validation\Factory');
		$this->mailer = Mockery::mock('Illuminate\Mail\Mailer');
		$this->config = Mockery::mock('Illuminate\Config\Repository');
	}

	public function tearDown()
	{
		Mockery::close();
	}

	public function testSuccessfulEnquiry()
	{
		$this->validator->shouldReceive('make')->andReturn($this->validator);
		$this->validator->shouldReceive('passes')->andReturn(true);
		$this->mailer->shouldReceive('send');

		$process = new Process($this->validator, $this->mailer, $this->config);
		$process->make(array(), array());
	}

	public function testFailsValidation()
	{
		$this->validator->shouldReceive('make')->andReturn($this->validator);
		$this->validator->shouldReceive('passes')->andReturn(false);
		$this->validator->shouldReceive('fails')->andReturn(true);

		$process = new Process($this->validator, $this->mailer, $this->config);
		$process->make(array(), array());

		$this->assertTrue($process->fails());
	}
}

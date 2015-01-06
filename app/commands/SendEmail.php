<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendEmail extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mail:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sends a queued email.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$emailId = (int)$this->argument("id");

		$email = Email::find($emailId);
		if (!$email) {
			$this->error(sprintf("Email ID %d could not be found", $emailId));
			return;
		}

		mail($email->user->email, $email->subject, $email->plain);

		$email->status = "sent";
		$email->save();

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('id', InputArgument::REQUIRED, 'The email ID in the database.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}

}

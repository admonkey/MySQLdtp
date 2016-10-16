<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command {
	protected function configure(){
		$this->setName('create')
			->setDescription('Create a database and user accounts.')
			->addArgument(
				'name',
				InputArgument::OPTIONAL,
				'What name would you like to use for the database?'
			)->addOption(
				'environment',
				'e',
				InputOption::VALUE_REQUIRED,
				'Do you want a development, test, or production environment?'
			)->addOption(
				'hostname',
				'H',
				InputOption::VALUE_REQUIRED,
				'What is the database server hostname?'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Create');

		$data['database'] = $database = App::get('Name')->database();
		$data['username'] = App::get('Name')->user();
		$data['password'] = (new Random)->password();

		if( App::get('Query')->execute($this->sql($data)) ){
			App::get('io')->success("Created database: $database");
		}
	}

	protected function sql(Array $data){
		extract($data);
		$hostname = App::get('Hostname');
		return "
			CREATE DATABASE $database
			CHARACTER SET utf8
			COLLATE utf8_unicode_ci;

			CREATE USER '$username'@'$hostname'
			IDENTIFIED BY '$password';
		";
	}
}

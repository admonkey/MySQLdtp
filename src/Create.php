<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class Create extends Command {
	protected $errorMessages;
	protected $io;
	protected $name;
	protected $id;
	protected $environment;

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
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Create Database');

		$name = App::get(Name::class)->database();

		App::get(Query::class)->execute('SELECT 1');
		App::get('io')->success("Created database: $name");
	}
}

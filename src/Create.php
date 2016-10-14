<?php
namespace jpuck\dbdtp;

use PDO;
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

		App::get(Name::class);

		$this->executeQuery();
		App::get('io')->success(
			'Created database: '.App::get(Name::class)->database()
		);
	}

	protected function getLogin() : Array {
		$q = 'What is the database hostname?';
		$hostname = App::get('io')->ask($q, 'localhost');

		$q = 'What is the privileged username?';
		$username = App::get('io')->ask($q, 'root');

		$password = App::get('io')->askHidden(
			'What is the password?', function ($password) {
				if (empty($password)) {
					throw new \RuntimeException('Password cannot be empty.');
				}
				return $password;
			}
		);

		return [
			'hostname'=>$hostname,
			'username'=>$username,
			'password'=>$password
		];
	}

	protected function executeQuery() : Bool {
		extract($this->getLogin());
		$pdo = new PDO(
			"mysql:host=$hostname;
			charset=UTF8",
			$username,
			$password
		);
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return true;
	}
}

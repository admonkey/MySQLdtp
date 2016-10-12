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
	protected $input;
	protected $output;
	protected $io;
	protected $name;
	protected $id;
	protected $environment;
	protected $idchars =
		'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
		$this->input = $input;
		$this->output = $output;
		$io = new SymfonyStyle($input, $output);
		$this->io = $io;
		App::bind('input', $input);
		App::bind('output', $output);
		App::bind('io', $io);

		$io->title('Create Database');

		// generate ID
		$this->id = (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars);

		$dbname = new Name;
		$this->getEnvironment();

		// validation
		$formatter = $this->getHelper('formatter');
		if (!empty($this->errorMessages)){
			$formattedBlock = $formatter
				->formatBlock($this->errorMessages, 'error');
			$output->writeln($formattedBlock);
			return 1;
		}

		$this->executeQuery();
		$io->success('Created database: '.$dbname->get());
	}

	protected function getEnvironment(){
		$this->environment = $this->input->getOption('environment');
		if (!$this->validateEnvironment()){
			$helper = $this->getHelper('question');
			$question = new ChoiceQuestion(
				'Do you want a development, test, or production environment?',
				array('development', 'test', 'production'),
				'development'
			);
			$question->setErrorMessage('%s is invalid.');
			$this->environment =
				$helper->ask($this->input, $this->output, $question);
		}

		$this->output->writeln(
			"<comment>environment:</> <info>{$this->environment}</>"
		);
		$this->io->newLine();
	}

	protected function validateEnvironment() : Bool {
		if (
			in_array(
				strtolower($this->environment), ['development','dev','d']
			)
		){
			$this->environment = 'development';
			return true;
		} elseif (
			in_array(
				strtolower($this->environment), ['test','t']
			)
		){
			$this->environment = 'test';
			return true;
		} elseif (
			in_array(
				strtolower($this->environment), ['production','prod','p']
			)
		){
			$this->environment = 'production';
			return true;
		}

		return false;
	}

	protected function getLogin() : Array {
		$q = 'What is the database hostname?';
		$hostname = $this->io->ask($q, 'localhost');

		$q = 'What is the privileged username?';
		$username = $this->io->ask($q, 'root');

		$password = $this->io->askHidden(
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

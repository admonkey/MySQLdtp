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
		$formatter = $this->getHelper('formatter');
		$io->title('Create Database');

		// generate ID
		$this->id = (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars);

		$this->getDbName();
		$this->getEnvironment();

		// validation
		if (!empty($this->errorMessages)){
			$formattedBlock = $formatter
				->formatBlock($this->errorMessages, 'error');
			$output->writeln($formattedBlock);
			return 1;
		}

		$this->getLogin();
		$io->success('Created database: '.$this->dbName());
	}

	protected function getDbName(){
		// get database name
		$this->name = $this->input->getArgument('name');
		if (empty($this->name)){
			$helper = $this->getHelper('question');
			$question = new Question(
				'Please enter a name for the database (max 7 characters): '
			);
			$this->name = $helper->ask($this->input, $this->output, $question);
		}

		// validation
		if (empty($this->name)){
			$this->errorMessages []= 'Name cannot be empty.';
		}

		// validation
		$name_length = strlen($this->name);
		if ($name_length > 7){
			$this->errorMessages []=
				"Maximum 7 characters allowed. {$this->name} is $name_length.";
		}

		$this->output->writeln("<comment>name:</> <info>".$this->dbName()."</>");
		$this->io->newLine();
	}

	protected function dbName(){
		return "{$this->name}_"
			.strtoupper(substr($this->environment,0,1))
			."_{$this->id}";
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

	protected function getLogin(){
		// validates the given answer
		$this->io->askHidden('What is your password?', function ($password) {
			if (empty($password)) {
				throw new \RuntimeException('Password cannot be empty.');
			}

			return $password;
		});
	}
}

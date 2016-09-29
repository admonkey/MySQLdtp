<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Create extends Command {
	protected $errorMessages;
	protected $input;
	protected $output;
	protected $name;
	protected $idchars =
		'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	protected function configure(){
		$this->setName('create')
			->setDescription('Create a database and user accounts.')
			->addArgument(
				'name',
				InputArgument::OPTIONAL,
				'What name would you like to use for the database?'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		$this->input = $input;
		$this->output = $output;
		$formatter = $this->getHelper('formatter');

		$this->getDbName();

		// validation
		if (!empty($this->errorMessages)){
			$formattedBlock = $formatter
				->formatBlock($this->errorMessages, 'error');
			$output->writeln($formattedBlock);
			return 1;
		}

		// generate ID
		$id = (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars);
		$this->name = "{$this->name}_$id";

		// success
		$output->writeln("<comment>database name:</> <info>{$this->name}</>");
	}

	protected function getDbName(){
		// get database name
		$this->name = $this->input->getArgument('name');
		if (empty($this->name)){
			$helper = $this->getHelper('question');
			$question = new Question(
				'Please enter a name for the database (max 7 characters) '
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
	}
}

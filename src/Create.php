<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Create extends Command {

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
		$formatter = $this->getHelper('formatter');

		// get database name
		$name = $input->getArgument('name');
		if (empty($name)){
			$helper = $this->getHelper('question');
			$question = new Question(
				'Please enter a name for the database (max 7 characters) '
			);
			$name = $helper->ask($input, $output, $question);
		}

		// validation
		if (empty($name)){
			$errorMessages []= 'Name cannot be empty.';
		}

		// validation
		$name_length = strlen($name);
		if ($name_length > 7){
			$errorMessages []=
				"Maximum 7 characters allowed. $name is $name_length.";
		}

		// validation
		if (!empty($errorMessages)){
			$formattedBlock = $formatter->formatBlock($errorMessages, 'error');
			$output->writeln($formattedBlock);
			return 1;
		}

		// generate ID
		$id = (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars);
		$name = "{$name}_$id";

		// success
		$output->writeln("<comment>database name:</> <info>[ $name ]</>");
	}
}

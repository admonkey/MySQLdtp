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

		$name = $input->getArgument('name');
		if (empty($name)){
			$helper = $this->getHelper('question');
			$question = new Question(
				'What name would you like to use for the database? '
			);
			$name = $helper->ask($input, $output, $question);
		}

		if (empty($name)){
			$errorMessages = array('Error!', 'Name cannot be empty.');
			$formattedBlock = $formatter->formatBlock($errorMessages, 'error');
			$output->writeln($formattedBlock);
			return;
		}

		// generate ID
		$id = (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars);
		$name = "{$name}_$id";

		$output->writeln("database name: <info>[ $name ]</>");

	}
}

<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Drop extends Command {
	protected function configure(){
		$this->setName('drop')
			->setDescription('Destroy database and all related user accounts.')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'What\'s the name of the database you would you like to drop?'
			)->addOption(
				'hostname',
				'H',
				InputOption::VALUE_REQUIRED,
				'What is the database server hostname?'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Drop Database');

		$name = App::get('in')->getArgument('name');

		if( App::get(Query::class)->execute($this->sql($name)) ){
			App::get('io')->success("Dropped database: $name");
		}
	}

	protected function sql(String $name){
		return "
			DROP DATABASE $name;
		";
	}
}

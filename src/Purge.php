<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use jpuck\phpdev\Functions as jp;

class Purge extends Command {
	protected function configure(){
		$this->setName('purge')
			->setDescription('Drop everything from a Microsoft database')
			->addArgument(
				'pdo',
				InputArgument::REQUIRED,
				'File that returns an instance of PDO'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Purge');

		$pdo = require App::get('in')->getArgument('pdo');
		jp::CleanMsSQLdb($pdo);
		App::get('io')->success("Purged");
	}
}

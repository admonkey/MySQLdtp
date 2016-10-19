<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Execute extends Command {
	protected function configure(){
		$this->setName('execute')
			->setDescription('Execute SQL scripts')
			->addArgument(
				'pdo',
				InputArgument::OPTIONAL,
				'File that returns an instance of PDO'
			)->addArgument(
				'sql',
				InputArgument::OPTIONAL,
				'SQL script file'
			);
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Execute');

		$pdo = require App::get('in')->getArgument('pdo');

		$file = App::get('in')->getArgument('sql');
		$sql  = file_get_contents($file);

		$result = $pdo->exec($sql);
		if($result !== false){
			App::get('io')->success("Executed $file");
		}
	}
}

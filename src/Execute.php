<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Execute extends Command {
	use QueryTrait;
	protected function configure(){
		$this->setName('execute')
			->setDescription('Execute SQL scripts')
			->addArgument(
				'sql',
				InputArgument::OPTIONAL,
				'SQL script file'
			);
		$this->addOptions();
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Execute');

		$pdo = require App::get('in')->getOption('pdo');

		$file = App::get('in')->getArgument('sql');
		if(pathinfo($file, PATHINFO_EXTENSION) === 'sql'){
			$files []= $file;
		} else {
			$files = file($file, FILE_IGNORE_NEW_LINES);
		}

		foreach($files as $file){
			$sql = file_get_contents($file);
			$result = App::get('Query')->execute($sql);
			if($result !== false){
				App::get('io')->success("Executed $file");
			}
		}
	}
}

<?php
namespace jpuck\qdbp;

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
				'SQL script, or list of scripts'
			);
		$this->addOptions();
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Execute');

		$file = App::get('in')->getArgument('sql');
		$pathinfo = pathinfo($file);
		if($pathinfo['extension'] === 'sql'){
			$files []= $file;
		} else {
			$files = file($file, FILE_IGNORE_NEW_LINES);
			// eager load in case of PDOFile path before chdir
			App::get('Query');
			chdir($pathinfo['dirname']);
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

<?php
namespace jpuck\qdbp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Drop extends Command {
	use QueryTrait;
	protected $database;
	protected function configure(){
		$this->setName('drop')
			->setDescription('Destroy database and all related user accounts.')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'What\'s the name of the database you would you like to drop?'
			);
		$this->addOptions();
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Drop');

		$this->database = App::get('in')->getArgument('name');

		$dropUsers = $this->dropUsers();

		if( App::get('Query')->execute($this->dropDatabase()) ){
			App::get('io')->success("Dropped database: {$this->database}");
		}

		if(!empty($dropUsers)){
			if( App::get('Query')->execute($dropUsers) ){
				App::get('io')->success("$dropUsers");
			}
		}
	}

	protected function dropDatabase() : String {
		return "DROP DATABASE {$this->database};";
	}

	protected function dropUsers() : String {
		$hostname = App::get('Hostname');
		App::get('Query')->execute("USE mysql;");
		$sql = "
			SELECT `User`
			FROM   `user`
			WHERE  `User` LIKE '{$this->database}%'
			  AND  `Host` =    '$hostname';
		";
		$stmt = App::get('Query')->query($sql);
		$sql = '';
		while(!empty($result = $stmt->fetch(\PDO::FETCH_ASSOC))){
			$sql .= "DROP USER '$result[User]'@'$hostname';\n";
		}
		$stmt->closeCursor();
		return trim($sql);
	}
}

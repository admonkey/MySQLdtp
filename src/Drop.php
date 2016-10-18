<?php
namespace jpuck\dbdtp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Drop extends Command {
	protected $database;
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
			->title('Drop');

		$this->database = App::get('in')->getArgument('name');

		$dropUsers = $this->dropUsers();

		if( App::get(Query::class)->execute($this->dropDatabase()) ){
			App::get('io')->success("Dropped database: {$this->database}");
		}

		if( App::get(Query::class)->execute($dropUsers) ){
			App::get('io')->success("$dropUsers");
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
		while($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
			$sql .= "DROP USER '$result[User]'@'$hostname';\n";
		}
		$stmt->closeCursor();
		return trim($sql);
	}
}

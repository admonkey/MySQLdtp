<?php
namespace jpuck\qdbp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command {
	use QueryTrait;
	protected function configure(){
		$this->setName('create')
			->setDescription('Create a database and user accounts.')
			->addArgument(
				'name',
				InputArgument::OPTIONAL,
				'What name would you like to use for the database?'
			)->addOption(
				'environment',
				'e',
				InputOption::VALUE_REQUIRED,
				'Do you want a development, test, or production environment?'
			);
		$this->addOptions();
	}

	public function execute(InputInterface $input, OutputInterface $output){
		App::bind('io', new IO($this, $input, $output))
			->title('Create');

		$database = App::get('Name')->database();
		$username = App::get('Name')->user();

		if( App::get('Query')->execute($this->sql($database, $username)) ){
			App::get('io')->success("Created database: $database");
		}
	}

	protected function sqlUserGrant(String $username) : String {
		$last = $username[strlen($username)-1];
		$database = App::get('Name')->database();

		switch($last){
			case 'A':
				return "GRANT ALL PRIVILEGES ON $database.*";
			case 'E':
				return "GRANT EXECUTE ON $database.*";
			default:
				throw new \InvalidArgumentException(
					"Expecting A or E, got $last"
				);
		}
	}

	protected function sqlUser(String $username) : String {
		$hostname   = App::get('Hostname');
		$password   = App::get('Random')->password();
		$permission = $this->sqlUserGrant($username);
		file_put_contents("$username.pdo.php", new PDOFile(
			App::get('Name')->database(),
			$username,
			$password,
			$hostname
		));
		return "
			CREATE USER '$username'@'$hostname'
			IDENTIFIED BY '$password';
			$permission TO '$username'@'$hostname';
		";
	}

	protected function sql($database, $username){
		$query = "
			CREATE DATABASE $database
			CHARACTER SET utf8mb4
			COLLATE utf8mb4_unicode_520_ci;
		";

		if(is_array($username)){
			foreach($username as $user){
				$query .= $this->sqlUser($user);
			}
			return $query;
		}

		return $query . $this->sqlUser($username);
	}
}

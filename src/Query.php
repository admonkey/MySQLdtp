<?php
namespace jpuck\dbdtp;
use PDO;
use RuntimeException;

class Query {
	protected $hostname;
	protected $username;
	protected $password;
	protected $pdo;

	public function __construct(){
		$this->getLogin();
		$this->pdo = new PDO(
			"mysql:host={$this->hostname};
			charset=UTF8",
			$this->username,
			$this->password
		);
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function execute(String $sql) : Bool {
		return $this->pdo->query($sql)->closeCursor();
	}

	protected function getLogin(){
		$q = 'What is the server hostname?';
		$this->hostname = App::get('io')->ask($q, 'localhost');

		$q = 'What is the privileged username?';
		$this->username = App::get('io')->ask($q, 'root');

		$this->password = App::get('io')->askHidden(
			'What is the password?', function ($password) {
				if (empty($password)) {
					throw new \RuntimeException('Password cannot be empty.');
				}
				return $password;
			}
		);
	}
}

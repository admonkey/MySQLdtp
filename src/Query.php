<?php
namespace jpuck\dbdtp;
use PDO;
use RuntimeException;

class Query {
	protected $pdo;

	public function __construct(){
		$hostname = App::get('Hostname');
		extract($this->getLogin());
		$this->pdo = new PDO(
			"mysql:host=$hostname;
			charset=UTF8",
			$username,
			$password
		);
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function execute(String $sql) : Bool {
		return $this->pdo->query($sql)->closeCursor();
	}

	protected function getLogin() : Array {
		$login['username'] = App::get('io')->ask(
			'What is the privileged username?', 'root'
		);

		$login['password'] = App::get('io')->askHidden(
			'What is the password?', function ($password) {
				if (empty($password)) {
					throw new RuntimeException('Password cannot be empty.');
				}
				return $password;
			}
		);

		return $login;
	}
}

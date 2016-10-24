<?php
namespace jpuck\qdbp;
use PDO;
use PDOException;
use RuntimeException;
use InvalidArgumentException;

class Query {
	protected $pdo;

	public function __construct(){
		$pdo = App::get('in')->getOption('pdo');
		if(empty($pdo)){
			$this->connect();
		} else {
			$pdo = require $pdo;
			if($pdo instanceof PDO){
				$this->pdo = $pdo;
			} else {
				throw new InvalidArgumentException(
					'File does not return instance of PDO'
				);
			}
		}
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function execute(String $sql) : Bool {
		$this->pdo->beginTransaction();
		try {
			$statement = $this->pdo->query($sql);
			while ($statement->nextRowset()) {
				/* https://bugs.php.net/bug.php?id=61613 */
			};
			$this->pdo->commit();
		} catch (PDOException $e) {
			$this->pdo->rollBack();
			throw $e;
		} finally {
			if(isset($statement)){
				$success = $statement->closeCursor();
			}
		}
		return $success;
	}

	public function query(String $sql){
		return $this->pdo->query($sql);
	}

	protected function connect(){
		$hostname = App::get('Hostname');
		extract($this->getLogin());
		$this->pdo = new PDO(
			"mysql:host=$hostname;
			charset=UTF8",
			$username,
			$password
		);
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

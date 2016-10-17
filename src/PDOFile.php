<?php
namespace jpuck\dbdtp;

class PDOFile {
	protected $database;
	protected $username;
	protected $password;
	protected $hostname;

	public function __construct(
		String $database,
		String $username,
		String $password,
		String $hostname = 'localhost'
	){
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
	}

	public function __toString(){
		return trim(preg_replace('/\t+/', '', "
			<?php
			return (function(){
			    \$hostname = '{$this->hostname}';
			    \$database = '{$this->database}';
			    \$username = '{$this->username}';
			    \$password = '{$this->password}';

			    \$pdo = new PDO(\"mysql:host=\$hostname;
			        charset=UTF8;
			        dbname=\$database\",
			        \$username,
			        \$password
			    );
			    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			    return \$pdo;
			})();
		"));
	}
}

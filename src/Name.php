<?php
namespace jpuck\dbdtp;
use RuntimeException;

class Name {
	protected $name;
	public function __construct(String $name = null){
		App::get(Environment::class);

		// get database name
		if(empty($name)){
			$name = App::get('in')->getArgument('name');
		}

		try {
			$name = $this->validate($name);
		} catch (RuntimeException $e) {
			$q = 'Please enter a name for the database (max 7 characters)';
			$name = App::get('io')->ask($q, null, function($name){
				return $this->validate($name);
			});
		}
		$this->name = $name;

		// generate ID
		App::bind('id', (new Random)->id());

		$name = $this->database();
		App::get('io')->write("<comment>name:</> <info>$name</>");
	}

	protected function validate($name){
		if (empty($name)) {
			throw new RuntimeException('Name cannot be empty.');
		}

		$name_length = strlen($name);
		if ($name_length > 7){
			throw new RuntimeException(
				"Maximum 7 characters allowed. $name is $name_length."
			);
		}

		return $name;
	}

	public function user(){
		$name = $this->name;
		switch (App::get('environment')) {
			case 'development':
				$env  = 'A';
				break;
			// TODO: case 'test':
			case 'production':
				$env  = 'E';
				break;
		}
		$id = App::get('id');
		return "{$name}_U{$env}_{$id}";
	}

	public function database(){
		$name = $this->name;
		$env  = App::get('environment');
		$env  = strtoupper(substr($env,0,1));
		$id   = App::get('id');
		return "{$name}_{$env}_{$id}";
	}

	public function __toString(){
		return $this->name;
	}
}

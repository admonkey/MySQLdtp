<?php
namespace jpuck\dbdtp;
use RuntimeException;

class Name {
	public function __construct(){
		// get database name
		$name = App::get('input')->getArgument('name');
		try {
			$name = $this->validateDbName($name);
		} catch (RuntimeException $e) {
			if (empty($name)){
				$q = 'Please enter a name for the database (max 7 characters)';
				$name = App::get('io')->ask($q, null, function($name){
					return $this->validateDbName($name);
				});
			}
		}
		App::bind('name', $name);

		$name = $this->get();
		App::get('io')->writeln("<comment>name:</> <info>$name</>");
		App::get('io')->newLine();
	}

	protected function validateDbName($name){
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

	public function get(){
		$name = App::get('name');
		$env  = App::get('environment');
		$id   = App::get('id');
		return "{$name}_"
			.strtoupper(substr($env,0,1))
			."_$id";
	}
}

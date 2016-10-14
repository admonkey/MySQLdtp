<?php
namespace jpuck\dbdtp;
use RuntimeException;

class Name {
	protected $idchars =
		'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public function __construct(){
		// get database name
		$name = App::get('input')->getArgument('name');
		try {
			$name = $this->validate($name);
		} catch (RuntimeException $e) {
			if (empty($name)){
				$q = 'Please enter a name for the database (max 7 characters)';
				$name = App::get('io')->ask($q, null, function($name){
					return $this->validate($name);
				});
			}
		}
		App::bind('name', $name);

		// generate ID
		App::bind('id', (
			(new \RandomLib\Factory)->getMediumStrengthGenerator()
		)->generateString(5, $this->idchars));

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

	public function database(){
		$name = App::get('name');
		$env  = App::get('environment');
		$env  = strtoupper(substr($env,0,1));
		$id   = App::get('id');
		return "{$name}_{$env}_{$id}";
	}
}

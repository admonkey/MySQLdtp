<?php
namespace jpuck\qdbp;
use RandomLib\Factory;
use InvalidArgumentException;

class Random {
	protected $characters = [
		'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
		'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'numbers'   => '0123456789',
		'special'   => '{[(</|>)]}~!@#%^&*-_=+;:,.?',
	];

	public function id(Int $length = 5) : String {
		$chars =
			$this->characters['lowercase'] .
			$this->characters['uppercase'] .
			$this->characters['numbers'];
		return $this->generate($length, $chars);
	}

	public function password(Int $length = 32) : String {
		$charsets = count($this->characters);
		if($length < $charsets){
			throw new InvalidArgumentException(
				"Password must contain at least $charsets characters."
			);
		}

		// http://stackoverflow.com/a/31634299/4233593
		$password = '';

		// make sure one of each
		foreach($this->characters as $chars){
			$password .= $this->generate(1, $chars);
		}

		$chars = implode('', $this->characters);
		$password .= $this->generate($length - $charsets, $chars);
		return str_shuffle($password);
	}

	public function generate(Int $length, String $chars) : String {
		if($length < 1){
			throw new InvalidArgumentException(
				"Length must be greater than zero."
			);
		}

		if(empty($chars)){
			throw new InvalidArgumentException(
				"Must provide some characters."
			);
		}

		return ($this->generator ?? $this->generator = (new Factory)
			->getMediumStrengthGenerator())
			->generateString($length, $chars);
	}
}

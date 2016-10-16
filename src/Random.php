<?php
namespace jpuck\dbdtp;
use RandomLib\Factory;
use RuntimeException;

class Random {
	protected $alphanumeric =
		'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	protected $special = '`~!@#$%^&*()-_+=;:<,>.?/ {[|\]}';
	protected $factory;

	public function id(Int $length = 5) : String {
		$chars = $this->alphanumeric;
		return $this->generate($length, $chars);
	}

	public function password(Int $length = 32) : String {
		// http://stackoverflow.com/a/31634299/4233593
		$chars = $this->alphanumeric . $this->special;
		return $this->generate($length, $chars);
	}

	protected function generate(Int $length, String $chars) : String {
		return ($this->factory ?? $this->factory = new Factory)
			->getMediumStrengthGenerator()
			->generateString($length, $chars);
	}
}

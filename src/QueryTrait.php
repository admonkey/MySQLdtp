<?php
namespace jpuck\dbdtp;
use Symfony\Component\Console\Input\InputOption;

trait QueryTrait {
	protected function addOptions(){
		$this->addOption(
			'hostname',
			'H',
			InputOption::VALUE_REQUIRED,
			'What is the database server hostname?'
		)->addOption(
			'pdo',
			'p',
			InputOption::VALUE_REQUIRED,
			'File that returns an instance of PDO'
		);
	}
}

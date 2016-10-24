<?php
namespace jpuck\qdbp;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Environment {
	protected $environment;
	public function __construct(){
		if (!$this->validate(App::get('in')->getOption('environment'))){
			$question = new ChoiceQuestion(
				'Do you want a development, test, or production environment?',
				['development', 'test', 'production'],
				'development'
			);
			$question->setErrorMessage('%s is invalid.');
			$this->validate(App::get('io')->question($question));
		}

		App::get('io')->write(
			"<comment>environment:</> <info>{$this->environment}</>"
		);
	}

	protected function validate(String $environment = null) : Bool {
		if(empty($environment)){
			return false;
		}

		switch(strtolower($environment[0])){
			case 'd':
				$this->environment = 'development';
				return true;
			case 't':
				$this->environment = 'test';
				return true;
			case 'p':
				$this->environment = 'production';
				return true;
			default:
				return false;
		}
	}

	public function __toString(){
		return $this->environment;
	}
}

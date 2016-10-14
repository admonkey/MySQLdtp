<?php
namespace jpuck\dbdtp;
use RuntimeException;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Environment {
	public function __construct(){
		if (!$this->validate(App::get('io')->getOption('environment'))){
			$question = new ChoiceQuestion(
				'Do you want a development, test, or production environment?',
				['development', 'test', 'production'],
				'development'
			);
			$question->setErrorMessage('%s is invalid.');
			$this->validate(App::get('io')->question($question));
		}
		$environment = App::get('environment');

		App::get('io')->write("<comment>environment:</> <info>$environment</>");
	}

	protected function validate(String $environment = null) : Bool {
		if(empty($environment)){
			return false;
		}

		if (
			in_array(
				strtolower($environment), ['development','dev','d']
			)
		){
			App::bind('environment','development');
			return true;
		} elseif (
			in_array(
				strtolower($environment), ['test','t']
			)
		){
			App::bind('environment','test');
			return true;
		} elseif (
			in_array(
				strtolower($environment), ['production','prod','p']
			)
		){
			App::bind('environment','production');
			return true;
		}

		return false;
	}
}

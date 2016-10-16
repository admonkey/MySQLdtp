<?php
namespace jpuck\dbdtp;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Environment {
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
		$environment = App::get('environment');

		App::get('io')->write("<comment>environment:</> <info>$environment</>");
	}

	protected function validate(String $environment = null) : Bool {
		if(empty($environment)){
			return false;
		}

		switch(strtolower($environment)[0]){
			case 'd':
				App::bind('environment','development');
				break;
			case 't':
				App::bind('environment','test');
				break;
			case 'p':
				App::bind('environment','production');
				break;
			default:
				return false;
		}

		return true;
	}
}

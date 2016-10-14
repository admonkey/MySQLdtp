<?php
namespace jpuck\dbdtp;
use RuntimeException;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Environment {
	public function __construct(){
		$environment = App::get('io')->getOption('environment');
		if (!$this->validate($environment)){
			$helper = new Question;
			$question = new ChoiceQuestion(
				'Do you want a development, test, or production environment?',
				array('development', 'test', 'production'),
				'development'
			);
			$question->setErrorMessage('%s is invalid.');
			$environment =
				$helper->ask($this->input, $this->output, $question);
		}

		$this->output->writeln(
			"<comment>environment:</> <info>{$this->environment}</>"
		);
		$this->io->newLine();
	}

	protected function validate(String $environment) : Bool {
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

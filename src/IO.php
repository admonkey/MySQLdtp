<?php
namespace jpuck\dbdtp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class IO {
	protected $cmd;
	protected $in;
	protected $out;
	protected $style;

	public function __construct(Command $cmd, InputInterface $in, OutputInterface $out){
		$this->cmd = $cmd;
		$this->in  = $in;
		$this->out = $out;
		App::bind('in',$in);
		App::bind('out',$out);
		$this->style = new SymfonyStyle($in, $out);
	}

	public function question($question){
		$helper = $this->cmd->getHelper('question');
		return $helper->ask($this->in, $this->out, $question);
	}

	public function write(String $msg){
		$this->style->writeln($msg);
		$this->style->newLine();
	}

	public function __call($name, $arguments){
		return $this->style->$name(...$arguments);
	}
}

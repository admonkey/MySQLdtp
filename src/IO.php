<?php
namespace jpuck\dbdtp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class IO {
	protected $style;
	public function __construct(Command $cmd, InputInterface $in, OutputInterface $out){
		$this->style = new SymfonyStyle($in, $out);
	}

	public function title(String $title){
		return $this->style->title($title);
	}
}

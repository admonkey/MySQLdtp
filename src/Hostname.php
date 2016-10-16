<?php
namespace jpuck\dbdtp;

class Hostname {
	protected $hostname;
	public function __construct(String $hostname = null){
		if(empty($hostname)){
			if(empty($hostname = App::get('in')->getOption('hostname'))){
				$hostname = App::get('io')->ask(
					'What is the server hostname?', 'localhost'
				);
			}
		}
		$this->hostname = $hostname;
	}
	public function __toString(){
		return $this->hostname;
	}
}

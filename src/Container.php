<?php
namespace jpuck\dbdtp;

// http://stackoverflow.com/q/3715518/4233593
class Container {
	private $DbName = "This is the Database Name class.";
	private $objects;
	public function __get($class){
		if(isset($this->objects[$class])){
			return $this->objects[$class];
		}
		return $this->objects[$class] = new $class();
	}
}

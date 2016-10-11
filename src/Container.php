<?php
namespace jpuck\dbdtp;
use Exception;

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

	protected static $registry = [];
	public static function bind($key, $value){
		static::$registry[$key] = $value;
	}
	public static function get($key){
		if(!array_key_exists($key, static::$registry)){
// 			throw new Exception("No $key is bound in the container.");
			return null;
		}
		return static::$registry[$key];
	}
}

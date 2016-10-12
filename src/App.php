<?php
namespace jpuck\dbdtp;
use Exception;

// http://stackoverflow.com/q/3715518/4233593
class App {
	static private $DbName = "This is the Database Name class.";
	static private $objects;
	public function __get($class){
		if(isset(static::$objects[$class])){
			return static::$objects[$class];
		}
		return static::$objects[$class] = new $class();
	}

	static protected $registry = [];
	static public function bind($key, $value){
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

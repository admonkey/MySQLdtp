<?php
namespace jpuck\dbdtp;
use Exception;

// http://stackoverflow.com/q/3715518/4233593
class App {
	static protected $registry = [];
	static public function bind($key, $value){
		static::$registry[$key] = $value;
	}
	public static function get($key){
		if(!array_key_exists($key, static::$registry)){
			static::$registry[$key] = new $key;
		}
		return static::$registry[$key];
	}
}

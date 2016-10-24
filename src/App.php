<?php
namespace jpuck\qdbp;

// http://stackoverflow.com/q/3715518/4233593
class App {
	static protected $registry = [];
	static public function bind($key, $value){
		static::$registry[$key] = $value;
		return $value;
	}
	public static function get($key){
		if(!array_key_exists($key, static::$registry)){
			if(!class_exists($key)){
				if(!class_exists(__NAMESPACE__."\\$key")){
					return null;
				}
				$class = __NAMESPACE__."\\$key";
			}
			$class = $class ?? $key;

			// dynamically create instance
			static::$registry[$key] = new $class;

			// set short alias if namespaced
			if($key === $class){
				// http://stackoverflow.com/a/27457689/4233593
				if($name = substr(strrchr($key, '\\'), 1)){
					static::$registry[$name] =& static::$registry[$key];
				}
			}
		}
		return static::$registry[$key];
	}
}

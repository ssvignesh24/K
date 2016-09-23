<?php
	
	class Cookie{
		public static function get($name, $def = false){
			if(isset($_COOKIE[$name]))
				return $_COOKIE[$name];
			else return $def;
		}

		public static function set($name, $value, $t = 86400){
			setcookie($name, $value, time() + $t);
			return true;
		}

		public static function has($name){	
			return isset($_COOKIE[$name]);
		}
	}
	
?>
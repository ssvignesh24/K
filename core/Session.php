<?php
	
	class Session{
		public static function get($name, $def = false){
			if(isset($_SESSION[$name]))
				return $_SESSION[$name];
			else return $def;
		}

		public static function set($name, $value){
			$_SESSION[$name] = $value;
			return true;
		}

		public static function has($name){	
			return isset($_SESSION[$name]);
		}

		public static function destory(){
			$_SESSION = array();
			session_destroy();
		}

		public static function print_all(){
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
		}
	}
	
?>
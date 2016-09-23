<?php

	class EmptySQLResult{
		public function each($args = false){
			return false;
		}

		public function is_empty(){
			return true;
		}

		public function is_not_empty(){
			return false;
		}

		public function count(){
			return 0;
		}

		public function to_json($p = false){
			return 0;
		}
	}
?>
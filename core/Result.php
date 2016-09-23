<?php
	
	class SQLResult extends Engine{

		private $result, $table, $joins;
		public function __construct($result, $table, $joins = false){
			$this->result = $result;
			$this->table = $table;
			if($joins){
				$this->joins = $joins;
			}
		}

		public function count(){
			return count($this->result);
		}

		public function is_empty(){
			if(count($this->result) > 0)
				return false;
			else
				return true;
		}

		public function is_not_empty(){
			return !$this->is_empty();
		}

		public function to_json($process = false){
			$r = $this->result;
			if(is_callable($process)){
				if(is_array($r)){
					foreach ($r as $value) {
						$process($value);
					}
				}else
					$process($r);
				}
			if($r)
				return json_encode($r);
			else return 0;
		}

		public function where($condition, $data){
			if(!is_array($data))
				$data = array($data);
			$q = "SELECT *,".$this->table.".created_at as p_created_at FROM `".$this->table."`";
			foreach ($this->joins as $join) {
				$pices = explode(":", $join);
				if(count($pices) > 1){
					$j = $pices[0];
					$q .= " INNER JOIN `$j` on ".$this->table.".".$pices[1]." = $j.".$pices[0]."_id ";
				}else{
					$q .= " INNER JOIN `$join` on ".$this->table.".".$join."_id = $join.".$join."_id ";
				}
			}
			$q .= " WHERE ".$condition;
			$result = Engine::prepare_and_execute($q, $data);
			$this->result = $result;
			return $this;
		}

		public function __get($name){
			if(isset($this->result->$name))
				return $this->result->$name;
			else
				return null;
		}

		public function showError(){
			throw new Exception();
		}

		public function increment($column){
			$q = "UPDATE `".$this->table."` SET $column = $column + 1 WHERE `id` in (";
		 	if(is_array($this->result)){
				$ar = array();
				foreach ($this->result as $value) array_push($ar, $value->id);
				$q .= implode(",", $ar);
			}else
				$q .= $this->result->id;
			$q .= ")";
			Engine::prepare_and_execute($q);
			return $value->$column + 1;
    	}

    	public function decrement($column, $less_than_zero = false){
    		if($less_than_zero){
    			if($value->$column <= 0)
    				return false;
    		}
			$q = "UPDATE `".$this->table."` SET $column = $column - 1 WHERE `id` in (";
		 	if(is_array($this->result)){
				$ar = array();
				foreach ($this->result as $value) array_push($ar, $value->id);
				$q .= implode(",", $ar);
			}else
				$q .= $this->result->id;
			$q .= ")";
			Engine::prepare_and_execute($q);
			return $value->$column - 1;
    	}

		public function update($array){
			$q = "UPDATE `".$this->table."` SET ";
			foreach ($array as $key => $value) $q .= " ".$key." = ?,";
			$q[strlen($q) - 1 ] = " ";
			$q .= "WHERE `id` in (";

			if(is_array($this->result)){
				$ar = array();
				foreach ($this->result as $value) array_push($ar, $value->id);
				$q .= implode(",", $ar);
			}else
				$q .= $this->result->id;
			$q .= ")";
			Engine::prepare_and_execute($q,array_values($array));
			return $this;
		}

		public function each($func){
			if(is_array($this->result)){
				foreach ($this->result as $value) {
					$func($value);
				}
			}else
				$func($this->result);
		}
	}
	
?>
<?php
 
	class Admin{
		public $content, $table_name, $partial;
		public function handle($route){
			$r = explode("/", $route);	
			if(!isset($r[1]) && $r[0] == "admin"){

			}else{
				if(isset(Database::$schema[$r[1]])){
					if(count($r) == 2 || (count($r) == 2 || $r[2] == "" )){
						$this->content = $this->fetch_data($r[1]);
						$this->table_name = $r[1];
						$this->partial = "table";
					}elseif(count($r) == 3){
						if($r[2] == "new"){
							$this->table_name = $r[1];
							$this->partial = "new";
						}elseif($r[2] == "create"){
							$this->create_new();
						}elseif($r[2] == "delete"){
							$this->delete(filter_input(INPUT_GET, 'rid', FILTER_VALIDATE_INT),$r[1]);
						}elseif($r[2] == "edit"){
							$this->table_name = $r[1];
							$this->partial = "edit";
							$this->content = $this->edit(filter_input(INPUT_GET, 'rid', FILTER_VALIDATE_INT),$r[1]);
						}elseif($r[2] == "update"){
							$this->update($r[1]);
						}elseif($r[2] == "truncate"){
							$this->truncate($r[1]);
						}
					}
				
				}
			}
			
			include 'views/Admin/layout.php';
		}

		private function truncate($table){
			$q = 'TRUNCATE `'.$table.'`';
			$this->execute($q);
			header("Location:/admin/".$table);
		}
		private function update($table){
			$d = $_POST;
			$rid = $d['_rid'];
			unset($d['_rid']);
			$q = "UPDATE `$table` SET ";
			$s = array_keys(Database::$schema[$table], "boolen");
			foreach ($s as $b) {
				if(!isset($d[$b])){
					$d[$b] = 0;
				}elseif($d[$b] == "on")
					$d[$b] = 1;
			}
			$fields = array_keys($d);

			$f = "";
			foreach ($fields as $field) {
				$f .= ", $field = ? ";
			}
			$f = substr($f, 1);
			$q .= $f." WHERE id = ?";
			
			$data = array_values($d);
			array_push($data, $rid);

			$this->execute($q, $data);
			header("Location:/admin/".$table);

		}
		private function delete($rid,$table){
			$q = "DELETE FROM `$table` WHERE id = ?";
			$this->execute($q,array($rid));
			header("Location:/admin/".$table);
		}

		private function edit($rid, $table){

			$q = "SELECT * FROM `$table` WHERE id = ?";



			$data = $this->execute($q, array($rid));
			if(count($data) == 1)
				$data = $data[0];
			if($data->id == $rid)
				return $data;

		}
		private function create_new(){
			$d = $_POST;

			$table = $d['_table'];
			unset($d['_table']);
			$index = array_keys($d, "on");
			foreach ($index as $k) {
				if(Database::$schema[$table][$k] == "boolen"){
					$d[$k] = "1";
				}
			}
			$d['created_at'] = $this->now();
			$q = "INSERT INTO `".$table."`(";
			$f = implode(",",array_keys($d));
			$f = str_replace("_table,", "", $f);
			$q .= $f.") VALUES (";
			$ques = str_repeat(",?", count($d));
			$ques = substr($ques, 1);
			$q .= $ques.")";
			$data = array_values($d);
			$this->execute_and_get_id($q, $data);
			header("Location:/admin/".$table);
		}

		private function fetch_data($table){
			$data = $this->execute("SELECT * FROM `$table`");
			if(count($data) > 0)
				return $data;
		}

		protected static function db_connect(){
			$db = new PDO("mysql:host=localhost", Database::USERNAME, Database::PASSWORD);
	     	$db->query('USE '.Database::DATABASE);
	     	return $db;
		}

	    protected static function handle_db_error($message){
	      var_dump($message);
	      throw new Exception($message[2]);
	    }

	    public static function now(){
	      return  date("Y-m-d H:i:s");
	    }

	    protected function execute($query, $data = array()){
	      $db = Admin::db_connect();
	      $db->query('USE '.Database::DATABASE);
	      $stmt = $db->prepare($query);
	      $stmt->execute($data);
	      if($stmt->errorInfo()[1] != NULL) throw new Exception($stmt->errorInfo()[2],1);
	      else{
	        return $stmt->fetchAll(PDO::FETCH_CLASS);
	      } 
	    }


	    public function execute_and_get_id($query, $data = array()){
	      $db = Admin::db_connect();
	      $db->query('USE '.Database::DATABASE);
	      $stmt = $db->prepare($query);
	      $stmt->execute($data);
	      if($stmt->errorInfo()[1] != NULL) throw new Exception($stmt->errorInfo()[2]."FFFFF",1);
	      $stmt = $db->prepare("SELECT @@IDENTITY");
	      $stmt->execute();
	      if($stmt->errorInfo()[1] == NULL){
	        $row_ =  $stmt->fetch(PDO::FETCH_ASSOC);
	        return ($row_["@@IDENTITY"]);
	      }
	      else throw new Exception($stmt->errorInfo()[2]."EEEEE",1);
	    }
	}

?>
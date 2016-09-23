<?php

	class Engine{

    protected $post, $get, $join;

    public function __construct(){
      $this->post = $_POST;
      $this->get = $_GET;
      $this->join = array();
    }
		protected static function db_connect(){
			$db = new PDO("mysql:host=localhost", Database::USERNAME, Database::PASSWORD);
      $db->query('USE '.Database::DATABASE);
      return $db;
		}

    protected function rand_($data){
      $sum = 0;
      for($i=0;$i<count($data);$i++)
        $sum += (int) $data[$i];
      return $sum;
    }

    public static function join($data){
      if(!is_array($data))
        $data = array($data);
      return new SQLResult(false, strtolower(get_called_class()), $data);
    }

    protected static function handle_db_error($message){
      var_dump($message);
      throw new Exception($message);
    }

    public static function now(){
      return  date("Y-m-d H:i:s");
    }

    public static function execute($query, $data = array()){
      $db = Engine::db_connect();
      $db->query('USE '.Database::DATABASE);
      $stmt = $db->prepare($query);
      $stmt->execute($data);
      if($stmt->errorInfo()[1] != NULL) throw new Exception($stmt->errorInfo()[2],1);
      else{
        return $stmt->fetchAll(PDO::FETCH_CLASS);
      } 
    }

    protected static function prepare_and_execute($query, $data = false){
      $db = Engine::db_connect();
      $stmt = $db->prepare($query);
      ($data)? $stmt->execute($data) : $stmt->execute();
      if($stmt->errorInfo()[1] != NULL) Engine::handle_db_error($stmt->errorInfo()[2]);
      else{
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        if(count($result) == 1)
          return $result[0];
        else return $result;
      }
    }

    public function execute_and_get_id($query, $data = array()){
      $db = Engine::db_connect();
      $db->query('USE '.Database::DATABASE);
      $stmt = $db->prepare($query);
      $stmt->execute($data);
      if($stmt->errorInfo()[1] != NULL) throw new Exception($stmt->errorInfo()[2],1);
      $stmt = $db->prepare("SELECT @@IDENTITY");
      $stmt->execute();
      if($stmt->errorInfo()[1] == NULL){
        $row_ =  $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row_["@@IDENTITY"]);
      }
      else throw new Exception($stmt->errorInfo()[2],1);
    }


    static public function insert($data = false){
      if($data && is_array($data)){
        $data['created_at'] = Engine::now();
        $table = strtolower(get_called_class());
        $q = "INSERT INTO `$table`(".implode(",",array_keys($data)).") VALUES(";
        $fill = implode(",", str_split(str_repeat("?", count($data)))).")";
        $q .= $fill;
        $db = Engine::db_connect();
        $stmt = $db->prepare($q);
        $stmt->execute(array_values($data));
        if($stmt->errorInfo()[1] != NULL){
          if($stmt->errorInfo()[1] == 1062){
            return -1;
          }
          else{
            Engine::handle_db_error($stmt->errorInfo());
            return false;
          }
        }
        else{
          $stmt = $db->prepare("SELECT @@IDENTITY");
          $stmt->execute();
          if($stmt->errorInfo()[1] == NULL){
            $row_ =  $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row_["@@IDENTITY"]);
          }
        }
      }
    } 

    static public function all(){
      $table = strtolower(get_called_class());
      $q = "SELECT * FROM `$table`";
      $result = Engine::prepare_and_execute($q);
      $obj = new SQLResult($result, get_called_class());
      return $obj;
    }   

    static public function find($condition, $values){
      if(!is_array($values))
        $values = array($values);
      $table = strtolower(get_called_class());
      $q = "SELECT * FROM `$table` WHERE $condition";
      $result = Engine::prepare_and_execute($q,$values);
      if(count($result) == 0){
        return new EmptySQLResult();
      }
      $obj = new SQLResult($result, get_called_class());
      return $obj;
    }

    static public function truncate(){
      $table = strtolower(get_called_class());
      $q = "TRUNCATE `$table`";
      Engine::prepare_and_execute($q);
    }

    static public function delete($condition, $data = false){
      $table = strtolower(get_called_class());
      $q = "DELETE FROM `$table` WHERE ";
      if(is_string($condition)){
        $q .= $condition;
        Engine::prepare_and_execute($q, $data);
      }elseif(is_int($condition)){
        $q .= " id = ?";
        Engine::prepare_and_execute($q, array($condition));
      }
    }
	}

?>
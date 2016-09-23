<?php
	
	class Validation{
		private $rules,$errors,$data,$error_fields;

		public function __construct($condition){
			$this->rules = $condition;
		}

		public static function test($array){
			$r = new static($array);
			return $r;
		}

		public static function getRequestData($method){
			$r = array();
			if($method == "GET"){
				foreach ($_GET as $key => $value){
					$r[$key] = $value;
				}
			}
			elseif($method == "POST"){
				foreach ($_POST as $key => $value){
					$r[$key] = $value;
				}
			}

			return $r;
		}

		public function getErrorFields(){
			return $this->error_fields;
		}
		
		public function validate($method){
			$errors = array();
			$data = array();
			$error_fields = array();
			$input = ($method == "GET")? INPUT_GET : INPUT_POST;
			foreach ($this->rules as $field => $condition) {
				if(is_string($condition))
					$rules = explode("|", $condition);
				elseif(is_array($condition))
					$rules = $condition;
				$flag = false;
				foreach ($rules as $rule) {
					$result = false;
					if(is_string($rule)){
						$r = explode(":", $rule);
						if($r[0] == "if"){
							if($method == "GET"){
								if(!isset($_GET[$field]))
									break;
							}elseif($method == "POST"){
								if(!isset($_POST[$field]))
									break;
							}
						}
						switch ($r[0]) {
							case "in":
								$vals = explode(",", $r[1]);
								foreach ($vals as $key => $value) 
									$vals[$key] = trim($value);
								$given = trim(filter_input($input, $field));
								if(array_search($given, $vals) !== false){
									$result = true;
								}else{
									$errors[$field."_".$r[0]] = "Invalid input  (not in list): '".filter_input($input, $field). "' for ".ucfirst($field);
									$result = false;
									array_push($error_fields, $field);
								}
								break;
							case 'minlength':
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									$v_flag = 1;
									foreach ($array as $v) {
										if($v == "" || $v == null || $v == false || strlen($v) < (int)$r[1]){
											$errors[$field."_".$r[0]] = "Invalid input (not met min-length): '".filter_input($input, $field). "' for ".ucfirst($field);
											$result = false;
											$v_flag = 0;
											break;
										}
									}
									if($v_flag){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$errors[$field."_".$r[0]] = "Invalid input  (not met min-length): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}else{
									if(strlen(filter_input($input, $field)) >= (int)$r[1])
										$result = true;
									else{
										$errors[$field."_".$r[0]] = "Invalid input  (not met min-length): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}
								break;

							case "required":
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									$v_flag = 1;
									foreach ($array as $v) {
										if($v == "" || $v == null || $v == false){
											$v_flag = 0;
											break;
										}
									}
									if($v_flag){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$errors[$field."_".$r[0]] = "Invalid input (empty): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}else{
									if(strlen(filter_input($input, $field)) > 0 || filter_input($input, $field) != ""){
										$result = true;
									}else{
										$errors[$field."_".$r[0]] = "Invalid input (empty): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}
								break;

							case "maxlength":
								if(strlen(filter_input($input, $field)) <= (int)$r[1])
									$result = true;
								else{
									$errors[$field."_".$r[0]] = "Invalid input (not met max-length): '".filter_input($input, $field). "' for ".ucfirst($field);
									$result = false;
									array_push($error_fields, $field);
								}
								break;

							case "int":
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									$v_flag = 1;
									foreach ($array as $v) {

										if(!is_int((int)$v)){
											$v_flag = 0;
											break;
										}
									}
									if($v_flag){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$errors[$field."_".$r[0]] = "Invalid input (not a number): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}else{
									if(filter_input($input, $field, FILTER_VALIDATE_INT) !== false)
										$result = true;
									else{
										$errors[$field."_".$r[0]] = "Invalid input (not a number): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}
								break;
							case "email":
								if(filter_input($input, $field, FILTER_VALIDATE_EMAIL))
									$result = true;
								else{
									$errors[$field."_".$r[0]] = "Invalid input (invalid email): '".filter_input($input, $field). "' for ".ucfirst($field);
									$result = false;
									array_push($error_fields, $field);
								}
								break;

							case "url":
								if(filter_input($input, $field, FILTER_VALIDATE_URL))
									$result = true;
								else{
									$errors[$field."_".$r[0]] = "Invalid input (invalid url): '".filter_input($input, $field). "' for ".ucfirst($field);
									$result = false;
									array_push($error_fields, $field);
								}
								break;	
							case "string":
								if($flag){
									$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
								}else{
									$data[$field] = filter_var(filter_input($input,$field) ,  FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
									$result = false;
								}
								break;

							case "boolean":
								$in = filter_input($input, $field);
								if($in){
									$data[$field] = 1;
								}elseif($in == "on" || $in == 1 || $in == "1" || $in == "yes"){
									$data[$field] = 1;
								}else{
									$data[$field] = 0;
								}
								$result = false;
								break;
							case 'mincount':
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									if(count($array) >= (int) $r[1]){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$result = false;
										array_push($error_fields, $field);
										$errors[$field."_".$r[0]] = "Invalid input (not met min-count): '".filter_input($input, $field). "' for ".ucfirst($field);
									}
								}
								break;
							case 'maxcount':
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									if(count($array) <= (int) $r[1]){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$result = false;
										array_push($error_fields, $field);
										$errors[$field."_".$r[0]] = "Invalid input (not met max-count): '".filter_input($input, $field). "' for ".ucfirst($field);
									}
								}
								break;
							case "array":
								$flag = true;
								$result = false;
								break;

							case "date":
								if($flag){
									$array = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									$v_flag = 1;
									foreach ($array as $v) {
										$d = DateTime::createFromFormat("Y-m-d", $v);
										
										if($d && $d->format("Y-m-d") == $v){
											continue;
										}else{
											$v_flag = 0;
											break;
										}
									}
									if($v_flag){
										$result = false;
										$data[$field] = filter_input($input, $field,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
									}else{
										$errors[$field."_".$r[0]] = "Invalid input (invalid date): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
									}
								}else{
									$d = DateTime::createFromFormat("Y-n-d", filter_input($input,$field));
	    							if($d && ($d->format("Y-n-d") == filter_input($input,$field) || $d->format("Y-m-d") == filter_input($input,$field) )){
	    								$result = true;
	    							}else{
	    								$errors[$field."_".$r[0]] = "Invalid input (invalid date): '".filter_input($input, $field). "' for ".ucfirst($field);
										$result = false;
										array_push($error_fields, $field);
	    							}
	    						}
								break;

						}
					}elseif(is_callable($rule)){
						$in = filter_input($input, $field);
						if($rule($in)){
							$result = true;
						}else{
							$errors[$field."_".$r[0]] = "Invalid input : '".filter_input($input, $field). "' for ".ucfirst($field);
							$result = false;
							array_push($error_fields, $field);
						}
					}
					if($result)
						$data[$field] = filter_input($input, $field);
 				}
			}

			if(count($errors) == 0){
				if($method == "GET"){
					foreach ($_GET as $key => $value){
						if(!isset($data[$key])){
							$data[$key] = $value;
						}
					}
				}
				elseif($method == "POST"){
					foreach ($_POST as $key => $value){
						if(!isset($data[$key])){
							$data[$key] = $value;
						}
					}
				}
				$this->data = $data;
				return true;
			}
			else{ 
				$this->errors = $errors;
				$this->error_fields = $error_fields;
				return false;
			}
		}

		public function getErrors(){
			return $this->errors;
		}

		public function getData(){
			return $this->data;
		}
	}

?>
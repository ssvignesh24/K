<?php
	
	class Route{

		private static $_ROUTE,$_VALIDATION;

		public static function get($pattern, $handle, $validation = false){
			$p = explode("/", $pattern);
			$p_count = count($p);
			if($p_count > 1 && ($p[0][0] == ":" || $p[1][0] == ":")){
				if($p_count == 2){
					if(!isset(Route::$_ROUTE['get_abst']))
						Route::$_ROUTE['get_abst'] = array();
					if($p[0][0] == ":" && $p[1][0] != ":"){
						Route::$_ROUTE['get_abst'][2][1][$p[1]] = $handle;
						Route::$_ROUTE['get_abst'][2][1]["2_key"] = $p[0];
						Route::$_ROUTE['get_abst'][2]["_key_for"] = 1;
					}elseif($p[0][0] != ":" && $p[1][0] == ":"){
						Route::$_ROUTE['get_abst'][2][2][$p[0]] = $handle;
						Route::$_ROUTE['get_abst'][2][2]["2_key".$p[0]] = $p[1];
						//Route::$_ROUTE['get_abst'][2][2]["2_key"] = $p[1];
						Route::$_ROUTE['get_abst'][2]["_key_for"] = 2;
					}
				}
			}elseif($pattern[0] == ":"){
				if(!isset(Route::$_ROUTE['get_abst']))
					Route::$_ROUTE['get_abst'] = array();
				Route::$_ROUTE['get_abst'][1] = $handle;
				Route::$_ROUTE['get_abst']["1_key"] = $p[0];
				if($validation) Route::$_VALIDATION['get_abst'][1] = $validation;
			}else{
				if(!isset(Route::$_ROUTE['get']))
					$_ROUTE['get'] = array();
				Route::$_ROUTE['get'][$pattern] = $handle;
				if($validation) Route::$_VALIDATION['get'][$pattern] = $validation;
			}
		}

		public static function post($pattern, $handle, $validation = false){
			if(!isset(Route::$_ROUTE['post']))
				$_ROUTE['post'] = array();
			Route::$_ROUTE['post'][$pattern] = $handle;
			if($validation) Route::$_VALIDATION['post'][$pattern] = $validation;
		}

		public static function url($pattern, $handle, $validation = false){
			if(!isset(Route::$_ROUTE['url']))
				$_ROUTE['url'] = array();
			Route::$_ROUTE['url'][$pattern] = $handle;
			if($validation) Route::$_VALIDATION['url'][$pattern] = $validation;
		}

		public static function route_of($url,$method){
			$r = array();
			$frags = false;
			$p = explode("/", $url);
			$p_count = count($p);
			$val = false;

			if(isset(Route::$_ROUTE['url'][$url])){
				$frags = explode("=>", Route::$_ROUTE['url'][$url]);
				if(isset(Route::$_VALIDATION['url'][$url]))
					$val = Route::$_VALIDATION['url'][$url];

			}elseif($method == "GET" && isset(Route::$_ROUTE['get'][$url])){
				$frags = explode("=>", Route::$_ROUTE['get'][$url]);
				if(isset(Route::$_VALIDATION['get'][$url]))
					$val = Route::$_VALIDATION['get'][$url];
			}elseif($method == "GET" && ($p_count == 1 && isset(Route::$_ROUTE['get_abst'][1]))){
				if(isset(Route::$_ROUTE['get_abst'][1]))
					$frags = explode("=>", Route::$_ROUTE['get_abst'][1]);
				if(isset(Route::$_VALIDATION['get_abst'][1]))
					$val = Route::$_VALIDATION['get_abst'][1];
			}elseif($method == "GET" && ($p_count == 2 && isset(Route::$_ROUTE['get_abst'][2]))){
				if(isset(Route::$_ROUTE['get_abst'][2][2][$p[0]])){
					if(isset(Route::$_ROUTE['get_abst'][2][2][$p[0]]))
						$frags = explode("=>", Route::$_ROUTE['get_abst'][2][2][$p[0]]);
					if(isset(Route::$_VALIDATION['get_abst'][2][2][$p[0]]))
							$val = Route::$_VALIDATION['get_abst'][2][2][$p[0]];
					Route::$_ROUTE['get_abst'][2]["_key_for"] = 2;
				}else{
					if(isset(Route::$_ROUTE['get_abst'][2][1][$p[1]])){
						if(isset(Route::$_ROUTE['get_abst'][2][1][$p[1]]))
							$frags = explode("=>", Route::$_ROUTE['get_abst'][2][1][$p[1]]);
						if(isset(Route::$_VALIDATION['get_abst'][2][1][$p[1]]))
							$val = Route::$_VALIDATION['get_abst'][2][1][$p[1]];
						Route::$_ROUTE['get_abst'][2]["_key_for"] = 1;
					}
				}
			}elseif($method == "GET" && isset(Route::$_ROUTE['get'][$url])){
				$frags = explode("=>", Route::$_ROUTE['get'][$url]);
			}elseif($method == "GET" && !isset(Route::$_ROUTE['get'][$url]) && isset(Route::$_ROUTE['get']["_default"])){
				$frags = explode("=>", Route::$_ROUTE['get']["_default"]);
			}
			elseif($method == "POST" && isset(Route::$_ROUTE['post'][$url])){
				$frags = explode("=>", Route::$_ROUTE['post'][$url]);
				if(isset(Route::$_VALIDATION['post'][$url]))
					$val = Route::$_VALIDATION['post'][$url];
			}
			
			try{
				if($frags){
					$r["controller"] = $frags[0]."Controller";
					$filter = explode(":", $frags[1]);
					$r["action"] = $filter[0];
					$r['filter'] = isset($filter[1]);

					if($val){
						if($val->validate($method)){
							$r['data'] = $val->getData();
							if($p_count == 1 && $method == "GET")
								$r['data'][substr(Route::$_ROUTE['get_abst']["1_key"], 1)] = $p[0];
						}
						else{
							$r['error'] = $val->getErrors();
							$r['error_fields'] = $val->getErrorFields();
						}
					}else{
						$r['data'] = Validation::getRequestData($method);
						if($p_count == 1)
							$r['data'][substr(Route::$_ROUTE['get_abst']["1_key"], 1)] = $p[0];
						elseif($p_count == 2){

							if(Route::$_ROUTE['get_abst'][2]["_key_for"] == 1)
								$r['data'][substr(Route::$_ROUTE['get_abst'][2][1]["2_key"], 1)] = $p[0];
							elseif(Route::$_ROUTE['get_abst'][2]["_key_for"] == 2)
								$r['data'][substr(Route::$_ROUTE['get_abst'][2][2]["2_key".$p[0]], 1)] = $p[1];
						}
					}
					unset($r['data']['route']);
					return $r;
				}else throw new Exception("Something went wrong");
			}catch(Exception $e){
				echo $e->getMessage();
				return false;
			}
		}
	}

?>

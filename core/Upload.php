<?php
	
	class Uploads{

		public static function moveImage($name, $target, $name_generate = false){
			if(isset($_FILES[$name])){
				$orgi_file_name = $_FILES[$name]["name"];
				$file_name = $orgi_file_name;
				$imageFileType = pathinfo($orgi_file_name,PATHINFO_EXTENSION);
				if($name_generate && is_callable($name_generate))
					$file_name = $name_generate($orgi_file_name).".".$imageFileType;
				elseif($name_generate)
					$file_name = $name_generate.".".$imageFileType;
				
				$check_image = getimagesize($_FILES[$name]["tmp_name"]);
				$uploadOk = false;
			    if($check_image !== false) {
			        $uploadOk = true;
			    } else {
			        return false;
			    }

			    if($uploadOk){
			    	$full_name = $target.basename($file_name);
			    	if(file_exists($full_name)){
			    		return -1;
			    	}else{
			    		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
						    return -2;
						}else{
							if (move_uploaded_file($_FILES[$name]["tmp_name"], $full_name)) {
						       return $full_name;
						    } else {
						       return -3;
						    }
						}
			    	}
			    }
			}else return 0;
		}

		public static function multipleImage($name, $target, $name_generate = false){

			if(isset($_FILES[$name])){
				$combined = array();
				$total = count($_FILES[$name]['name']);
				for($i = 0; $i < $total; $i++){
					$orgi_file_name = $_FILES[$name]["name"][$i];
					$file_name = $orgi_file_name;
					$imageFileType = pathinfo($orgi_file_name,PATHINFO_EXTENSION);
					if($name_generate && is_callable($name_generate))
						$file_name = $name_generate($orgi_file_name).".".$imageFileType;
					elseif($name_generate)
						$file_name = $name_generate.".".$imageFileType;
					
					$check_image = getimagesize($_FILES[$name]["tmp_name"][$i]);
					$uploadOk = false;
				    if($check_image !== false) {
				        $uploadOk = true;
				    } else {
				        return false;
				    }

				    if($uploadOk){
				    	$full_name = $target.basename($file_name);
				    	if(file_exists($full_name)){
				    		return -1;
				    	}else{
				    		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
							    return -2;
							}else{
								if (move_uploaded_file($_FILES[$name]["tmp_name"][$i], $full_name)) {
							       array_push($combined, $full_name);
							    } else {
							       return -3;
							    }
							}
				    	}
				    }
				}

				if(count($combined) > 0)
					return implode(";", $combined);
			}else return 0;
		}


		public static function moveDoc($name, $target, $name_generate = false){
			if(isset($_FILES[$name])){
				$orgi_file_name = $_FILES[$name]["name"];
				$file_name = $orgi_file_name;
				$docFileType = pathinfo($orgi_file_name,PATHINFO_EXTENSION);
				if($name_generate && is_callable($name_generate))
					$file_name = $name_generate($orgi_file_name).".".$docFileType;
			    $full_name = $target.basename($file_name);
		    	if(file_exists($full_name)){
		    		return -1;
		    	}else{
		    		if($docFileType != "doc" && $docFileType != "docx" && $docFileType != "pdf" && $docFileType != "rtf" ) {
					    return -2;
					}else{
						if (move_uploaded_file($_FILES[$name]["tmp_name"], $full_name)) {
					       return $full_name;
					    } else {
					       return -3;
					    }
					}
			   }
			}else return 0;
		}


		public static function moveFile($name, $target, $name_generate = false){
			if(isset($_FILES[$name])){
				$orgi_file_name = $_FILES[$name]["name"];
				$file_name = $orgi_file_name;
				$docFileType = pathinfo($orgi_file_name,PATHINFO_EXTENSION);
				if($name_generate && is_callable($name_generate))
					$file_name = $name_generate($orgi_file_name).".".$docFileType;
			    $full_name = $target.basename($file_name);
		    	if(file_exists($full_name)){
		    		return -1;
		    	}else{
					if (move_uploaded_file($_FILES[$name]["tmp_name"], $full_name)) {
				       return $full_name;
				    } else {
				       return -3;
				    }
			   }
			}else return 0;
		}


	}

?>
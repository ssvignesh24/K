<?php

	spl_autoload_register(function ($classname) {
		$classname_ = substr($classname, 0, -10);

    	if(file_exists("controller/".$classname_."_controller.php"))
    		include 'controller/'.$classname_."_controller.php";
    	elseif(file_exists("modal/".$classname.".php"))
    		include 'modal/'.$classname.".php";
    	elseif(file_exists("helpers/".$classname."_helper.php"))
    		include 'helpers/'.$classname."_helper.php";
    	else
    		throw new Exception("Can find file $classname:2");
	});
?>
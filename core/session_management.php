<?php
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        echo "ERROR IN SESSION";
        exit();
    }
    
    session_start(); 
    session_regenerate_id(true);
?>
<?php
session_start();
require_once("config.php");
require_once("functions.php");
require_once("user.php");

if( !isset( $_POST['front_office'])  && isset($_GET["front_office"])) { 
    $content = <<< EOT
    <div class="alert alert-success" role="alert">
        You just created a clerk account!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div>
    <meta http-equiv='refresh' content='7; url=./index.php' />
EOT;
    render_page($content, ""); // TODO: eventually add a side_content
} else {
    if(!isset($_POST["password"]) || !isset($_POST["front_office"])){
        // check for required fields
		get_error(1);	
    }
    elseif($_POST["password"] == "" || $_POST["front_office"] == ""){
        // check for required fields
		get_error(1);	
    }
    // Check the user is not currently logged
    elseif(!is_admin()){
        get_error(3);
    }
    // Eventually add a check for password strength
    else {
        // TODO: improve security my filter string mysqlirealscape should be enough for demo
        $password = $_POST["password"];
        $front_office = $_POST["front_office"];
        register($front_office, $password);    
	}
}
?>
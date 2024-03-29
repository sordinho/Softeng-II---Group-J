<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Register a new service into the DB
if ($_GET['action'] == "newService" && isset($_POST['newService'])) {
//    echo "<pre>".print_r($_POST,TRUE)."</pre>";

    $newServiceToAdd = $_POST["newService"];
    $res = add_new_service($newServiceToAdd);

    if ($res) {
        $content = <<< EOT
        <div class="alert alert-success" role="alert">
        Succesfully registered a new service!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
        </div>
        <meta http-equiv='refresh' content='3; url=./index.php' />
EOT;

    } else
        $content = <<< EOT
    <div class="alert alert-warning" role="alert">
        Error!!! Impossible to register the new service<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div> 
    <meta http-equiv='refresh' content='3; url=./index.php' />
EOT;

    render_page($content, '');
}
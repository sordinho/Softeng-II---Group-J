<?php
require_once('config.php');
require_once('functions.php');

if ($_GET['action'] == "newService") {
    echo "<pre>".print_r($_POST,TRUE)."</pre>";

    $newServiceToAdd = $_POST["newService"];

    $conn = connectMySQL();

    // Query for adding new service to service table
    $addService = "INSERT INTO Service(Name,Counter) VALUES (?,0)";
    $query = $conn->prepare($addService);
    $query ->bind_param('s', $newServiceToAdd);
    $res = $query->execute();

    $serviceID = $conn->insert_id;

    echo "Service id: " .$serviceID;

    //Now we need to bind the new service to the selected frontOffices



}
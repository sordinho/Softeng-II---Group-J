<?php
require_once("customer.php");
require_once("functions.php");

if($_GET['action'] == "generateTicket" && isset($_POST['service'])){
    // Call generate ticket function
    //$ticketinfo = get_a_new_ticket($serviceID (or $serviceName))
    // Following code is just to let the frontend work for now: TODO: remove when ready token generation
    $ticket_info["ticketN"] = 10;
    $ticket_info["service"] = "GUITest";
    // Easy way to avoid query
    $ticket_info = add_top($_POST['service']);
    $ticket_info["service"] = $_POST['service']; 

    //print_r($ticket_info);//Array ( [ID] => 10 [serviceID] => 1 [ticketN] => 7 [timestamp] => 2019-10-17 18:39:29 )
    customer_register_ticket($ticket_info);
    customer_register_timestamp($ticket_info['timestamp']);
    $content = '
    <div class="alert alert-success" role="alert">
        You got a ticket!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div> ';
    $content .= "<meta http-equiv='refresh' content='3; url=".PLATFORM_PATH."' />"; 
    render_page($content, '');
}
?>
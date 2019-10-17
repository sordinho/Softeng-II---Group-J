<?php
require_once("customer.php");
require_once("functions.php");

if($_GET['action'] == "generateTicket" && isset($_POST['service'])){
    // Call generate ticket function
    //$ticketinfo = get_a_new_ticket($serviceID (or $serviceName))
    // Following code is just to let the frontend work for now: TODO: remove when ready token generation
    $ticket_info["ticketN"] = 10;
    $ticket_info["service"] = "GUITest";
    customer_register_ticket($ticket_info);
    $content = '
    <div class="alert alert-success" role="alert">
        You got a ticket!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div> ';
    $content .= "<meta http-equiv='refresh' content='3; url=".PLATFORM_PATH."' />"; 
    render_page($content, '');
}
?>
<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Customer ticket handler
function customer_register_ticket($ticket_info){
    $_SESSION['ticketN'] = $ticket_info["ticketN"];
    $_SESSION['service'] = $ticket_info["service"];
}
function has_pending_ticket(){
    return isset($_SESSION['ticketN']) ? $_SESSION['ticketN'] : false;
}

function get_ticketn(){
    return $_SESSION['ticketN'];
}
function get_ticket(){
    $ticket_info["ticketN"] = $_SESSION['ticketN'];
    $ticket_info["service"] = $_SESSION['service'];
    return $ticket_info;
}
function get_distance_from_top(){
    $ticket_info = get_ticket();
    //TODO: Implement queue_distance_from_top
    //qdis = queue_distance_from_top($ticket_info);
    return qdis();
}

?>
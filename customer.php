<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');

/*
TODOS and ideas:
When customer click on a button and do a GET request on a page a session is set with the info about the generated ticket.
In this way we can refresh the page every X secs (or do an ajax request) to get info about scheduled remaining time.
Note that, when a ticket is generated and so an INSERT in the Queue table is done , the timestamp is automatically generated 
        so just retrieve and keep that info saved as well

        In addition we need an info of some sort like the actual number being served for that service to clear the session 
    after the customer requested is done.

*/
/* This files conains all the functions regardings to customer (active and passive) action */
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
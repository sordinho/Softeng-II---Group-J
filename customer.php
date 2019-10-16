<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Customer ticket handler
function customer_register_ticket($ticket_n){
    $_SESSION['ticketN'] = $ticket_n;
}
function has_pending_ticket($ticket_n){
    return isset($_SESSION['ticketN']) ? $_SESSION['ticketN'] : false;
    $_SESSION['ticketN'] = $ticket_n;
}

function get_ticket($ticket_n){

}
?>
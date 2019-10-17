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
    $_SESSION['serviceID'] = $ticket_info["serviceID"];
}
function customer_register_timestamp($timestamp){
    $_SESSION['timestamp'] = $timestamp;
}
function customer_get_timestamp($timestamp){
    return isset($_SESSION['timestamp']) ? $_SESSION['timestamp'] : false;
}
function has_pending_ticket(){
    //return true;//TODO: remove or comment after testing
    return isset($_SESSION['ticketN']) ? $_SESSION['ticketN'] : false;
}

function get_ticketn(){
    return $_SESSION['ticketN'];
}
function get_ticket(){
    $ticket_info["ticketN"] = $_SESSION['ticketN'];
    $ticket_info["service"] = $_SESSION['service'];
    $ticket_info["serviceID"] = $_SESSION['serviceID'];
    return $ticket_info;
}
function get_distance_from_top(){
    $ticket_info = get_ticket();
    //TODO: Implement queue_distance_from_top
    //qdis = queue_distance_from_top($ticket_info);
    return qdis();
}
function get_ticket_html(){
    $ticket_info = get_ticket();
    $format_ticket = sprintf("%03d", $ticket_info["ticketN"]);
    $format_cur_ticket = sprintf("%03d", $ticket_info["ticketN"]-3);//TODO:
    // Test format for GUI rappresentation
    $time = customer_get_timestamp($ticket_info); 
    $format_timestamp = strtotime($time);
    $format_date = timestamp_to_date($format_timestamp);
    $html_ticket = '
    <!-- Ticket HTML -->
    <div class="ticketContainer">
    <h1>YOUR TICKET </h1>
    <ul>
        <li class="f_row"> <span class="ticketNum" data="Your #ticket">'.$format_ticket.'</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">
            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
        </svg><span class="san" data="Currently served">'.$format_cur_ticket.'</span>
        </li>
        <li class="t_row"> <span class="date" data="Timestamp">'.$format_date["day"]." ".$format_date["month"].'</span><span class="boarding" data="">'.$format_date["time"].'</span></li>
        <li class="fo_row"> <span class="passenger" data="Choosen service:">'.$ticket_info["service"].'</span></li>
        <li class="fi_row"> 
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg"> 
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg"> 
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg"> 
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        </li>
    </ul>
    </div>';
    return $html_ticket;
}

?>
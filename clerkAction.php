<?php
require_once("user.php");
require_once("queue.php");
require_once("functions.php");

if($_GET['action'] == "nextTicket" && is_clerk()){
    // 1) delete from queue
    $ticket_info = clerk_get_cur_ticket();
    delete_ticket(get_serviceID(), $ticket_info['ticketN']);
    // 2) Increase counter for stats (Both in Service table and Authentication table)
    update_stats(get_serviceID());
    // 3)Get new ticket to serve
    $ticket_info = get_next(get_serviceID());
    // 4) Save in the session the new current info about ticket
    clerk_register_ticket($ticket_info);

    $content = '
    <div class="alert alert-success" role="alert">
        You got a new ticket to serve!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div> ';
        $content .= "<meta http-equiv='refresh' content='3; url=" . PLATFORM_PATH . "' />";
        render_page($content, '');
        
}
?>
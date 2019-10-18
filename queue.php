<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Queue ticket handler
function add_top($service_name){
            // Do an insert and get back the info about the generated number
            $mysqli = connectMySQL(); 
            $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) SELECT Service.ID,MAX(TicketNumber)+1  FROM Queue JOIN Service ON ServiceID=Service.ID WHERE Service.Name = ? GROUP BY ServiceID';
            $query = $mysqli->prepare($sql);
            //echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            $query->bind_param('s', $service_name);
            $res = $query->execute();
            $last_id = $mysqli->insert_id;
            //print($last_id);
            if(!$res){
                printf("Error message: %s\n", $mysqli->error);
                return false;
            }
            else{
                $query->close();
                //die();
                $sql = "SELECT * FROM Queue WHERE ID = '$last_id'";
                $ticket_info= array();
                if ($result = $mysqli->query($sql)) {
                    /* fetch object array */
                    $row = $result->fetch_object();
                    $ticket_info['ID'] = $row->ID;
                    $ticket_info['serviceID'] = $row->ServiceID;
                    $ticket_info['ticketN'] = $row->TicketNumber;
                    $ticket_info['timestamp'] = $row->Timestamp;
                    $result->close();
                    $mysqli->close();
                }
                return $ticket_info;
            }
    }
function get_bottom($service_name){
        $conn = connectMySQL(); 
        $sql = "SELECT MIN(TicketNumber) as TicketN FROM Queue JOIN Service ON ServiceID = Service.ID WHERE Service.Name = '$service_name'";
        if ($result = $conn->query($sql)) {
            /* fetch object array */
            $row = $result->fetch_object();
            $ticketN = $row->TicketN;
        
            $result->close();
            return $ticketN;
        } else{
            printf("Error message: %s\n", $conn->error);
        }
}

function get_next($serviceID) {

    if ($serviceID === 0) {
        /*
         * get the current minimum ticket number from the current most sized queue
         * in case of equal queue size, the query picks the ticket number of the serviceID queue
         * with the minimum timestamp i.e. higher waiting time
         */
        $conn = connectMySQL();
        $sql = "select s.service service, s.ticket num, timestamp, MAX(s.count) count from (select ServiceID service, COUNT(*) count, MIN(TicketNumber) ticket, MIN(timestamp) timestamp from queue GROUP BY ServiceID order by timestamp asc) s";
        /*
         * query alternative
         */
        // $sql = "select s.service service, s.ticket num, timestamp, MAX(s.count) count from (select ServiceID service, COUNT(*) count, MIN(TicketNumber) ticket from queue GROUP BY ServiceID) s where timestamp = (select min(timestamp) from queue)";
        $ticket = array();
        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                $ticket = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
        }

        /*
         * $ticket['num'] -> ticket number
         * $ticket['service'] -> serviceID
         * $ticket['count'] -> total number of people in serviceID queue
         * $ticket['timestamp'] -> timestamp of the ticket
         */
        return $ticket;
    }
    elseif ($serviceID !== -1) {
        $conn = connectMySQL();
        $sql = "select serviceID service, min(TicketNumber) num, timestamp, count(*) count from queue where ServiceID='$serviceID'";
        $ticket = array();
        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                $ticket = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
        }

        /*
         * $ticket['num'] -> ticket number
         * $ticket['service'] -> serviceID
         * $ticket['count'] -> total number of people in serviceID queue
         * $ticket['timestamp'] -> timestamp of the ticket
         */
        return $ticket;
    }


}

?>
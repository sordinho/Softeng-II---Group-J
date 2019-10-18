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
        $sql = "select ID, ServiceID, TicketNumber ticketN, Timestamp timestamp from Queue where TicketNumber in (select MIN(TicketNumber) from Queue group by ServiceID) group by id, ServiceID, TicketNumber, Timestamp order by Timestamp limit 1";
        $ticket_info = array();
        if ($result = $conn->query($sql)) {
            if ($result->num_rows === 1) {
                $ticket_info = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
        }
        return $ticket_info;
    }
    elseif ($serviceID !== -1) {
        /*
         * get the minimum numbered ticket from a given serviceID queue
         */
        $conn = connectMySQL();
        $sql = "select ID, ServiceID, TicketNumber ticketN, Timestamp timestamp from Queue where TicketNumber = (select MIN(TicketNumber) from Queue) and ServiceID='$serviceID'";
        $ticket_info = array();
        if ($result = $conn->query($sql)) {
            if ($result->num_rows === 1) {
                $ticket_info = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
        }
        return $ticket_info;
    }


}

function delete_ticket($serviceID) {
    /*
     * delete one ticket from the specified serviceID
     * if one row has been affected returns true
     * false instead
     */
    $conn = connectMySQL();
    $sql = "select id from Queue where TicketNumber = (select MIN(TicketNumber) from Queue where ServiceID='$serviceID') and ServiceID='$serviceID'";
    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            $id = $result->fetch_assoc();
            if ($result2 = $conn->query("delete from Queue where id='$id'")) {
                return ($result2->num_rows === 1);
            } else {
                printf("Error message: %s\n", $conn->error);
                return false;
            }
        }
        return false;
    } else {
        printf("Error message: %s\n", $conn->error);
        return false;
    }
}

function update_stats($serviceID) {
    /*
     * update both Authentication and Service table
     * return true if affected rows are equal to 1
     * false instead
     */
    $conn = connectMySQL();
    $sql1 = "update Authentication set Counter=Counter+1 where ServiceID='$serviceID'";
    $sql2 = "update Service set Counter=Counter+1 where ID='$serviceID'";
    if ($result1 = $conn->query($sql1) && $result2 = $conn->query($sql2)) {
        return ($result1->num_rows === 1 && $result1->num_rows === 1);
    } else {
        printf("Error message: %s\n", $conn->error);
        return false;
    }
}

function get_currently_served_ticket_by($service_name){
    return get_bottom($service_name);
}

?>
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

// Add a dummy ticket. This will be called when, for a given service, there are no more records in the Queue table.
function add_dummy_ticket($service_id){
    // Do an insert and get back the info about the generated number
    $mysqli = connectMySQL(); 
    $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) VALUES (?, 0)';
    $query = $mysqli->prepare($sql);
    //echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    $query->bind_param('d', $service_id);
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
// Not used for now
function get_bottom_ticket_by_id($service_id){
    $conn = connectMySQL(); 
    $service_id = intval($service_id);
    $sql = "SELECT ID, ServiceID, TicketNumber AS ticketN, Timestamp AS timestamp FROM Queue WHERE TicketNumber IN (select MIN(TicketNumber) FROM Queue WHERE ServiceID='$service_id') AND ServiceID='$service_id'";
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

function get_next($serviceID) {

    if ($serviceID == 0) {
        /*
         * get the current minimum ticket number from the current most sized queue
         * in case of equal queue size, the query picks the ticket number of the serviceID queue
         * with the minimum timestamp i.e. higher waiting time
         */

        /*
         *  select count(*) minimum from (select count(*) count from Queue group by ServiceID) s having minimum=min(s.count);
            select serviceID from Queue group by ServiceID having count(*)=2;
            select serviceID, min(ticketNumber) ticketN from Queue where serviceID in (select serviceID from Queue group by ServiceID having count(*)=2) group by serviceID;
            select id, serviceID, ticketNumber ticketN, timestamp from Queue where ticketNumber in (select min(ticketNumber) from Queue where serviceID in (select serviceID from Queue group by ServiceID having count(*)=2) group by serviceID) order by timestamp asc;
         */
        $conn = connectMySQL();
        /*
         * first it gets the maximum sized queue and stores the result into a variable
         */
        $query1 = "select count(*) maximum from (select count(*) count from Queue group by ServiceID) s having maximum=max(s.count);";
        $ticket_info = array();
        if ($result1 = $conn->query($query1)) {
            $row = $result1->fetch_object();
            $count = $row->maximum;
            /*
             * even if there's more than one queue the result is limited by 1 so I always get the ticketN
             * from the maximum sized queue ordered by timestamps
             * given the maximum count the query search the minimum numbered ticket
             * from a partial window that displays all the serviceID queues given $count
             * and in the end it limits the result at 1 to only get a single ticket
             */
            $query2 = "select id, serviceID, ticketNumber ticketN, timestamp from Queue where ticketNumber in (select min(ticketNumber) from Queue where serviceID in (select serviceID from Queue group by ServiceID having count(*)=$count) group by serviceID) order by timestamp asc limit 1;";
            if ($result2 = $conn->query($query2)) {
                if ($result2->num_rows === 1) {
                    $ticket_info = $result2->fetch_assoc();
                }
            } else {
                printf("Error message: %s\n", $conn->error);
                print("\n".$serviceID);
                die($query2);
            }
        } else {
            printf("Error message: %s\n", $conn->error);
            print("\n".$serviceID);
            die($query1);
        }

        return $ticket_info;
    }
    elseif ($serviceID != -1) {
        /*
         * get the minimum numbered ticket from a given serviceID queue
         */
        $conn = connectMySQL();
        $sql = "SELECT ID, ServiceID AS serviceID, TicketNumber ticketN, Timestamp AS timestamp from Queue where TicketNumber IN (SELECT MIN(TicketNumber) FROM Queue WHERE ServiceID=$serviceID) AND ServiceID=$serviceID";
        $ticket_info = array();
        if ($result = $conn->query($sql)) {
            if ($result->num_rows === 1) {
                $ticket_info = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
            print("\n".$serviceID);
            die($sql);
        }
        return $ticket_info;
    }

}

function delete_ticket($serviceID, $ticketN) {
    /*
     * delete one ticket for the specified serviceID, ticketnum
     * if one row has been affected returns true
     * false instead
     */
    $conn = connectMySQL();
    $service_id = intval($serviceID);
    $ticket_n = intval($ticketN);
    $sql = "DELETE FROM Queue WHERE TicketNumber = $ticket_n AND ServiceID=$service_id";
    if ($result = $conn->query($sql)) {
        return ($result->num_rows === 1);
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
    $serviceID = intval($serviceID);
    $sql1 = "update Authentication set Counter=Counter+1 where ServiceID=$serviceID";
    $sql2 = "update Service set Counter=Counter+1 where ID=$serviceID";
    $result1 = $conn->query($sql1);
    $result2 = $conn->query($sql2);

    if ($result1 && $result2) {
        return ($result1->num_rows === 1 && $result2->num_rows === 1);
    } else {
        printf("Error message: %s\n", $conn->error);
        return false;
    }
}
/*Get current queued ticket for a given service.
    return -1 on failure*/
function get_length_by_service_id($service_id){
    //SELECT COUNT(ID) FROM Queue WHERE ServiceID=3
    $conn = connectMySQL();
    $serviceID = intval($service_id);
    $sql = "SELECT COUNT(ID) as counter FROM Queue WHERE ServiceID=".$service_id;
    $queue = -1;
    if ($result = $conn->query($sql)) {
        if ($result->num_rows === 1) {
            $queue= $result->fetch_assoc();
        }
    } else {
        printf("Error message: %s\n", $conn->error);
        print("\n".$serviceID);
    }
    return $queue["counter"];
}

function get_totatal_service_num(){
    // Create connection
    $conn = connectMySQL();
    $side_content = '<p class="tally"></p>';
    $sql = "SELECT COUNT(*) as n FROM Service";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $side_content = '<p class="tally">'. $row["n"] .'</p>';
        $conn->close();
    }
    return $side_content;
}

function get_totatal_lenght(){
    $conn = connectMySQL();
    $paragraph_content = '<p class="tally"></p>';
    $sql = "SELECT COUNT(*) as n FROM Queue";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $paragraph_content = '<p class="tally">'. $row["n"] .'</p>';
        $conn->close();
    }
    return $paragraph_content;
}
function get_currently_served_ticket_by($service_name){
    return get_bottom($service_name);
}

?>
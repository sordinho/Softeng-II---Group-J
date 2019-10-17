<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Queue ticket handler
function add_top($service_name){

    // Do an insert
            $mysqli = connectMySQL(); 
            $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) SELECT ID,MAX(TicketNumber)+1  FROM Queue JOIN Service ON ServiceID=ID WHERE Service.Name = "$service_name" GROUP BY ServiceID';
            $query = $mysqli->prepare($sql);
            $res = $query->execute();
            if(!$res){
                printf("Error message: %s\n", $mysqli->error);
                return false;
            }
            else{
                $query->close();
                $mysqli->close();
                $url = PLATFORM_PATH;
                die( "<meta http-equiv='refresh' content='1; url=$url' />");
            }
    }
function get_bottom($service_name){
        $conn = connectMySQL(); 
        $sql = "SELECT MIN(TicketNumber) FROM Queue JOIN Service ON ServiceID = ID WHERE Service.Name = '$service_name'";
        $userinfo = array();
        if ($result = $conn->query($sql)) {
            /* fetch object array */
            while ($row = $result->fetch_object()) {
                $userinfo['usergroup'] = $row->Permission;
                $userinfo['front_office'] = $row->front_office;
                $userinfo['name'] = $row->Name;
            }
            $result->close();
            return $userinfo;
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
        // todo
    }


}

?>
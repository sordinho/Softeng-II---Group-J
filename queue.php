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
                    $ticket_info['service'] = $row->ServiceID;
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
        $sql = "SELECT MIN(TicketNumber) FROM Queue JOIN Service ON ServiceID = Service.ID WHERE Service.Name = '$service_name'";
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

?>
<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');


// Queue ticket handler
function add_top($service_name){

    // Do an insert
            $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) SELECT ID,MAX(TicketNumber)+1  FROM Queue JOIN Service ON ServiceID=ID WHERE Service.Name = "$service_name" GROUP BY ServiceID';
            $query = $mysqli->prepare($sql);
            $res = $query->execute();
            if(!$res){
                printf("Error message: %s\n", $mysqli->error);
                return $success;
            }
            else{
                $query->close();
                $mysqli->close();
                $username_enc = urlencode($username);
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
                $userinfo['username'] = $row->Username;
                $userinfo['name'] = $row->Name;
            }
            $result->close();
            return $userinfo;
        } else{
            printf("Error message: %s\n", $conn->error);
        }
}

?>
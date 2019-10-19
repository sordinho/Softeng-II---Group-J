<?php
use \PHPUnit\Framework\TestCase;
require_once '..\queue.php';

class TestQueue extends TestCase
{
    /*
     * Funzioni di supporto al testing
     */

    protected function setUp() {
        /*
        $mysqli = connectMySQL();
        $sql1 = 'INSERT INTO Queue(ServiceID, TicketNumber) VALUES (1, 0)';
        $sql2 = 'INSERT INTO Queue(ServiceID, TicketNumber) VALUES (2, 0)';

        $query1 = $mysqli->query($sql1);
        $query2 = $mysqli->query($sql2);
        $mysqli->close();
        */
    }


    /*
     *     if(!is_admin())
        return false;
    $conn = connectMySQL();
    // Query for adding new service to service table
    $addService = "INSERT INTO Service(Name,Counter) VALUES (?,0)";
    $query = $conn->prepare($addService);
    if(!$query)
        return false;
    $query->bind_param('s', $new_service_to_add);
    $res = $query->execute();
    return $res;
    }
 */


    function get_top($service_name){
        $conn = connectMySQL();
        $sql = "SELECT MAX(TicketNumber) as TicketN FROM Queue JOIN Service ON ServiceID = Service.ID WHERE Service.Name = '$service_name'";
        if ($result = $conn->query($sql)) {
            /* fetch object array */
            $row = $result->fetch_object();
            $ticketN = $row->TicketN;

            $result->close();
            return $ticketN;
        } else {
            printf("Error message: %s\n", $conn->error);
        }
    }

    /*
     * Funzioni di test
     */

    // this function is supposed to add a ticket
    function test_add_dummy_ticket() {
        $sql = "SELECT COUNT(*) AS Num FROM Queue WHERE ServiceID = 5 AND TicketNumber = 0";
        $conn = connectMySQL();

        if ($result = $conn->query($sql)) {
            $row = $result->fetch_object();
            $num_before = $row->Num;

            $result->close();
        } else {
            printf("Error message: %s\n", $conn->error);
        }

        //perform insertion by dummy ticket function
        add_dummy_ticket(5);
        $sql = "SELECT COUNT(*) AS Num FROM Queue WHERE ServiceID = 5 AND TicketNumber = 0";
        $conn = connectMySQL();

        if ($result = $conn->query($sql)) {
            $row = $result->fetch_object();
            $num_after = $row->Num;

            $result->close();
        } else {
            printf("Error message: %s\n", $conn->error);
        }

        $this->assertFalse($num_after == $num_before + 1, "TestQueue : test_add_dummmy_ticket insertion non performed correctly or not performed");
    }

    function test_add_top() {
        // perform insertion of one value
        add_dummy_ticket(6);
        // select for max

        // perform add top
        // select for max
        //ver
    }

    function tearDown(){

    }
}

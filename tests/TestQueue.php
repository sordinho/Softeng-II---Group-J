<?php
use \PHPUnit\Framework\TestCase;
require_once '..\queue.php';
require_once 'testConfig.php';
require_once 'testUtility.php';

class TestQueue extends TestCase
{
    /*
     * Funzioni di supporto al testing
     */

    protected function setUp():void {
        createTestDatabase();
    }

    public function get_top($service_name){
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
    public function test_add_dummy_ticket() {
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

        $this->assertTrue($num_after == $num_before + 1, "TestQueue : test_add_dummy_ticket insertion not performed correctly or not performed");
    }

    public function test_add_top() {
        // perform insertion of one value
        $chosen_service = "Packages";
        //packages = 1
        add_dummy_ticket(1);
        // select for max
        $max_before = $this->perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID ='{1}'"
        );

        add_top($chosen_service);

        $max_after = $this->perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID ='{1}'"
        );

        $this->assertTrue($max_after == $max_before + 1, "TestQueue: test_add_top not performed correctly or not performed");
    }

    public function test_get_bottom(){

    }

    protected function tearDown():void{
        dropTestDatabase();
    }
}

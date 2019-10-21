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

    protected function setUp():void {
        createTestDatabase();
    }

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

        $max_before = perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID ='{1}'"
        );


        $max_after = perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID ='{1}'"
        );
        printf("\n\n%d---->%d\n\n",$max_before,$max_after);
        $this->assertTrue($max_after == ($max_before + 1), "TestQueue: test_add_top not performed correctly or not performed");
    }

    public function test_get_bottom(){

        /*
         * The actual configuration in Queue table is:
         *
         * ----------------------------------------------------------
         * |ID  |ServiceID  |TicketNUmber   |Timestamp              |
         * ----------------------------------------------------------
         * |2   |2          |0              |2019-10-19 12:18:17    |
         * |61  |1          |0              |2019-10-19 16:31:49    |
         * |62  |1          |1              |2019-10-19 20:03:25    |
         * ----------------------------------------------------------
         */

        $service_name = "Packages";
        $service_ID = get_serviceID_by_service_name($service_name);
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES('{$service_ID}', 2)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES('{$service_ID}', 3)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES('{$service_ID}', 4)");

        /*
         * The actual configuration in Queue table is:
         *
         * ----------------------------------------------------------
         * |ID  |ServiceID  |TicketNUmber   |Timestamp              |
         * ----------------------------------------------------------
         * |2   |2          |0              |2019-10-19 12:18:17    |
         * |61  |1          |0              |2019-10-19 16:31:49    |
         * |62  |1          |1              |2019-10-19 20:03:25    |
         * |?   |**1**      |**2**          |?                      |<<<
         * |?   |**1**      |**3**          |?                      |<<<
         * |?   |**1**      |**4**          |?                      |<<<
         * ----------------------------------------------------------
         */

        $this->assertTrue(get_bottom($service_name) == 0, "TestQueue: test_get_bottom not performed correctly or not performed");

        //TODO: check if DELETE statement is to be added here
    }

    public function test_get_next() {
        //TEST CASE: $serviceID == 0
        //TEST CASE: $serviceID != -1 AND $serviceID != 0
        //TEST CASE: $serviceID == -1

    }

    protected function tearDown():void{
        dropTestDatabase();
    }
}

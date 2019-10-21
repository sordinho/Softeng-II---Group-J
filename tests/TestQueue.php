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
        $sql = "SELECT MAX(TicketNumber) as TicketN FROM Queue JOIN Service ON ServiceID = Service.ID WHERE Service.Name = $service_name";
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

   /* protected function setUp():void {
        createTestDatabase();
       /*
        * Configuration before every test
        *
        * ----------------------------------------------------------
        * |ID  |ServiceID  |TicketNUmber   |Timestamp              |
        * ----------------------------------------------------------
        * |2   |2          |0              |2019-10-19 12:18:17    |
        * |61  |1          |0              |2019-10-19 16:31:49    |
        * |62  |1          |1              |2019-10-19 20:03:25    |
        * ----------------------------------------------------------

    }*/
    public static function setUpBeforeClass(): void
    {
        createTestDatabase();
    }
    public static function tearDownAfterClass(): void
    {
        dropTestDatabase();
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

        $this->assertTrue($num_after == ($num_before + 1), "TestQueue : test_add_dummy_ticket insertion not performed correctly or not performed");
        $sql= "DELETE FROM queue WHERE ServiceID = 5;";
        perform_INSERT_or_DELETE($sql);
    }

    public function test_add_top() {
        // perform insertion of one value
        $chosen_service = "Packages";
        //packages = 1
        add_dummy_ticket(1);
        // select for max

        $max_before = perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID =1;"
        );

        add_top("Packages");
        $max_after = perform_SELECT_return_single_value(
            "SELECT MAX(TicketNumber) FROM Queue WHERE ServiceID =1;"
        );
       // printf("\n\n%d---->%d\n\n",$max_before,$max_after);
        $this->assertTrue($max_after == ($max_before + 1), "TestQueue: test_add_top not performed correctly or not performed");
        perform_INSERT_or_DELETE("DELETE FROM queue where ID > 62;");
    }

    public function test_get_bottom(){
        $service_name = "Packages";
        $service_ID = get_serviceID_by_service_name($service_name);
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES($service_ID, 2)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES($service_ID, 3)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES($service_ID, 4)");

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
        perform_INSERT_or_DELETE("DELETE FROM queue where ServiceID = 1 AND TicketNumber = 2;");
        perform_INSERT_or_DELETE("DELETE FROM queue where ServiceID = 1 AND TicketNumber = 3;");
        perform_INSERT_or_DELETE("DELETE FROM queue where ServiceID = 1 AND TicketNumber = 4;");
        //TODO: check if DELETE statement is to be added here
    }

    public function test_get_next() {
    //database configuration of queue table is given in setUp()
    //TEST CASE: $serviceID == 0
        //caso con code di lunghezza diversa => prende dalla coda più lunga il biglietto con numero più basso

        $ticket_info1 = get_next(0);
        $this->assertEquals(0,$ticket_info1['ticketN'], "TestQueue: test_get_next not performed correctly or not performed");
        $this->assertEquals(1,$ticket_info1['serviceID'], "TestQueue: test_get_next not performed correctly or not performed");
        //caso con code di lunghezza uguale => prende il timestamp minore

        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES(2, 1)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES(2, 2)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES(1, 1)");

        /*
         * The actual configuration in Queue table is:
         *
         * ----------------------------------------------------------
         * |ID  |ServiceID  |TicketNUmber   |Timestamp              |
         * ----------------------------------------------------------
         * |2   |2          |0              |2019-10-19 12:18:17    |
         * |61  |1          |0              |2019-10-19 16:31:49    |
         * |62  |1          |1              |2019-10-19 20:03:25    |
         * |?   |**2**      |**1**          |?                      |<<<
         * |?   |**2**      |**2**          |?                      |<<<
         * |?   |**1**      |**1**          |?                      |<<< the element with the latest timestamp
         * ----------------------------------------------------------
         */

        $ticket_info2 = get_next(0);
        $this->assertTrue($ticket_info2['ticketN'] == 0, "TestQueue: test_get_next not performed correctly or not performed");
        $this->assertTrue($ticket_info2['serviceID'] == 2, "TestQueue: test_get_next not performed correctly or not performed");

        //TEST CASE: $serviceID != -1 AND $serviceID != 0 => ritorna il biglietto con numero minore
        $ticket_info3 = get_next(1);

        $this->assertTrue($ticket_info3['ticketN'] == 0, "TestQueue: test_get_next not performed correctly or not performed");
        $this->assertTrue($ticket_info3['serviceID'] == 1, "TestQueue: test_get_next not performed correctly or not performed");

        $ticket_info4 = get_next(2);
        $this->assertTrue($ticket_info4['ticketN'] == 0, "TestQueue: test_get_next not performed correctly or not performed");
        $this->assertTrue($ticket_info4['serviceID'] == 2, "TestQueue: test_get_next not performed correctly or not performed");
        $conn = TestsConnectMySQL();
        $sql = "Select * from queue;";
        $res = $conn->query($sql);
        while( $row = $res->fetch_row()){
            echo"$row[0] $row[1] $row[2] $row[3] \n";
        }
        $res->close();
        perform_INSERT_or_DELETE("DELETE FROM queue where ID >62;");
    }

    public function test_delete_ticket() {
        //database configuration of queue table is given in setUp()

        $serviceID = 2;
        $ticketN = 0;

        //perform select before
        $count_before = perform_SELECT_return_single_value("SELECT COUNT(*) FROM Queue WHERE TicketNumber = $ticketN AND ServiceID =$serviceID");
        $this->assertEquals(1,$count_before, "Something went wrong with the database configuration. Expected >> 1");

        //perform delete
        delete_ticket($serviceID,$ticketN);

        //perform select to verify COUNT == 0
        $count_after = perform_SELECT_return_single_value("SELECT COUNT(*) FROM Queue WHERE TicketNumber = $ticketN AND ServiceID =$serviceID");
        $this->assertEquals(0,$count_after, "TestQueue: test_delete_ticket not performed correctly or not performed");
        add_dummy_ticket($serviceID);
    }

    public function test_get_length_by_service_id() {
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES(2, 1)");
        perform_INSERT_or_DELETE("INSERT INTO Queue(ServiceID, TicketNumber) VALUES(2, 2)");
        /*
         * The actual configuration in Queue table is:
         *
         * ----------------------------------------------------------
         * |ID  |ServiceID  |TicketNUmber   |Timestamp              |
         * ----------------------------------------------------------
         * |2   |2          |0              |2019-10-19 12:18:17    |
         * |61  |1          |0              |2019-10-19 16:31:49    |
         * |62  |1          |1              |2019-10-19 20:03:25    |
         * |?   |**2**      |**1**          |?                      |<<<
         * |?   |**2**      |**2**          |?                      |<<<
         * ----------------------------------------------------------
         */
        $lenght1 = get_length_by_service_id(1);
        $lenght2 = get_length_by_service_id(2);
        $this->assertTrue($lenght1 == 2, "TestQueue: test_get_lenght_by_service_id not performed correctly or not performed");
        $this->assertTrue($lenght2 == 3, "TestQueue: test_get_lenght_by_service_id not performed correctly or not performed");
        perform_INSERT_or_DELETE("DELETE FROM queue where ServiceID = 2 and TicketNumber = 1;");
        perform_INSERT_or_DELETE("DELETE FROM queue where ServiceID = 2 and TicketNumber = 2;");
    }

    public function test_get_total_service_num() {
        $num_services = get_total_service_num();
        $this->assertEquals('<p class="tally">2</p>',$num_services, "TestQueue: get_total_service_num not performed correctly or not performed");
    }

    public function test_get_total_lenght() {
        $this->assertEquals('<p class="tally">3</p>',get_total_lenght(), "TestQueue: test_get_total_lenght not performed correctly or not performed");
    }

    /*protected function tearDown():void{
        dropTestDatabase();
    }*/
}

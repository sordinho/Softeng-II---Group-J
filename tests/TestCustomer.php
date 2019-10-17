<?php
use \PHPUnit\Framework\TestCase;
require_once '..\customer.php';
//Vittorio
//todo: devo fare controlli per cross site scripting e code injection? se si bisogna implementare una sanitizeString e chiamarla su tutti gli input(io ho il codice se serve)
//todo: idem anche con sql injection

class TestCustomer extends TestCase
{
    public function test_customer_register_ticket_BOUNDARY(){
        //todo: eventually check null values, empty strings (?) and passing non Integer values in $ticket_info['ticketN']
        $this->assertEquals("","");
    }
    public function test_customer_register_ticket(){
        $ticket_info = array();
        $ticket_info['ticketN'] = 10;
        $ticket_info['service'] = 'test';
        $ticket_info['serviceID'] = 'T';
        customer_register_ticket($ticket_info);
        $this->assertEquals(10,$_SESSION['ticketN'],"TestCustomer: test_customer_register_ticket error in value of ticketN");
        $this->assertEquals('test',$_SESSION['service'],"TestCustomer: test_customer_register_ticket error in value of service");
    }
    //todo:test boundary conditions on customer_register_timestamp
    public function test_customer_register_timestamp(){
        $timestamp = mktime(11,00,00,10,17,2019);
        customer_register_timestamp($timestamp);
        $this->assertEquals($timestamp,$_SESSION['timestamp']);
    }
    //todo:test boundary conditions on customer_get_timestamp
    public function test_customer_get_timestamp(){
        unset($_SESSION['timestamp']);
        $this->assertEquals(false,customer_get_timestamp());
        $timestamp = mktime(11,00,00,10,17,2019);
        $_SESSION['timestamp'] = $timestamp;
        $this->assertEquals($timestamp,customer_get_timestamp());
    }

    public function test_has_pending_ticket(){
        unset($_SESSION['ticketN']);
        $this->assertEquals(false,has_pending_ticket(),"TestCustomer: test_has_pending_ticket error, session value should not be set");
        $_SESSION['ticketN'] = 100;
        $this->assertEquals(100,has_pending_ticket(),"TestCustomer: test_has_pending_ticket error, session value should be set");
    }
    public function test_get_ticketn(){

        $_SESSION['ticketN'] = 15;
        $this->assertEquals(15,get_ticketn(),"TestCustomer: test_get_ticketn error: wrong session[ticketn] value");
    }
    public function test_get_ticketn_boundary(){
        //todo: should I check what happens when calling this function after unsetting session[ticketN]?
        /*unset($_SESSION['ticketN']);
        $this->assertEquals("",get_ticketn());*/
        $this->assertEquals("","");
    }
    public function test_get_ticket(){
        $_SESSION['ticketN']=11;
        $_SESSION['service']='test';
        $res = get_ticket();
        $this->assertEquals(11,$res['ticketN'],"TestCustomer: test_get_ticket error: wrong returned [ticketn] value");
        $this->assertEquals('test',$res['service'],"TestCustomer: test_get_ticket error: wrong returned [service] value");
    }
    /*
    public function test_get_ticket_boundary(){
    todo:what if I unset the session values? Should I test this?
    }
     */


    //public function test_get_distance_from_top(){
        //todo
    //}

   /* public function test_get_ticket_html(){
        //todo when the method is complete
    }*/
}

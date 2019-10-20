<?php
use \PHPUnit\Framework\TestCase;
require_once '..\customer.php';

class TestCustomer extends TestCase
{
    public function test_customer_register_ticket_BOUNDARY(){
        $ticket_info = array();
        $this->assertFalse(customer_register_ticket($ticket_info),"TestCustomer: test_customer_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $ticket_info['ticketN'] = 10;
        $this->assertFalse((customer_register_ticket($ticket_info)),"TestCustomer: test_customer_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $ticket_info['service'] = "test";
        $this->assertFalse(customer_register_ticket($ticket_info),"TestCustomer: test_customer_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        unset($ticket_info['ticketN']);
        $ticket_info['serviceID'] = 1;
        $this->assertFalse(customer_register_ticket($ticket_info),"TestCustomer: test_customer_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $ticket_info['ticketN'] = "testWrong";
        $this->assertFalse(customer_register_ticket($ticket_info),"TestCustomer: test_customer_register_ticket_BOUNDARY it shouldn't be possible to set non numerical values for ticketN");
    }
    public function test_customer_register_ticket(){
        $ticket_info = array();
        $ticket_info['ticketN'] = 10;
        $ticket_info['service'] = 'test';
        $ticket_info['serviceID'] = 1;
        customer_register_ticket($ticket_info);
        $this->assertEquals(10,$_SESSION['ticketN'],"TestCustomer: test_customer_register_ticket error in value of ticketN");
        $this->assertEquals('test',$_SESSION['service'],"TestCustomer: test_customer_register_ticket error in value of service");
        $this->assertEquals(1,$_SESSION['serviceID'],"TestCustomer: test_customer_register_ticket error in value of serviceID");
        $this->assertTrue($_SESSION['pendingTicket'],"TestCustomer: test_customer_register_ticket error in value pendingTicket");
    }
    public function test_customer_register_timestamp(){
        $timestamp = mktime(11,00,00,10,17,2019);
        customer_register_timestamp($timestamp);
        $this->assertEquals($timestamp,$_SESSION['timestamp']);
    }
    public function test_customer_get_timestamp(){
        unset($_SESSION['timestamp']);
        $this->assertEquals(false,customer_get_timestamp());
        $timestamp = mktime(11,00,00,10,17,2019);
        $_SESSION['timestamp'] = $timestamp;
        $this->assertEquals($timestamp,customer_get_timestamp());
    }
    public function test_has_pending_ticket(){
        unset($_SESSION['pendingTicket']);
        $this->assertEquals(false,has_pending_ticket(),"TestCustomer: test_has_pending_ticket error, session value should not be set");
        $_SESSION['pendingTicket'] = true;
        $this->assertEquals(100,has_pending_ticket(),"TestCustomer: test_has_pending_ticket error, session value should be set");
    }
    public function test_get_ticketn(){
        $_SESSION['ticketN'] = 15;
        $this->assertEquals(15,get_ticketn(),"TestCustomer: test_get_ticketn error: wrong session[ticketn] value");
    }
    public function test_get_ticketn_BOUNDARY(){
        unset($_SESSION['ticketN']);
        $this->assertFalse(get_ticketn(),"TestCustomer: test_get_ticketn_BOUNDARY : wrong returned value");
    }
    public function test_get_ticket(){
        $_SESSION['ticketN']=11;
        $_SESSION['service']='test';
        $res = get_ticket();
        $this->assertEquals(11,$res['ticketN'],"TestCustomer: test_get_ticket error: wrong returned [ticketn] value");
        $this->assertEquals('test',$res['service'],"TestCustomer: test_get_ticket error: wrong returned [service] value");
    }
    public function test_get_distance_from_top(){
        //todo
        $_SESSION['ticketN']=1;
        $_SESSION['service']="test";
        $_SESSION['serviceID']=1;
        $dist = get_distance_from_top();
        $this->assertEquals(1,$dist);
        $_SESSION['ticketN'] = 0;
        $_SESSION['serviceID'] = 2;
        $dist = get_distance_from_top();
        $this->assertEquals(0,$dist);
    }

    /*public function test_get_ticket_html(){
        //todo when the method is complete
        $_SESSION['ticketN']=3;
        $_SESSION['service']="Accounts";
        $_SESSION['serviceID']=1;
        $timestamp = mktime(11,00,00,10,17,2019);
        $_SESSION['timestamp'] = $timestamp;
        $format_ticket = sprintf("%03d", 3);
        $format_cur_ticket = sprintf("%03d", get_currently_served_ticket_by('Accounts'));
        // Test format for GUI rappresentation
        $time = customer_get_timestamp();
        $format_timestamp = strtotime($time);
        $format_date = timestamp_to_date($format_timestamp);
        $res = get_ticket_html();
        $expected = '
    <!-- Ticket HTML -->
    <div class="ticketContainer">
    <h1>YOUR TICKET </h1>
    <ul>
        <li class="f_row"> <span class="ticketNum" data="Your #ticket">'.$format_ticket.'</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">
            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
        </svg><span class="san" data="Currently served">'.$format_cur_ticket.'</span>
        </li>
        <li class="t_row"> <span class="date" data="Timestamp">'.$format_date["day"]." ".$format_date["month"].'</span><span class="boarding" data="">'.$format_date["time"].'</span></li>
        <li class="fo_row"> <span class="passenger" data="Choosen service:">'.$ticket_info["service"].'</span></li>
        <li class="fi_row">
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        <svg class="barcode" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.834 11.549H1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h5.834a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM62.043 11.549h-4.168a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4.168a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM17 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM90.334 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM81.167 11.549h-2.724a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.724a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM51.875 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM42.167 11.549h-2.5a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM73.523 11.549H71.98a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1.543a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM33.667 11.549h-4a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM23.667 11.549h-1a1 1 0 0 0-1 1v66.236a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V12.549a1 1 0 0 0-1-1zM67.227 11.549h-.363c-.551 0-1 .448-1 1v66.236c0 .552.449 1 1 1h.363c.551 0 1-.448 1-1V12.549c0-.552-.45-1-1-1z"></path>
        </svg>
        </li>
    </ul>
    </div>';
    }*/
}

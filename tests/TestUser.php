<?php

require_once "..\user.php";
use PHPUnit\Framework\TestCase;

class TestUser extends TestCase
{
    public function test_is_logged(){
        $_SESSION['front_office']="test";
        $this->assertTrue(is_logged(),"TestUser : test_is_logged function is_logged should return true.");
        unset($_SESSION['front_office']);
        $this->assertFalse(is_logged(),"TestUser : test_is_logged function is_logged should return false.");
    }

    public function test_is_admin(){
        $_SESSION['usergroup'] = 'test';
        $this->assertFalse(is_admin(),"TestUser : test_is_admin function is_admin should have returned false, session[usergroup] is not Admin");
        $_SESSION['usergroup'] = 'Admin';
        $this->assertTrue(is_admin(),"TestUser : test_is_admin function is_admin should have returned true, session[usergroup] is Admin");
        unset($_SESSION['usergroup']);
        $this->assertFalse(is_admin(),"TestUser : test_is_admin function is_admin should have returned false, session[usergroup] is not set");
    }
    public function test_is_clerk(){
        $_SESSION['usergroup'] = 'test';
        $this->assertFalse(is_clerk(),"TestUser : test_is_admin function is_clerk should have returned false, session[usergroup] is not Clerk");
        $_SESSION['usergroup'] = 'Clerk';
        $this->assertTrue(is_clerk(),"TestUser : test_is_admin function is_clerk should have returned true, session[usergroup] is Clerk");
        unset($_SESSION['usergroup']);
        $this->assertFalse(is_clerk(),"TestUser : test_is_admin function is_clerk should have returned false, session[usergroup] is not set");
    }
    public function test_set_logged(){
        $front_office = "clerk";
        set_logged($front_office);
        $this->assertEquals("clerk",$_SESSION['front_office'],"TestUser : test_set_logged wrong value in session[front_office]");
    }
    public function test_set_logged_BOUNDARY(){
        $res = set_logged(null);
        $this->assertFalse($res,"TestUser : test_set_logged_BOUNDARY function set_logged should have returned false having null input.");
    }
    /*
//Memorizza nelle sessioni lo front_office
function set_front_office($front_office)
{
    $_SESSION['front_office'] = $front_office;
    return;
}
    public function test_set_front_office(){

    }
    */

    public function test_set_name(){
        $name = "test";
        set_name($name);
        $this->assertEquals("Test",$_SESSION['name'],"TestUser : test_set_name wrong value in session[name]");
        $name = "TEST";
        set_name($name);
        $this->assertEquals("TEST",$_SESSION['name'],"TestUser : test_set_name wrong value in session[name]");
        $name = "Test";
        set_name($name);
        $this->assertEquals("Test",$_SESSION['name'],"TestUser : test_set_name wrong value in session[name]");
        $name = "tEST";
        set_name($name);
        $this->assertEquals("TEST",$_SESSION['name'],"TestUser : test_set_name wrong value in session[name]");
    }

    public function test_set_name_BOUNDARY(){
        $res = set_name(null);
        $this->assertFalse($res,"TestUser : test_set_name_BOUNDARY function set_name should have returned false having null input");
    }
    public function test_set_serviceID(){
        $serviceID= 1;
        set_serviceID($serviceID);
        $this->assertEquals(1,$_SESSION['serviceID'],"TestUser : test_set_serviceID wrong value in session[serviceID]");
    }
    public function test_set_servideID_BOUNDARY(){
        $res = set_serviceID(null);
        $this->assertFalse($res,"TestUser : test_set_serviceID_BOUNDARY function set_serviceID should have returned false having null input");
    }
    public function test_set_usergroup(){
        $usergroup = "testval";
        set_usergroup($usergroup);
        $this->assertEquals($usergroup,$_SESSION['usergroup'],"TestUser : test_set_usergroup wrong value in session[usergroup]");
    }

    public function test_set_usergroup_BOUNDARY(){
        $res = set_usergroup(null);
        $this->assertFalse($res,"TestUser : test_set_usergroup_BOUNDARY function set_usergroup should have returned false having null input");
    }
    public function test_get_front_office(){
        $_SESSION['front_office'] = "test@input.it";
        $this->assertEquals("test@input.it",get_front_office(),"TestUser : test_get_front_office wrong returned value");
    }

    public function test_get_front_office_BOUNDARY(){
        $_SESSION['front_office']='';
        $this->assertEquals("",get_front_office(),"TestUser : test_get_front_office_BOUNDARY wrong returned value");
        $_SESSION['front_office'] = null;
        $this->assertEquals("",get_front_office(),"TestUser : test_get_front_office_BOUNDARY wrong returned value");
    }

    public function test_get_name(){
        $_SESSION['name'] = "test";
        $this->assertEquals("test",get_name(),"TestUser : test_get_name wrong returned value");
    }

    public function test_get_name_BOUNDARY(){
        $_SESSION['name'] = "";
        $this->assertEquals("",get_name(),"TestUser : test_get_name_BOUNDARY wrong returned value");
        $_SESSION['name'] = null;
        $this->assertEquals("",get_name(),"TestUser : test_get_name_BOUNDARY wrong returned value");
    }

    public function test_get_serviceID(){
        $_SESSION['serviceID'] = 1;
        $this->assertEquals(1,get_serviceID(),"TestUser : test_get_serviceID wrong returned value");
    }
    public function test_get_serviceID_BOUNDARY(){
        $_SESSION['serviceID'] = null;
        $this->assertEquals(false,get_serviceID(),"TestUser : test_get_serviceID_BOUNDARY wrong returned value");
    }

    public function test_get_usergroup(){
        $_SESSION['usergroup'] =  'test';
        $this->assertEquals("test",get_usergroup(),"TestUser : test_get_usergroup wrong returned value");
    }

    public function test_get_usergroup_BOUNDARY(){
        $_SESSION['usergroup'] = "";
        $this->assertEquals("", get_usergroup(),"TestUser : test_get_usergroup_BOUNDARY wrong returned value");
        $_SESSION['usergroup'] = null;
        $this->assertEquals("", get_usergroup(),"TestUser : test_get_usergroup_BOUNDARY wrong returned value");
    }

    public function test_clerk_register_ticket(){
        $ticket_info = array();
        $ticket_info['ticketN'] = 1;
        $ticket_info['serviceID']= 1;
        $timestamp = mktime(11,00,00,10,17,2019);
        $ticket_info['timestamp'] = $timestamp;
        clerk_register_ticket($ticket_info);
        $this->assertEquals(1,$_SESSION['ticketN'],"TestUser : test_clerk_register_ticket wrong value for session[ticketN]");
        $this->assertEquals(1,$_SESSION['serviceID'],"TestUser : test_clerk_register_ticket wrong value for session[serviceID]");
        $this->assertEquals($timestamp,$_SESSION['timestamp'],"TestUser : test_clerk_register_ticket wrong value for session[timestamp]");
    }

    public function test_clerk_register_ticket_BOUNDARY(){
        $ticket_info = array();
        $this->assertFalse(clerk_register_ticket($ticket_info),"TestUser: test_clerk_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $ticket_info['ticketN'] = 10;
        $this->assertFalse((clerk_register_ticket($ticket_info)),"TestUser: test_clerk_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $timestamp = mktime(11,00,00,10,17,2019);
        $ticket_info['timestamp'] = $timestamp;
        $this->assertFalse(clerk_register_ticket($ticket_info),"TestUser: test_clerk_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        unset($ticket_info['ticketN']);
        $ticket_info['serviceID'] = 1;
        $this->assertFalse(clerk_register_ticket($ticket_info),"TestUser: test_clerk_register_ticket_BOUNDARY it shouldn't be possible to set empty values");
        $ticket_info['ticketN'] = "testWrong";
        $this->assertFalse(clerk_register_ticket($ticket_info),"TestUser: test_clerk_register_ticket_BOUNDARY it shouldn't be possible to set non numerical values for ticketN");

    }
    public function test_clerk_get_our_ticket(){
        $_SESSION['ticketN'] = 1;
        $_SESSION['serviceID'] = 1;
        $timestamp = mktime(11,00,00,10,17,2019);
        $_SESSION['timestamp'] = $timestamp;
        $actual = clerk_get_cur_ticket();
        $this->assertEquals(1,$actual['ticketN'],"TestUser : test_clerk_get_our_ticket wrong returned value");
        $this->assertEquals(1,$actual['serviceID'],"TestUser : test_clerk_get_our_ticket wrong returned value");
        $this->assertEquals($timestamp,$actual['timestamp'],"TestUser : test_clerk_get_our_ticket wrong returned value");
    }
    public function test_get_clerk_content(){
        $_SESSION['usergroup'] = "notClerk";
        $this->assertFalse(get_clerk_content(),"TestUser : test_get_clerk_content should have returned false, session[usergroup] is not clerk");
        $expected = '
    <!-- The container  -->
    <div class="container">
        <div class="wrapper">
            <br/>
            <h1>Queue manager</h1>
            <p class="lead">Click on the button to mark the current customer as served and generate the next customer to call<br></p>
            <a class="btn btn-primary" href="./clerkAction.php?action=nextTicket"role="button">Next customer</a>
        </div>
    </div>';
        $_SESSION['usergroup'] = 'Clerk';
        $this->assertEquals($expected,get_clerk_content(),"TestUser : test_get_clerk_content wrong returned value");
    }

    public function test_get_clerk_side_content(){
        $_SESSION['usergroup'] = "notClerk";
        $this->assertFalse(get_clerk_side_content(),"TestUser : test_get_side_clerk_content should have returned false, session[usergroup] is not clerk");
        $_SESSION['usergroup'] = 'Clerk';
        $_SESSION['serviceID'] = 1;
        $_SESSION['ticketN'] = 1;
        $timestamp = mktime(11,00,00,10,17,2019);
        $_SESSION['timestamp'] = $timestamp;
        $res = '
            
            <section class="component-nstats">
                <div class="nstats">
                    <div class="networks">
                        <div class="network uptime">
                            <p class="title">Service no.</p>
                            <p class="tally">1</p>
                            <p class="unit">Packages</p>
                        </div>
                        
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        
                        <div class="network user">
                            <p class="title">Ticket no.</p>
                            <p class="tally">1</p>
                            <p class="unit">Serving</p>
                        </div>
                         <div class="ui-horizontal-lines"></div>
                    </div>
                </div>
            </section>
        ';
        $this->assertEquals($res,get_clerk_side_content(),"TestUser : test_get_clerk_side_content wrong returned value");
        $_SESSION['serviceID'] = 2;
        $res = '
            
            <section class="component-nstats">
                <div class="nstats">
                    <div class="networks">
                        <div class="network uptime">
                            <p class="title">Service no.</p>
                            <p class="tally">2</p>
                            <p class="unit">Accounts</p>
                        </div>
                        
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        
                        <div class="network user">
                            <p class="title">Ticket no.</p>
                            <p class="tally">1</p>
                            <p class="unit">Serving</p>
                        </div>
                         <div class="ui-horizontal-lines"></div>
                    </div>
                </div>
            </section>
        ';
        $this->assertEquals($res,get_clerk_side_content(),"TestUser : test_get_clerk_side_content wrong returned value");
        $_SESSION['serviceID'] = 0;
        $res = '
            
            <section class="component-nstats">
                <div class="nstats">
                    <div class="networks">
                        <div class="network uptime">
                            <p class="title">Service no.</p>
                            <p class="tally">0</p>
                            <p class="unit">Packages / Accounts</p>
                        </div>
                        
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        <div class="network actions">
                            <p class="title"></p>
                            <p class="tally"></p>
                            <p class="unit"></p>
                        </div>
                        
                        <div class="network user">
                            <p class="title">Ticket no.</p>
                            <p class="tally">1</p>
                            <p class="unit">Serving</p>
                        </div>
                         <div class="ui-horizontal-lines"></div>
                    </div>
                </div>
            </section>
        ';
        $this->assertEquals($res,get_clerk_side_content(),"TestUser : test_get_clerk_side_content wrong returned value");
    }

    public function test_get_admin_content(){
        $_SESSION['usergroup']='notAdmin';
        $this->assertFalse(get_admin_content(),"TestUser : test_get_admin_content function should have returned false");

        $_SESSION['usergroup']='Admin';
        $expected = '
    <!-- The container  -->
    <div class="container">
        <div class="wrapper">
            <br/>
            <h1>Register a new service</h1>
            <p class="lead">Insert the name and select to wich counter assign it.<br></p>
            <form action="./admin.php?action=newService" method="POST">
            <div class="form-group">
                <label for="exampleFormControlSelect2">New Service</label>
                <input name="newService" class="form-control" id="newService">                         
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        </div>
    </div>';
        $this->assertEquals($expected,get_admin_content(),"TestUser : test_get_admin_content wrong returned value");
    }
    /*function get_admin_side_content()
    {//todo
        $admin_side_content = get_side_content_as_html();
        return $admin_side_content;

    }*/
    public function test_is_email(){
        $email = "not an email";
        $res = is_email($email);
        $this->assertEquals(0,$res,"TestUser : test_is_email input value was not an email");

        $email = "test@email.com";
        $res = is_email($email);
        $this->assertEquals(1,$res,"TestUser : test_is_email input value was an email");
    }
}

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

/*// Memorizza nelle sessioni anche il nome dell'utente
function set_name($name)
{
    $_SESSION['name'] = ucfirst($name);
}*/
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
/*function get_usergroup()
{
    return isset($_SESSION['usergroup']) ? $_SESSION['usergroup'] : '';
}

function clerk_register_ticket($ticket_info){

    $_SESSION['ticketN'] = $ticket_info["ticketN"];
    //$_SESSION['service'] = $ticket_info["service"];
    $_SESSION['serviceID'] = $ticket_info["serviceID"];
    $_SESSION['timestamp'] = $ticket_info["timestamp"];
}

function clerk_get_cur_ticket(){
    $ticket_info["ticketN"] = $_SESSION['ticketN'];
    //$ticket_info["service"] = $_SESSION['service'];
    $ticket_info["serviceID"] = $_SESSION['serviceID'];
    $ticket_info["timestamp"] = $_SESSION['timestamp'];
    return $ticket_info;
}

function get_clerk_content()
{
    //<a class="btn btn-primary" href="#" role="button">Link</a>
    if (!is_clerk())
        return false;
    $content = '
    <!-- The container  -->
    <div class="container">
        <div class="wrapper">
            <br/>
            <h1>Queue manager</h1>
            <p class="lead">Click on the button to mark the current customer as served and generate the next customer to call<br></p>
            <a class="btn btn-primary" href="./clerkAction.php?action=nextTicket"role="button">Next customer</a>
        </div>
    </div>';
    return $content;
}

function get_clerk_side_content()
{
    $clerk_side_content = '

            <section class="component-nstats">
                <div class="nstats">
                    <div class="networks">
                        <div class="network uptime">
                            <p class="title">Front office no.</p>
                            <p class="tally">' . $_SESSION['serviceID'] . '</p>
                            <p class="unit">Packages / Accounts</p>
                        </div>


                    </div>


                </div>
</section>
        ';

    return $clerk_side_content;
}

function get_admin_content()
{
    if (!is_admin())
        return false;

    $content = '
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

    return $content;
}

function get_admin_side_content()
{
    $admin_side_content = get_side_content_as_html();
    return $admin_side_content;

}

// Check functions
function is_email($email)
{
    $regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/';
    return preg_match($regex, $email);
}
*/
}

<?php
require_once('config.php');
require_once('queue.php');
require_once('functions.php');

/* 
There are 2 type if user(a customer is not considered as *user* in this context):
- Admin: Can do some unique actions like adding a new Service
- Clerk: There are 2 types of clerk. You need to check the serviceID with get serviceID()=> 
            sID <- serviceID()
            if sID = 0) The operator should serve the shortest queue.
            if sID != 0) The operator always should elaborate the same service (pick always from the queue record with serviceID=sID)
        Note: use get_serviceID() to check if that frontoffice should check the shortest queue to pick up the ticket to serve (every time a new ticket should be dequeued). 
        */

function get_user_data($front_office)
{
    $conn = connectMySQL();
    $sql = "SELECT * FROM Authentication WHERE FrontOffice= '{$front_office}'";
    $userinfo = array();
    if ($result = $conn->query($sql)) {
        /* fetch object array */
        while ($row = $result->fetch_object()) {
            $userinfo['usergroup'] = $row->Permission;
            $userinfo['front_office'] = $row->FrontOffice;
            $userinfo['serviceID'] = $row->ServiceID;
        }
        $result->close();
        return $userinfo;
    } else {
        printf("Error message: %s\n", $conn->error);
    }
}

function user_login($post_data)
{
    $front_office = $post_data["front_office"];
    $password = $post_data["password"];
    $success = false;
    /*if(!is_email($front_office)){
        return $success;
    }*/

    $mysqli = new mysqli(DBAddr, DBUser, DBPassword, DBName);
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        return $success;
    }
    // Here using prepared statement to avoid SQLi
    $query = $mysqli->prepare("SELECT Password FROM Authentication WHERE FrontOffice = ?");
    $query->bind_param('s', $front_office);
    $res = $query->execute();
    if (!$res) {
        printf("Error message: %s\n", $mysqli->error);
        return $success;
    }

    $query->store_result();
    $query->bind_result($pass);
    // In case of success there should be just 1 user for a given front_office (front_office is also a primary key for its table)
    if ($query->num_rows != 1) {
        return $success;
    }
    $query->fetch();
    if (password_verify($password, $pass)) {
        // If here login was successful (hash was verified)
        $success = true;
        //set_logged();
    }
    $query->close();
    $mysqli->close();
    // TODO check
    $userinfo = get_user_data($front_office);
    set_usergroup($userinfo['usergroup']);
    set_name($userinfo['front_office']);             // todo controllare questa riga, era: set_name($userinfo['name']); ----- ma name non esiste
    set_front_office($userinfo['front_office']);
    if ($userinfo["serviceID"] != -1){// admin has a -1 value on serviceID field
        // Get also the first ticket that need to be served
        $ticket_info = get_bottom_ticket_by_id($userinfo["serviceID"]);
        clerk_register_ticket($ticket_info);
        set_serviceID($userinfo["serviceID"]);
        //print_r(clerk_get_cur_ticket());
    }
    return $success;
}

function register($front_office, $password)
{
    $success = false;
    // TODO: eventually edit with has_permission() (related to admin capabilities to add clerk)
    if (!is_admin()) {
        //die("You are already registered and logged in");
        return $success;
    }
    $mysqli = new mysqli(DBAddr, DBUser, DBPassword, DBName);
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        return $success;
    }

    $options = [
        //'salt' => custom_function_for_salt(), //eventually define a function to generate a  salt
        'cost' => 12 // default is 10, better have a little more security
    ];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT, $options);
    // In a real scenario it should be a nice practice to generate an activation code and let the user confirm that value (ex. with a link)
    //$activation_code = rand(100, 999).rand(100,999).rand(100,999);
    $sql = "INSERT INTO Authentication (FrontOffice, password, Counter, ServiceID) VALUES (?, ?, 0, 0)";
    $query = $mysqli->prepare($sql);
    $query->bind_param('ss', $front_office, $hashed_password);
    $res = $query->execute();
    if (!$res) {
        printf("Error message: %s\n", $mysqli->error);
        return $success;
    } else {
        $query->close();
        $mysqli->close();
        $front_office_enc = urlencode($front_office);
        $url = PLATFORM_PATH;
        $url .= "register.php?front_office=" . $front_office_enc;
        die("<meta http-equiv='refresh' content='1; url=$url' />");
    }
}

/***********************************
 * Check login status
 ***********************************/

// Login check functions
function is_logged()
{
    return isset($_SESSION['front_office']);
}

function is_admin()
{//TODO: test
    return isset($_SESSION['usergroup']) ? $_SESSION['usergroup'] == "Admin" : false;
}

function is_clerk()
{//TODO: test
    return isset($_SESSION['usergroup']) ? $_SESSION['usergroup'] == "Clerk" : false;
}

// set login
function set_logged($front_office)
{
    $_SESSION['front_office'] = $front_office;
    return;
}

//Memorizza nelle sessioni lo front_office
function set_front_office($front_office)
{
    $_SESSION['front_office'] = $front_office;
    return;
}

// Memorizza nelle sessioni anche il nome dell'utente
function set_name($name)
{
    $_SESSION['name'] = ucfirst($name);
}

function set_serviceID($serviceID)
{
    $_SESSION['serviceID'] = $serviceID;
}

function set_usergroup($usergroup)
{
    $_SESSION['usergroup'] = $usergroup;
}

//Restituisce la mail memorizzata nelle sessioni o stringa vuota se non settata
function get_front_office()
{
    return isset($_SESSION['front_office']) ? $_SESSION['front_office'] : '';
}

function get_name()
{
    return isset($_SESSION['name']) ? $_SESSION['name'] : '';
}

function get_serviceID()
{
    return isset($_SESSION['serviceID']) ? $_SESSION['serviceID'] : false;
}

function get_usergroup()
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

?>
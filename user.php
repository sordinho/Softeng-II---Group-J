<?php
require_once('config.php');

function connectMySQL() {
        $mysqli = new mysqli(DBAddr, DBUser, DBPassword, DBName);
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_errno);
            exit();
        }
        return $mysqli;
    }

function get_user_data($username){
        $conn = connectMySQL(); 
        $sql = "SELECT * FROM User WHERE Username = '{$username}'";
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

function user_login($post_data) {
        $username= $post_data["username"];
        $password = $post_data["password"]; 
		$success = false;
		/*if(!is_email($username)){
			return $success;
		}*/
		
		$mysqli = new mysqli(DBAddr, DBUser, DBPassword, DBName);
		if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", mysqli_connect_error());
			return $success;
		}
		// Here using prepared statement to avoid SQLi
		$query = $mysqli->prepare("SELECT Password FROM User WHERE username = ?");
		$query->bind_param('s', $username);
		$res = $query->execute();
		if(!$res){
            printf("Error message: %s\n", $mysqli->error);
			return $success;
		}

		$query->store_result();
		$query->bind_result($pass);
		// In case of success there should be just 1 user for a given username (username is also a primary key for its table)
		if ($query->num_rows != 1) {
			return $success;
		}
		$query->fetch();
		if(password_verify($password, $pass)){
			// If here login was successful (hash was verified)
			$success = true;
			//set_logged();
		}
		$query->close();
        $mysqli->close();
        // TODO check
        $userinfo = get_user_data($username);
        set_usergroup($userinfo['usergroup']);
        set_name($userinfo['name']);
        set_username($userinfo['username']);
		return $success;
	}

    function register($username, $password) {
        $success = false;
        // TODO: eventually edit with has_permission() (related to admin capabilities to add clerk)
		if(is_logged()){
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
		$sql = "INSERT INTO User (username, password) VALUES (?, ?)";
		$query = $mysqli->prepare($sql);
		$query->bind_param('ss', $username,$hashed_password);
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
            $url .= "register.php?username=".$username_enc;
			die( "<meta http-equiv='refresh' content='1; url=$url' />");
		}
    }
    
    /***********************************
        Check login status 
    ***********************************/
    
    // Login check functions 
    function is_logged(){
        return isset($_SESSION['username']);
    }
    function is_admin(){//TODO: test
        return isset($_SESSION['usergroup']) ? $_SESSION['usergroup']=="Admin" : false;
    }
    function is_clerk(){//TODO: test
        return isset($_SESSION['usergroup']) ? $_SESSION['usergroup']=="Clerk" : false;
    }
    // set login
    function set_logged($username){
        $_SESSION['username'] = $username;
        return;
    }

    //Memorizza nelle sessioni lo username
    function set_username($username){
        $_SESSION['username'] = $username;
        return;
    }
    
    // Memorizza nelle sessioni anche il nome dell'utente
    function set_name($name){
        $_SESSION['name'] = ucfirst($name);
        return;
    }
    function set_usergroup($usergroup){
        $_SESSION['usergroup'] = $usergroup;
        return;
    }
     //Restituisce la mail memorizzata nelle sessioni o stringa vuota se non settata 
    function get_username(){
        return isset($_SESSION['username']) ? $_SESSION['username'] : '';
    }

    function get_name(){
        return isset($_SESSION['name']) ? $_SESSION['name'] : '';
    }   
        
    function get_usergroup(){
        return isset($_SESSION['usergroup']) ? $_SESSION['usergroup'] : '';
    }


    // Check functions
    function is_email($email){
		$regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/';
		return preg_match($regex, $email);
    }
?>
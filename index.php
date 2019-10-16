<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');

# Define the content of the page
$content = <<< EOT
      <!-- The container  -->
      <div class="container">
        <div class="wrapper">
          <br/>
          <h1>Starter template</h1>
          <p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>
          Test
        </div>
      </div>
EOT;

# Define the logic
# If user not authenticated and a post requested with front_office was sent => try login
if(!isset($_SESSION['front_office']) && isset($_POST['front_office'])) {
  echo "Trying to login... wait";
  
  if( user_login($_POST) ) {
      // If login was successful then get info about user and redirect according to the role
      $usergroup = get_usergroup();  
      $redirect_path= "index.php";
      /* NOTE :
      It's better to handle this by rendering different menus (already done) and by checking permission in the functions
      that are run: ex. Clicking on Admin action submenu entry will execute adminFunction1(). Inside this check for is_admin() otherwise return false;
      if($usergroup == "Clerk"){
        $redirect_path= "clerk.php";
      }
      elseif($usergroup == "Admin"){
        $redirect_path= "admin.php";
      }else{
        $redirect_path= "index.php";
      }*/
      $content .= '
      <div class="alert alert-success" role="alert">
        Successful login!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
      </div> ';
      $content .= "<meta http-equiv='refresh' content='2; url=".PLATFORM_PATH.$redirect_path."' />"; // Redirect to home
  } else {
      $content .= '
      <div class="alert alert-success" role="alert">
        Failed login!<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
      </div> ';
      $content .= "<meta http-equiv='refresh' content='3; url=".PLATFORM_PATH."' />"; 
  }
}

$side_content = '
<section class="component-nstats">
    <div class="nstats">
    <div class="networks">
        <div class="network uptime">
        <p class="title">Service</p>
        <p class="tally">3</p>
        <p class="unit">Services</p>
        </div>

        <div class="network smartobject">
        <p class="title">Waiting</p>
        <p class="tally">35</p>
        <p class="unit">in queue</p>
        </div>      

        <div class="network actions">
        <p class="title">Estimated</p>
        <p class="tally">2</p>
        <p class="unit">Waiting time</p>
        </div>

        <div class="network user">
        <p class="title">Total</p>
        <p class="tally">156</p>
        <p class="unit">People</p>
        </div>
        
        <div class="ui-horizontal-lines"></div>
    </div>

    <div class="viralability">
        <div class="stats-wrapper">
        <p class="tally">Stats Infos</p>
        <p class="unit">(realtime)</p>
        </div>

    </div>
    </div>
</section>
';

// Finally render the full page: 1)centered (main) content and 2)the side one (on the right)
render_page($content, $side_content);
?>
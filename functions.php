<?php
/**				    	functions.php					        **/
/**		File contenente le funzioni ausiliarie utilizzate		**/

require_once("config.php");


function render_page($content, $side_content){
    // Render header section
    include 'header.php';

    echo "<main role='main' class='container'>
            <div class='row'>
            <div id='content' class='col-md-8'>";
    echo $content;
    echo "</div>";
    // Render right side 
    echo "<div id='setContent' class='d-none col-md-4 .d-md-block d-lg-block d-xl-block sidemenu'>";
            //Info d start here
            echo $side_content;
            //Info  ends here
            // Close pending div and main
            echo "</div></div></main>";
    // Render footer
    include 'footer.php';
}

function get_error($nerr){
    $messages = array(
        1 => 'Insert all the fields.',
        2 => 'Already logged in.',
        3 => 'You cant do such operation (missing permission).',
        4 => '.',
        5 => '.'
    );
    $content = '
    <div class="alert alert-warning" role="alert">'.
        $messages[$nerr].
        '<br>In a few seconds you will be redirected to home. If you are in a hurry <a href="./index.php" class="alert-link">just click here!</a>
    </div>
    <meta http-equiv=\'refresh\' content=\'7; url=./index.php\' />
';
    render_page($content, ""); // TODO: eventually add a side_content
}

function connectMySQL() {
    $mysqli = new mysqli(DBAddr, DBUser, DBPassword, DBName);
    /* check connection */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_errno);
        exit();
    }
    return $mysqli;
}

function timestamp_to_date($timestamp){
    $date['month'] = date("M", $timestamp);
    $date['day']= date("d", $timestamp);
    $date['time'] = date("H:i", $timestamp);
    return $date;
}
?>
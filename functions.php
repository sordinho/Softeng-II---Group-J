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

function get_services_as_list_html(){
    $content = "";
    $conn = connectMySQL();
    $sql = "SELECT * FROM Service";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //Adding option to the select input element based on services stored in the DB
            $content .= '<option value="' . $row["Name"] . '">' . $row["Name"] . '</option>';
        }
        $conn->close();
    }
    else{
        $content .= '<option value="Error">No current service were found</option>';
    }
    return $content;
}

// Should return the number of the actual service (count su tabella services)
function get_service_num(){

}

//function that loads datas from DB and build HTML string to generate the side panel
function get_side_content_as_html(){
    $side_content = '
        <section class="component-nstats">
            <div class="nstats">
            <div class="networks">
                <div class="network uptime">
                <p class="title">Service</p>';

        // Create connection
    $conn = connectMySQL();

    $sql = "SELECT COUNT(*) as n FROM service";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $side_content .= '<p class="tally">'. $row["n"] .'</p>';
        $conn->close();
    }


    $side_content.='
        <p class="unit">Services</p>
        </div>

        <div class="network smartobject">
        <p class="title">Waiting</p>';


    $conn = connectMySQL();

    $sql = "SELECT COUNT(*) as n FROM queue";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $side_content .= '<p class="tally">'. $row["n"] .'</p>';
        $conn->close();
    }


    $side_content.='
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
        </section>';
    return $side_content;
}



?>
<?php
use PHPUnit\Framework\TestCase;
require_once '..\functions.php';
require_once "testUtility.php";
class TestFunction extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        createTestDatabase();
    }
    public static function tearDownAfterClass(): void
    {
        dropTestDatabase();
    }
    public function test_connectMySql(){
        $myqli = connectMySQL();
        $this->assertNotNull($myqli,"TestFunction: test_connectMySQL returned value should not be null");
    }

    public function test_get_services_as_list_html(){
        /*
         * Service
         *  Name       |   ID      |   Counter     |
         * 'Packages'        1           8
         * 'Accounts'        2           0
         * */
        $expected = '<option value="Packages">Packages</option><option value="Accounts">Accounts</option>';
        $actual = get_services_as_list_html();
        $this->assertEquals($expected,$actual,"TestFunction: test_get_services_as_list_html wrong returned value");
        $sql = "DELETE FROM Service;";
        perform_INSERT_or_DELETE($sql);
        $expected = '<option value="Error">No current services were found</option>';
        $actual=get_services_as_list_html();
        $this->assertEquals($expected,$actual,"TestFunction: test_get_services_as_list_html wrong returned value");
        $sql = "INSERT INTO `Service` (`Name`, `ID`, `Counter`) VALUES
('Packages', 1, 8),
('Accounts', 2, 0);";
        perform_INSERT_or_DELETE($sql);
    }
    public function test_timestamp_to_date(){
        $timestamp = mktime(11,00,00,10,17,2019);
        $res = timestamp_to_date($timestamp);
        $this->assertEquals('Oct',$res['month'],"TestFunction: test_timestamp_to_date wrong month value");
        $this->assertEquals(17,$res['day'],"TestFunction: test_timestamp_to_date wrong day value");
        $this->assertEquals("11:00",$res['time'],"TestFunction: test_timestamp_to_date wrong hour:min value");
    }

    /*
function get_side_content_as_html(){
    $tot_lenght_html_paragraph = get_total_lenght();
    $tot_num_of_service = get_total_service_num();
    $side_content = '
        <section class="component-nstats">
            <div class="nstats">
            <div class="networks">
                <div class="network uptime">
                <p class="title">Service</p>'.$tot_num_of_service
                .'<p class="unit">Services</p>
            </div>
        <div class="network smartobject">
        <p class="title">Waiting</p>'.$tot_lenght_html_paragraph
    .'
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
}*/
    public function test_get_side_content_as_html(){
        $tot_lenght_html_paragraph ='<p class="tally">3</p>';


        $tot_num_of_service ='<p class="tally">2</p>';

        $side_content = '
        <section class="component-nstats">
            <div class="nstats">
            <div class="networks">
                <div class="network uptime">
                <p class="title">Service</p>' . $tot_num_of_service
            . '<p class="unit">Services</p>
            </div>
        <div class="network smartobject">
        <p class="title">Waiting</p>
        <p class="tally">X</p>
        <p class="unit">in queue</p>
        </div>      

        <div class="network actions">
            <p class="title">Estimated</p>
            <p class="tally">X</p>
            <p class="unit">Waiting time</p>
        </div>

        <div class="network user">
            <p class="title">Total</p>
            <p class="tally">X</p>
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
        $actual = get_side_content_as_html();
        $this->assertEquals($side_content,$actual,"TestFunction: test_get_side_content_as_html wrong returned value");
    }
}

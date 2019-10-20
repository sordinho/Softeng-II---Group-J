<?php
use PHPUnit\Framework\TestCase;
require_once '..\functions.php';

class TestFunction extends TestCase
{

    /*public function test_connectMySql(){
        $myqli = connectMySQL();
        $this->assertNotNull($myqli,"TestFunction: test_connectMySQL returned value should not be null");
    }*/
    public function test_timestamp_to_date(){
        $timestamp = mktime(11,00,00,10,17,2019);
        $res = timestamp_to_date($timestamp);
        $this->assertEquals('Oct',$res['month'],"TestFunction: test_timestamp_to_date wrong month value");
        $this->assertEquals(17,$res['day'],"TestFunction: test_timestamp_to_date wrong day value");
        $this->assertEquals("11:00",$res['time'],"TestFunction: test_timestamp_to_date wrong hour:min value");
    }
}

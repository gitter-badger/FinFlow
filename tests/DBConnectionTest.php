<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

include_once 'init-tests.php';


class DBConnectionTest extends PHPUnit_Framework_TestCase{

    public function testDBConnection(){
        global $fndb; $this->assertTrue($fndb->connected, "Oops! Could not connect to database on " . FN_DB_HOST);
    }

}
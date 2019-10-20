<?php

require_once 'testConfig.php';


/**
 * To be used in setUp() and tearDown() methods for tests
 *
 * @param null
 *
 * @return null
 */
function createTestDatabase() {
    $filename = 'testSofteng2.sql';

    $mysqli = new mysqli(DBAddrTest, DBUserTest, DBPasswordTest, DBNameTest);

    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_errno);
    }

    $templine = '';
    $lines = file($filename);

    foreach ($lines as $line) {
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        $templine .= $line;

        if (substr(trim($line), -1, 1) == ';') {
            $mysqli->query($templine) or print('Error performing query \'< strong>' . $templine . '\': ' . $mysqli->error() . '<br /><br />');

            $templine = '';
        }
    }

    $mysqli->close();
    return;
}

/**
 * To be used in setUp() and tearDown() methods for tests
 *
 * @param null
 *
 * @return null
 */
function dropTestDatabase() {
    $mysqli = TestsConnectMySQL();

    if ($mysqli->query("DROP DATABASE" . DBNameTest) === TRUE)
        echo "Database" . DBNameTest . " dropped successfully";
    else
        echo "Unable to drop database " . DBNameTest . ". ERROR: " . $mysqli->error;
}

/**
 * @param the query you want to perform (SELECT **aggregate function** ... >> NO GROUP BY
 *
 * @return value returned by the query
 */
function perform_SELECT_return_single_value($sql) {
    $conn = TestsConnectMySQL();
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_array();
        $value = $row[0];

        $result->close();
        return  $value;
    } else {
        printf("Error message: %s\n", $conn->error);
    }
}

/**
 * Creates a connection to the DB for testing
 * Connection is to be closed by the caller function
 *
 * @param null
 *
 * @return mysqli connection
 */
function TestsConnectMySQL() {
    $mysqli = new mysqli(DBAddrTest, DBUserTest, DBPasswordTest, DBNameTest);
    /* check connection */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_errno);
        exit();
    }
    return $mysqli;
}
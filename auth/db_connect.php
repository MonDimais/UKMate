<?php
function db_connect() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'test-ukmate';

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    return $conn;
}
?>
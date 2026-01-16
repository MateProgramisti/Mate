<?php
define(constant_name: 'DB_HOST', value: 'localhost');
define(constant_name: 'DB_USER', value: 'root');
define(constant_name: 'DB_PASS', value: '');
define(constant_name: 'DB_NAME', value: 'mshop');

function getDBConnection(): mysqli {
    $conn = new mysqli(hostname: DB_HOST, username: DB_USER, password: DB_PASS, database: DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset(charset: "utf8mb4");
    return $conn;
}

session_start();
?>

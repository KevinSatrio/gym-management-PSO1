<?php

$dbServername = getenv('DB_HOST') ?: '127.0.0.1';
$dbUsername = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'loginsystem';

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

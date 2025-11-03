<?php
// Database connection using MySQLi procedural style
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'we_transfer_user';
$DB_PASS = getenv('DB_PASS') ?: 'change_me';
$DB_NAME = getenv('DB_NAME') ?: 'we_transfer';

$mysqli = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$mysqli) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($mysqli, 'utf8mb4');
?>

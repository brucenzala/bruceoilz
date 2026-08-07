<?php
// db.php - Production-ready database connection

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'bruceoilz';

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Log error securely in production rather than displaying raw credentials
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error. Please try again later.");
}
?>
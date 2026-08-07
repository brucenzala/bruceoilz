<?php
$host = getenv('DB_HOST') ?: 'gateway01.us-east-1.prod.aws.tidbcloud.com';
$user = getenv('DB_USER') ?: '...';
$pass = getenv('DB_PASS') ?: '...';
$db   = getenv('DB_NAME') ?: 'bruceoilz';
$port = getenv('DB_PORT') ?: 4000;

$conn = mysqli_init();
if (getenv('DB_SSL') === 'true') {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
}

if (!@mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
    die("Database Connection Error: " . mysqli_connect_error());
}

// DO NOT ECHO ANYTHING HERE!
?>
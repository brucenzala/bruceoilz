<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'bruceoilz';
$port = getenv('DB_PORT') ?: 3306;

// Turn off MySQLi exception throwing so connection failures do not crash the page
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();

if (getenv('DB_SSL') === 'true' || getenv('DB_HOST')) {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    @mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
} else {
    @mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port);
}

if (!$conn || mysqli_connect_errno()) {
    $conn = false;
}
?>
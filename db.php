<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'bruceoilz';
$port = getenv('DB_PORT') ?: 3306;

$conn = mysqli_init();

// Enable SSL connection for production / TiDB Cloud
if (getenv('DB_SSL') === 'true' || getenv('DB_HOST')) {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    @mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
} else {
    @mysqli_real_connect($conn, $host, $user, $pass, $db, (int)$port);
}

// Set connection to false if it failed so pages can handle fallbacks cleanly
if (!$conn || mysqli_connect_errno()) {
    $conn = false;
}
?>
<?php
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';
$user = '2BTCsXYJuxUsUkn.root';
$pass = 'LiWZyAILrVsjB3pw';
$dbname = 'Bruceoilz'; // or 'sys'
$port = 4000;

$conn = mysqli_init();

if (!$conn) {
    die("mysqli_init failed");
}

// Enable SSL mode for TiDB Cloud
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

// Connect with SSL flag (MYSQLI_CLIENT_SSL)
if (!$conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully to TiDB!";
?>
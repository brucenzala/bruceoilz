$content = @"
<?php
`$host = getenv('DB_HOST') ?: 'localhost';
`$user = getenv('DB_USER') ?: 'root';
`$pass = getenv('DB_PASS') ?: '';
`$db   = getenv('DB_NAME') ?: 'bruceoilz';
`$port = getenv('DB_PORT') ?: 3306;

mysqli_report(MYSQLI_REPORT_OFF);

`$conn = mysqli_init();

if (getenv('DB_SSL') === 'true' || getenv('DB_HOST')) {
    mysqli_ssl_set(`$conn, NULL, NULL, NULL, NULL, NULL);
    @mysqli_real_connect(`$conn, `$host, `$user, `$pass, `$db, (int)`$port, NULL, MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
} else {
    @mysqli_real_connect(`$conn, `$host, `$user, `$pass, `$db, (int)`$port);
}

if (!`$conn || mysqli_connect_errno()) {
    `$db_error_details = mysqli_connect_error();
    `$conn = false;
}
"@

`$utf8NoBom = New-Object System.Text.UTF8Encoding `$false
[System.IO.File]::WriteAllText("`$PWD\db.php", `$content, `$utf8NoBom)
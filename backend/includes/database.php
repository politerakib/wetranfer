<?php

function db(): \mysqli
{
    static $connection = null;
    if ($connection instanceof \mysqli) {
        return $connection;
    }

    $host = env_get('DB_HOST', '127.0.0.1');
    $port = (int) env_get('DB_PORT', '3306');
    $dbname = env_get('DB_NAME', 'nshare');
    $user = env_get('DB_USER', 'root');
    $pass = env_get('DB_PASS', '');

    \mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $connection = new \mysqli($host, $user, $pass, $dbname, $port);
        $connection->set_charset('utf8mb4');
    } catch (\mysqli_sql_exception $exception) {
        throw new \RuntimeException('Unable to connect to the database: ' . $exception->getMessage(), 0, $exception);
    }

    return $connection;
}

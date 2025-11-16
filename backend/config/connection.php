<?php
// backend/config/connection.php

function getConnection() {
    $dsn = getenv("DATABASE_URL");

    if ($dsn) {
        // Render: parsea DATABASE_URL
        // Formato: mysql://user:pass@host:port/dbname o postgres://...
        $url = parse_url($dsn);

        $driver = $url['scheme'] === 'postgres' ? 'pgsql' : $url['scheme'];
        $host = $url['host'];
        $port = $url['port'];
        $user = $url['user'];
        $pass = $url['pass'];
        $dbname = ltrim($url['path'], '/');

        $pdoDsn = "$driver:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        try {
            $pdo = new PDO($pdoDsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión (Render): " . $e->getMessage());
        }
    } else {
        // Local Docker
        $db_host = 'db';
        $db_user = 'runatechdev';
        $db_pass = '1234';
        $db_name = 'idcultural';

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión (Docker): " . $e->getMessage());
        }
    }
}

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

        // DSN según el driver
        if ($driver === 'pgsql') {
            // Postgres no acepta charset en el DSN
            $pdoDsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        } else {
            // MySQL sí acepta charset
            $pdoDsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        }

        try {
            $pdo = new PDO($pdoDsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Forzar UTF-8 en Postgres
            if ($driver === 'pgsql') {
                $pdo->exec("SET NAMES 'UTF8'");
            }

            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión (Render): " . $e->getMessage());
        }
    } else {
        // Local Docker con MySQL
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

<?php

declare(strict_types=1);

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}

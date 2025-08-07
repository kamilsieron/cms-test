<?php

class DatabaseDriver
{
    private PDO $pdo;

    public function __construct()
    {
        $driver = $_ENV['DB_DRIVER'];
        $host   = $_ENV['DB_HOST'];
        $db     = $_ENV['DB_NAME'];
        $user   = $_ENV['DB_USER'];
        $pass   = $_ENV['DB_PASS'];

        $dsn = match ($driver) {
            'mysql'  => "mysql:host=$host;dbname=$db;charset=utf8mb4",
            'sqlsrv' => "sqlsrv:Server=$host;Database=$db",
            default  => throw new Exception("Nieobsługiwany sterownik: $driver"),
        };

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}

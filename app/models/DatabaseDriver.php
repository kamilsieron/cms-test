<?php
/**
 * DatabaseDriver – obsługa MySQL (PDO) oraz MS SQL Server (sqlsrv).
 *
 * Konfiguracja przez zmienne środowiskowe:
 *   DB_DRIVER = mysql | sqlsrv
 *   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 *   (opcjonalnie dla sqlsrv) DB_ENCRYPT=1|0, DB_TRUST_CERT=1|0
 */
class DatabaseDriver
{
    /** @var mixed */
    private $conn;
    /** @var string */
    private string $driver;

    public function __construct()
    {
        $this->driver = getenv('DB_DRIVER') ?: ($_ENV['DB_DRIVER'] ?? 'mysql');

        if ($this->driver === 'sqlsrv') {
            $this->connectSqlsrv();
        } else {
            // domyślnie PDO (MySQL)
            $this->connectPdo();
        }
    }

    private function connectPdo(): void
    {
        $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
        $db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? '');
        $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? '');
        $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');
        $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('PDO connection failed: ' . $e->getMessage(), 0, $e);
        }

        $this->conn = $pdo;
    }

    private function connectSqlsrv(): void
    {
        if (!function_exists('sqlsrv_connect')) {
            throw new \RuntimeException('Rozszerzenie php_sqlsrv nie jest dostępne (sqlsrv_connect nie istnieje).');
        }

        $host  = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
        $db    = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? '');
        $user  = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? '');
        $pass  = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');
        $port  = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '');
        $encrypt = (int)(getenv('DB_ENCRYPT') ?: ($_ENV['DB_ENCRYPT'] ?? 0));
        $trust   = (int)(getenv('DB_TRUST_CERT') ?: ($_ENV['DB_TRUST_CERT'] ?? 1));

        $serverName = $host;
        if (!empty($port)) {
            $serverName .= "," . $port; // sqlsrv używa host,port
        }

        $connectionInfo = [
            "Database" => $db,
            "UID" => $user,
            "PWD" => $pass,
            "CharacterSet" => "UTF-8",
            "Encrypt" => $encrypt ? 1 : 0,
            "TrustServerCertificate" => $trust ? 1 : 0,
        ];

        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if ($conn === false) {
            $errors = $this->formatSqlsrvErrors();
            throw new \RuntimeException('SQLSRV connection failed: ' . $errors);
        }
        $this->conn = $conn;
    }

    /** Wspólna metoda SELECT – zwraca tablicę asocjacyjną. */
    public function query(string $sql, array $params = []): array
    {
        if ($this->driver === 'sqlsrv') {
            $stmt = $this->prepareAndExecuteSqlsrv($sql, $params);
            $rows = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $rows[] = $row;
            }
            sqlsrv_free_stmt($stmt);
            return $rows;
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
    }

    /** INSERT/UPDATE/DELETE – zwraca powodzenie. */
    public function execute(string $sql, array $params = []): bool
    {
        if ($this->driver === 'sqlsrv') {
            $stmt = $this->prepareAndExecuteSqlsrv($sql, $params);
            sqlsrv_free_stmt($stmt);
            return true;
        } else {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        }
    }

    public function beginTransaction(): void
    {
        if ($this->driver === 'sqlsrv') {
            if (!sqlsrv_begin_transaction($this->conn)) {
                throw new \RuntimeException('Nie udało się rozpocząć transakcji: ' . $this->formatSqlsrvErrors());
            }
        } else {
            $this->conn->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->driver === 'sqlsrv') {
            if (!sqlsrv_commit($this->conn)) {
                throw new \RuntimeException('Nie udało się zatwierdzić transakcji: ' . $this->formatSqlsrvErrors());
            }
        } else {
            $this->conn->commit();
        }
    }

    public function rollBack(): void
    {
        if ($this->driver === 'sqlsrv') {
            if (!sqlsrv_rollback($this->conn)) {
                throw new \RuntimeException('Nie udało się wycofać transakcji: ' . $this->formatSqlsrvErrors());
            }
        } else {
            $this->conn->rollBack();
        }
    }

    /**
     * Ostatnie ID (auto increment).
     * Dla SQLSRV używa SCOPE_IDENTITY() – działa po INSERT w tej samej sesji.
     */
    public function lastInsertId(): string
    {
        if ($this->driver === 'sqlsrv') {
            $stmt = $this->prepareAndExecuteSqlsrv('SELECT CAST(SCOPE_IDENTITY() AS VARCHAR(255)) AS id');
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);
            return $row && isset($row['id']) ? (string)$row['id'] : '';
        } else {
            return $this->conn->lastInsertId();
        }
    }

    /** Zwraca aktywny driver: mysql | sqlsrv */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /** Pomocnik – przygotowanie i wykonanie zapytania dla sqlsrv. */
    private function prepareAndExecuteSqlsrv(string $sql, array $params = [])
    {
        // mapowanie parametrów: w sqlsrv przekazujemy zwykłe wartości (ew. referencje, gdy używamy typów)
        $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        if ($stmt === false) {
            throw new \RuntimeException('sqlsrv_prepare failed: ' . $this->formatSqlsrvErrors());
        }
        if (!sqlsrv_execute($stmt)) {
            $err = $this->formatSqlsrvErrors();
            sqlsrv_free_stmt($stmt);
            throw new \RuntimeException('sqlsrv_execute failed: ' . $err);
        }
        return $stmt;
    }

    /** Formatowanie błędów sqlsrv do czytelnego stringa. */
    private function formatSqlsrvErrors(): string
    {
        $errs = sqlsrv_errors() ?: [];
        $out = [];
        foreach ($errs as $e) {
            $out[] = sprintf('[%s] %s (SQLSTATE %s)', $e['code'] ?? '?', $e['message'] ?? 'unknown', $e['SQLSTATE'] ?? '?');
        }
        return implode('; ', $out);
    }
}

<?php

class PasswordResetModel
{
    private DatabaseDriver $db;
    public function __construct(DatabaseDriver $db) { $this->db = $db; }

    public function create(string $username, string $plainToken, \DateTime $expires): bool
    {
        $hash = hash('sha256', $plainToken);
        return $this->db->execute(
            "INSERT INTO password_resets (username, token_hash, expires_at) VALUES (:u, :h, :e)",
            ['u' => $username, 'h' => $hash, 'e' => $expires->format('Y-m-d H:i:s')]
        );
    }

    public function findValid(string $plainToken): ?array
    {
        $hash = hash('sha256', $plainToken);
        $rows = $this->db->query(
            "SELECT * FROM password_resets WHERE token_hash = :h AND used = 0 AND expires_at > NOW() LIMIT 1",
            ['h' => $hash]
        );
        return $rows[0] ?? null;
    }

    public function markUsed(int $id): void
    {
        $this->db->execute("UPDATE password_resets SET used = 1 WHERE id = :id", ['id' => $id]);
    }

    public function clearAllForUser(string $username): void
    {
        // opcjonalnie: czyścisz stare tokeny przy generowaniu nowego
        $this->db->execute("UPDATE password_resets SET used = 1 WHERE username = :u", ['u' => $username]);
    }
}

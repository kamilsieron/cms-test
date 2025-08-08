<?php

class PermissionModel
{
    private DatabaseDriver $db;

    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    public function getByUser(string $username): array
    {
        $result = $this->db->query("SELECT strona FROM uprawnienia WHERE login = :login", [
            'login' => $username
        ]);
        return array_column($result, 'strona');
    }

    public function replacePermissions(string $username, array $slugs, string $editor): void
    {
        $this->clearPermissions($username);

        foreach ($slugs as $slug) {
            $this->db->execute(
                "INSERT INTO uprawnienia (strona, login, login_edytujacego) VALUES (:strona, :login, :editor)",
                ['strona' => $slug, 'login' => $username, 'editor' => $editor]
            );
        }
    }

    public function clearPermissions(string $username): void
    {
        $this->db->execute("DELETE FROM uprawnienia WHERE login = :login", [
            'login' => $username
        ]);
    }
}

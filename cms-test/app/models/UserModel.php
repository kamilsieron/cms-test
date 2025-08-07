<?php

class UserModel
{
    private DatabaseDriver $db;

    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM users ORDER BY id ASC");
    }
	
	public function create(string $username, string $hash): bool
	{
		$sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
		return $this->db->execute($sql, [
			'username' => $username,
			'password' => $hash
		]);
	}
	
	public function find(string $username): ?array {
		$sql = "SELECT * FROM users WHERE username = :username";
		$result = $this->db->query($sql, ['username' => $username]);
		return $result[0] ?? null;
	}
	
	public function updatePassword(string $username, string $hash): bool {
		return $this->db->execute("UPDATE users SET password = :password WHERE username = :username", [
			'password' => $hash,
			'username' => $username
		]);
	}
	
	public function updateRole(string $username, string $role): bool {
		return $this->db->execute("UPDATE users SET role = :role WHERE username = :username", [
			'role' => $role,
			'username' => $username
		]);
	}
}



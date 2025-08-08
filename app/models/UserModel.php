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
	
public function create(string $username, string $email, string $role = 'user'): bool
{
    return $this->db->execute(
        "INSERT INTO users (username, email, role) VALUES (?, ?, ?)",
        [$username, $email, $role]
    );
}


public function find(string $username): ?array
{
    $r = $this->db->query("SELECT * FROM users WHERE username = ?", [$username]);
    return $r[0] ?? null;
}

public function updateAvatar(string $login, string $avatarPath): bool
{
    $sql = "UPDATE users SET avatar = :avatar WHERE username = :login";
    return $this->db->execute($sql, [
        'avatar' => $avatarPath,
        'login'  => $login
    ]);
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



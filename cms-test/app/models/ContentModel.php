<?php
require_once __DIR__ . '/DatabaseDriver.php';

class ContentModel
{
    private DatabaseDriver $db;

    public function __construct()
    {
        $this->db = new DatabaseDriver();
    }

    public function load(): array
    {
        $rows = $this->db->query("SELECT * FROM pages ORDER BY id ASC");
        $result = [];
        foreach ($rows as $row) {
            $result[$row['slug']] = [
                'title' => $row['title'],
                'body'  => $row['body'],
                'menu_label' => $row['menu_label'],
                'visible_in_menu' => $row['visible_in_menu']
            ];
        }
        return $result;
    }

    public function save(string $slug, array $data): void
    {
        $existing = $this->db->query("SELECT id FROM pages WHERE slug = ?", [$slug]);

        if ($existing) {
            // update
            $this->db->execute(
                "UPDATE pages SET title = ?, body = ?, menu_label = ?, visible_in_menu = ? WHERE slug = ?",
                [$data['title'], $data['body'], $data['menu_label'], $data['visible_in_menu'], $slug]
            );
        } else {
            // insert
            $this->db->execute(
                "INSERT INTO pages (slug, title, body, menu_label, visible_in_menu) VALUES (?, ?, ?, ?, ?)",
                [$slug, $data['title'], $data['body'], $data['menu_label'], $data['visible_in_menu']]
            );
        }
    }
	
	public function allPages(): array
	{
		return $this->db->query("SELECT slug, title FROM pages ORDER BY slug ASC");
	}
}


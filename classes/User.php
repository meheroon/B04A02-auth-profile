<?php
// classes/User.php
declare(strict_types=1);

class User
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->pdo();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at, password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ");

        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
        ]);
    }

    public function update(int $id, string $name, string $email, ?string $newPassword = null): bool
    {
        if ($newPassword && trim($newPassword) !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE users SET name = :name, email = :email, password = :password
                WHERE id = :id
            ");
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hash,
                'id' => $id,
            ]);
        }

        $stmt = $this->db->prepare("
            UPDATE users SET name = :name, email = :email
            WHERE id = :id
        ");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'id' => $id,
        ]);
    }
}

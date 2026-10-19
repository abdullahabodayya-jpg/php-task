<?php

require_once __DIR__ . "/../config/Database.php";

class UserRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $firstName, string $lastName, string $email, string $contacNumber, string $address): bool{
        $sql = "INSERT INTO users
                (first_name, last_name, email, contact_number, address)
                VALUES
                (:first_name, :last_name, :email, :contact_number, :address)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(["first_name" => $firstName, "last_name" => $lastName, "email" => $email, "contact_number" => $contacNumber, "address" => $address]);
            
    }

    public function getAll(): array{
        $sql = "SELECT * FROM users ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getByID(int $id): array {
        $sql = "SELECT * FROM users WHERE id = :id";

       $stmt = $this->pdo->prepare($sql);
       $stmt->execute(["id" => $id]);
       return $stmt->fetch();

    }

    public function delete(int $id): bool{

    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute(["id" => $id]);
    }

    public function update(int $id, string $firstName, string $lastName, string $email, string $contacNumber, string $address): bool{
        $sql = "UPDATE users 
                SET  first_name = :first_name,
                     last_name = :last_name,
                    email = :email,
                    contact_number = :contact_number,
                    address = :address 
                WHERE id = :id  
                    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id" => $id,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "email" => $email,
            "contact_number" => $contacNumber,
            "address" => $address
        ]);

    }
        

} 
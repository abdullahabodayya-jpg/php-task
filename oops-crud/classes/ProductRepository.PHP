
<?php

require_once __DIR__ . "/../config/Database.php";

class ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function getAll(): array
    {
        $sql = "
            SELECT
                p.*,
                (
                    SELECT pi.image_path
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.is_primary DESC, pi.id ASC
                    LIMIT 1
                ) AS image_path
            FROM products p
            ORDER BY p.id DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    
    public function getById(int $id): array|false
    {
        $sql = "
            SELECT *
            FROM products
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();
    }

    
    public function create(
            string $name,
            string $description,
            float $price,
            int $quantity
        ): int {
            $sql = "
                INSERT INTO products
                    (name, description, price, quantity)
                VALUES
                    (:name, :description, :price, :quantity)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                "name" => $name,
                "description" => $description,
                "price" => $price,
                "quantity" => $quantity
            ]);

            return (int) $this->pdo->lastInsertId();
        }

    
    public function update(
        int $id,
        string $name,
        string $description,
        float $price,
        int $quantity
    ): bool {
        $sql = "
            UPDATE products
            SET
                name = :name,
                description = :description,
                price = :price,
                quantity = :quantity
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "quantity" => $quantity
        ]);
    }

    
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM products WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(["id" => $id]);
    }
}

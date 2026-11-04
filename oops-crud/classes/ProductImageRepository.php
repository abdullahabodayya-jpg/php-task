
<?php

class ProductImageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function add(
        int $productId,
        string $imagePath,
        bool $isPrimary = false
    ): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO product_images
                (product_id, image_path, is_primary)
             VALUES
                (:product_id, :image_path, :is_primary)"
        );

        $stmt->execute([
            "product_id" => $productId,
            "image_path" => $imagePath,
            "is_primary" => $isPrimary ? 1 : 0
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM product_images
             WHERE product_id = :product_id
             ORDER BY is_primary DESC, id ASC"
        );

        $stmt->execute(["product_id" => $productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $imageId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM product_images
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->execute(["id" => $imageId]);

        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        return $image ?: null;
    }

    public function delete(int $imageId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM product_images WHERE id = :id"
        );

        $stmt->execute(["id" => $imageId]);

        return $stmt->rowCount() > 0;
    }

    public function setPrimary(
        int $productId,
        int $imageId
    ): bool {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT id
                 FROM product_images
                 WHERE id = :image_id
                   AND product_id = :product_id
                 LIMIT 1"
            );

            $stmt->execute([
                "image_id" => $imageId,
                "product_id" => $productId
            ]);

            if (!$stmt->fetch()) {
                throw new RuntimeException(
                    "Image does not belong to this product."
                );
            }

            $stmt = $this->pdo->prepare(
                "UPDATE product_images
                 SET is_primary = 0
                 WHERE product_id = :product_id"
            );

            $stmt->execute(["product_id" => $productId]);

            $stmt = $this->pdo->prepare(
                "UPDATE product_images
                 SET is_primary = 1
                 WHERE id = :image_id
                   AND product_id = :product_id"
            );

            $stmt->execute([
                "image_id" => $imageId,
                "product_id" => $productId
            ]);

            $this->pdo->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}

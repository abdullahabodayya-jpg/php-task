<?php

require_once __DIR__ . "/../classes/ProductRepository.php";
require_once __DIR__ . "/../classes/ProductImageRepository.php";
require_once __DIR__ . "/../classes/ProductImageUploader.php";

$database = new Database();
$pdo = $database->connect();

$productRepository = new ProductRepository($pdo);
$imageRepository = new ProductImageRepository($pdo);
$uploader = new ProductImageUploader();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: products.php?error=invalid_id");
    exit;
}

try {
    $images = $imageRepository->getByProductId($id);

    $pdo->beginTransaction();

    try {
        $product = $productRepository->getById($id);

        if (!$product) {
            $pdo->rollBack();
            header("Location: products.php?error=not_found");
            exit;
        }

        $productRepository->delete($id);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }

    foreach ($images as $image) {
        $uploader->deleteFile($image["image_path"]);
    }

    header("Location: products.php?message=deleted");
    exit;
} catch (Throwable $e) {
    error_log($e->getMessage());

    header("Location: products.php?error=delete_failed");
    exit;
}
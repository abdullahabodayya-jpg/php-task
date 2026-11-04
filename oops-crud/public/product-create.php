<?php

require_once __DIR__ . "/../classes/ProductRepository.php";
require_once __DIR__ . "/../classes/ProductImageRepository.php";
require_once __DIR__ . "/../classes/ProductImageUploader.php";

$database = new Database();
$pdo = $database->connect();

$productRepository = new ProductRepository($pdo);
$imageRepository = new ProductImageRepository($pdo);
$uploader = new ProductImageUploader();

$error = "";
$name = "";
$description = "";
$price = "";
$quantity = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $quantity = $_POST["quantity"] ?? "";

    $priceValid = is_scalar($price)
        && trim((string) $price) !== ""
        && is_numeric($price)
        && is_finite((float) $price)
        && (float) $price >= 0
        && (float) $price <= 99999999.99;

    $quantityValid = is_scalar($quantity)
        && filter_var(
            $quantity,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0, "max_range" => 4294967295]]
        ) !== false;

    $files = $_FILES["images"] ?? null;
    $fileCount = 0;

    if ($files && isset($files["name"]) && is_array($files["name"])) {
        foreach ($files["name"] as $index => $unused) {
            if (($files["error"][$index] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_NO_FILE) {
                $fileCount++;
            }
        }
    }

    if (
        $name === "" ||
        strlen($name) > 150 ||
        strlen($description) > 20000 ||
        !$priceValid ||
        !$quantityValid ||
        $fileCount > 5
    ) {
        $error = "Please check the product details. You can upload up to 5 images.";
    } else {
        $savedFiles = [];
        $pdo->beginTransaction();

        try {
            $productId = $productRepository->create(
                $name,
                $description,
                (float) $price,
                (int) $quantity
            );

            if ($files && is_array($files["name"])) {
                foreach ($files["name"] as $index => $unused) {
                    $file = [
                        "name" => $files["name"][$index],
                        "type" => $files["type"][$index],
                        "tmp_name" => $files["tmp_name"][$index],
                        "error" => $files["error"][$index],
                        "size" => $files["size"][$index]
                    ];

                    if ($file["error"] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    if (count($savedFiles) >= 5) {
                        throw new RuntimeException(
                            "You can upload up to 5 images."
                        );
                    }

                    $path = $uploader->upload($file);
                    $savedFiles[] = $path;

                    $imageRepository->add(
                        $productId,
                        $path,
                        count($savedFiles) === 1
                    );
                }
            }

            $pdo->commit();

            header("Location: products.php?message=created");
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            foreach ($savedFiles as $path) {
                $uploader->deleteFile($path);
            }

            error_log($e->getMessage());
            $error = "Could not create the product. Check the information and images.";
        }
    }
}

function e(string|null $value): string
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add Product</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold mb-4">Add Product</h1>

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input
                        id="name"
                        name="name"
                        class="form-control"
                        maxlength="150"
                        value="<?= e($name) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="4"
                        maxlength="20000"
                    ><?= e($description) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input
                            id="price"
                            type="number"
                            name="price"
                            class="form-control"
                            min="0"
                            max="99999999.99"
                            step="0.01"
                            value="<?= e(is_scalar($price) ? (string) $price : "") ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">
                            Quantity
                        </label>
                        <input
                            id="quantity"
                            type="number"
                            name="quantity"
                            class="form-control"
                            min="0"
                            step="1"
                            value="<?= e(is_scalar($quantity) ? (string) $quantity : "") ?>"
                            required
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="images" class="form-label">
                        Product Images (up to 5)
                    </label>
                    <input
                        id="images"
                        type="file"
                        name="images[]"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >
                    <div class="form-text">
                        JPEG, PNG or WebP. Maximum 5 MB per image.
                    </div>
                </div>

                <button class="btn btn-orange text-white" type="submit">
                    Save Product
                </button>

                <a href="products.php" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-orange {
        background: #ff6b00;
        border-color: #ff6b00;
    }

    .btn-orange:hover {
        background: #e85d00;
        border-color: #e85d00;
    }
</style>

</body>
</html>
<?php

require_once __DIR__ . "/../classes/ProductRepository.php";
require_once __DIR__ . "/../classes/ProductImageRepository.php";
require_once __DIR__ . "/../classes/ProductImageUploader.php";

$database = new Database();
$pdo = $database->connect();

$productRepository = new ProductRepository($pdo);
$imageRepository = new ProductImageRepository($pdo);
$uploader = new ProductImageUploader();

function e(string|null $value): string
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: products.php");
    exit;
}

$product = $productRepository->getById($id);

if (!$product) {
    http_response_code(404);
    exit("Product not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "update";

    try {
        if ($action === "delete_image") {
            $imageId = filter_input(
                INPUT_POST,
                "image_id",
                FILTER_VALIDATE_INT
            );

            if (!$imageId || $imageId < 1) {
                throw new RuntimeException("Invalid image.");
            }

            $image = $imageRepository->getById($imageId);

            if (!$image || (int) $image["product_id"] !== $id) {
                throw new RuntimeException("Image not found.");
            }

            $pdo->beginTransaction();

            try {
                $imageRepository->delete($imageId);

                // If the primary image was deleted, select another.
                if ((int) $image["is_primary"] === 1) {
                    $remaining = $imageRepository->getByProductId($id);

                    if (!empty($remaining)) {
                        $imageRepository->setPrimary(
                            $id,
                            (int) $remaining[0]["id"]
                        );
                    }
                }

                $pdo->commit();
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e;
            }

            // Delete the physical file only after the DB commit.
            $uploader->deleteFile($image["image_path"]);

            header("Location: product-edit.php?id=" . $id);
            exit;
        }

        if ($action === "set_primary") {
            $imageId = filter_input(
                INPUT_POST,
                "image_id",
                FILTER_VALIDATE_INT
            );

            if (!$imageId || !$imageRepository->setPrimary($id, $imageId)) {
                throw new RuntimeException("Could not select primary image.");
            }

            header("Location: product-edit.php?id=" . $id);
            exit;
        }

        if ($action !== "update") {
            throw new RuntimeException("Invalid action.");
        }

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

        if (
            $name === "" ||
            strlen($name) > 150 ||
            strlen($description) > 20000 ||
            !$priceValid ||
            !$quantityValid
        ) {
            throw new RuntimeException("Please enter valid product details.");
        }

        $files = $_FILES["images"] ?? null;
        $savedFiles = [];
        $pdo->beginTransaction();

        try {
            $productRepository->update(
                $id,
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

                    $currentImages = $imageRepository->getByProductId($id);

                    if (count($currentImages) + count($savedFiles) >= 5) {
                        throw new RuntimeException(
                            "A product can have at most 5 images."
                        );
                    }

                    $path = $uploader->upload($file);
                    $savedFiles[] = $path;

                    $imageRepository->add(
                        $id,
                        $path,
                        empty($currentImages) && count($savedFiles) === 1
                    );
                }
            }

            $pdo->commit();

            header("Location: product-edit.php?id=" . $id . "&saved=1");
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            foreach ($savedFiles as $path) {
                $uploader->deleteFile($path);
            }

            throw $e;
        }
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = "Could not complete the action. Please check your input.";
    }
}

$product = $productRepository->getById($id);
$images = $imageRepository->getByProductId($id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Product</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body { background: #f5f6fa; }

        .image-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .btn-orange {
            background: #ff6b00;
            border-color: #ff6b00;
            color: white;
        }

        .btn-orange:hover {
            background: #e85d00;
            border-color: #e85d00;
            color: white;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Edit Product</h1>
        <a href="products.php" class="btn btn-outline-secondary">
            Back to Products
        </a>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET["saved"])): ?>
        <div class="alert alert-success">Product updated successfully.</div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">

                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        class="form-control"
                        maxlength="150"
                        value="<?= e($product["name"]) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="3"
                        maxlength="20000"
                    ><?= e($product["description"]) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="price">Price</label>
                        <input
                            id="price"
                            type="number"
                            name="price"
                            class="form-control"
                            min="0"
                            max="99999999.99"
                            step="0.01"
                            value="<?= e((string) $product["price"]) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="quantity">
                            Quantity
                        </label>
                        <input
                            id="quantity"
                            type="number"
                            name="quantity"
                            class="form-control"
                            min="0"
                            step="1"
                            value="<?= (int) $product["quantity"] ?>"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="images">
                        Add More Images
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
                        Maximum 5 images per product, 5 MB per image.
                    </div>
                </div>

                <button class="btn btn-orange" type="submit">
                    Save Changes
                </button>
            </form>

        </div>
    </div>

    <h2 class="h4 fw-bold mb-3">Product Images</h2>

    <div class="row g-3">
        <?php if (empty($images)): ?>
            <div class="col-12">
                <p class="text-secondary">No images uploaded yet.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($images as $image): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card image-card shadow-sm h-100">
                    <img
                        class="product-image"
                        src="<?= e($image["image_path"]) ?>"
                        alt="<?= e($product["name"]) ?>"
                    >

                    <div class="card-body">
                        <?php if ((int) $image["is_primary"] === 1): ?>
                            <span class="badge text-bg-success mb-2">
                                Primary Image
                            </span>
                        <?php else: ?>
                            <form method="POST" class="mb-2">
                                <input
                                    type="hidden"
                                    name="action"
                                    value="set_primary"
                                >
                                <input
                                    type="hidden"
                                    name="image_id"
                                    value="<?= (int) $image["id"] ?>"
                                >
                                <button class="btn btn-sm btn-outline-primary w-100">
                                    Set as Primary
                                </button>
                            </form>
                        <?php endif; ?>

                        <form
                            method="POST"
                            onsubmit="return confirm('Delete this image?');"
                        >
                            <input
                                type="hidden"
                                name="action"
                                value="delete_image"
                            >
                            <input
                                type="hidden"
                                name="image_id"
                                value="<?= (int) $image["id"] ?>"
                            >
                            <button class="btn btn-sm btn-outline-danger w-100">
                                Delete Image
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
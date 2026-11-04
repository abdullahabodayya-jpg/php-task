
<?php

require_once __DIR__ . "/../classes/ProductRepository.php";

$database = new Database();
$pdo = $database->connect();

$productRepository = new ProductRepository($pdo);
$products = $productRepository->getAll();

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

    <title>Our Products | My Store</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f6fa;
            color: #212529;
        }

        .navbar {
            background: #212529;
        }

        .page-title {
            letter-spacing: -0.5px;
        }

        .product-card {
            height: 100%;
            border: 0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #e9ecef;
        }

        .product-description {
            color: #6c757d;
            min-height: 48px;
        }

        .product-price {
            color: #e85d00;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .btn-orange {
            background: #ff6b00;
            border: 1px solid #ff6b00;
            color: #fff;
        }

        .btn-orange:hover {
            background: #e85d00;
            border-color: #e85d00;
            color: #fff;
        }

        .empty-state {
            border: 0;
            border-radius: 14px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="products.php">
            My Store
        </a>
    </div>
</nav>

<main class="container py-5">

    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">

        <div>
            <h1 class="page-title fw-bold mb-2">Our Products</h1>
            <p class="text-secondary mb-0">
                Find your favorite products.
            </p>
        </div>

        <span class="badge text-bg-dark rounded-pill px-3 py-2">
            <?= count($products) ?>
            <?= count($products) === 1 ? "Product" : "Products" ?>
        </span>

    </div>

    <div class="row g-4">

        <?php if (empty($products)): ?>

            <div class="col-12">
                <div class="card empty-state shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="fw-bold">No products yet</h4>
                        <p class="text-secondary mb-0">
                            Products will appear here once they are added.
                        </p>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($products as $product): ?>

                <?php
                $imagePath = $product["image_path"] ?? "";

                $imageUrl = $imagePath !== ""
                    ? $imagePath
                    : "assets/no-image.png";

                $quantity = (int) $product["quantity"];
                ?>

                <div class="col-sm-6 col-lg-4 col-xl-3">

                    <div class="card product-card shadow-sm">

                        <img
                            src="<?= e($imageUrl) ?>"
                            class="product-image"
                            alt="<?= e($product["name"]) ?>"
                            loading="lazy"
                        >

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title fw-bold">
                                <?= e($product["name"]) ?>
                            </h5>

                            <p class="product-description">
                                <?= e($product["description"]) ?>
                            </p>

                            <p class="product-price mb-2">
                                $<?= number_format(
                                    (float) $product["price"],
                                    2
                                ) ?>
                            </p>

                            <div class="mb-3">
                                <?php if ($quantity > 0): ?>
                                    <span class="badge text-bg-success">
                                        In Stock: <?= $quantity ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">
                                        Out of Stock
                                    </span>
                                <?php endif; ?>
                            </div>

                            <a
                                href="product-edit.php?id=<?= (int) $product["id"] ?>"
                                class="btn btn-orange mt-auto"
                            >
                                Edit Product
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

</body>
</html>

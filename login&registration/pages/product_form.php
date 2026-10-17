<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../database/db_connect.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$isEdit = $id !== null && $id !== false && $id > 0;

$name = "";
$description = "";
$price = "";
$quantity = "";
$error = "";

if ($isEdit) {
    $stmt = $conn->prepare(
        "SELECT name, description, price, quantity
         FROM products WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $description, $price, $quantity);

    if (!$stmt->fetch()) {
        $stmt->close();
        http_response_code(404);
        exit("Product not found.");
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if (
        $name === '' ||
        !is_numeric($price) || (float)$price < 0 ||
        filter_var($quantity, FILTER_VALIDATE_INT) === false ||
        (int)$quantity < 0
    ) {
        $error = "Please enter valid product details.";
    } else {
        $price = (float)$price;
        $quantity = (int)$quantity;

        if ($isEdit) {
            $stmt = $conn->prepare(
                "UPDATE products
                 SET name = ?, description = ?, price = ?, quantity = ?
                 WHERE id = ?"
            );
            $stmt->bind_param(
                "ssdii",
                $name,
                $description,
                $price,
                $quantity,
                $id
            );
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO products (name, description, price, quantity)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssdi",
                $name,
                $description,
                $price,
                $quantity
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: dashboard.php");
            exit();
        }

        $error = "Could not save product. Please try again.";
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <h2 class="mb-4">
                <?= $isEdit ? 'Edit Product' : 'Add Product' ?>
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($name) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control"
                           min="0" step="0.01"
                           value="<?= htmlspecialchars((string)$price) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control"
                           min="0" step="1"
                           value="<?= htmlspecialchars((string)$quantity) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Product' : 'Add Product' ?>
                </button>

                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
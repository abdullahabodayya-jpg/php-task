<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../database/db_connect.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$error = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($action === 'remove') {
        if ($id !== false && $id !== null) {
            unset($_SESSION['cart'][$id]);
        }
    } elseif ($action === 'add' && $id !== false && $id !== null && $id > 0) {
        $stmt = $conn->prepare(
            "SELECT name, quantity FROM products WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($productName, $stock);

        if ($stmt->fetch()) {
            if ($stock < 1) {
                $error = "This product is out of stock.";
            } elseif (($_SESSION['cart'][$id] ?? 0) >= $stock) {
                $error = "You cannot add more than the available stock.";
            } else {
                $_SESSION['cart'][$id] =
                    ($_SESSION['cart'][$id] ?? 0) + 1;
                $message = $productName . " added to cart!";
            }
        }

        $stmt->close();
    } elseif ($action === 'update' && $id !== false && $id !== null && $id > 0) {
        $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($qty === false || $qty === null || $qty < 1) {
            unset($_SESSION['cart'][$id]);
        } else {
            $stmt = $conn->prepare(
                "SELECT quantity FROM products WHERE id = ?"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($stock);

            if ($stmt->fetch()) {
                if ($qty <= $stock) {
                    $_SESSION['cart'][$id] = $qty;
                } else {
                    $error = "Requested quantity exceeds available stock.";
                }
            } else {
                unset($_SESSION['cart'][$id]);
            }

            $stmt->close();
        }
    }

    $query = http_build_query([
        'search' => $search,
        'sort' => $sort
    ]);

    header("Location: products.php?" . $query);
    exit();
}

$orderBy = match ($sort) {
    'price_low' => 'price ASC',
    'price_high' => 'price DESC',
    'name' => 'name ASC',
    default => 'created_at DESC'
};

$sql = "SELECT id, name, description, price, quantity, created_at
        FROM products";

if ($search !== '') {
    $sql .= " WHERE name LIKE ? OR description LIKE ?";
}

$sql .= " ORDER BY " . $orderBy;

if ($search !== '') {
    $stmt = $conn->prepare($sql);
    $keyword = "%" . $search . "%";
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$cartProducts = [];
$cartTotal = 0;
$cartCount = 0;

if (!empty($_SESSION['cart'])) {
    $cartIds = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
    $types = str_repeat('i', count($cartIds));

    $stmtCart = $conn->prepare(
        "SELECT id, name, price, quantity
         FROM products
         WHERE id IN ($placeholders)"
    );

    $stmtCart->bind_param($types, ...$cartIds);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();

    while ($item = $cartResult->fetch_assoc()) {
        $id = (int) $item['id'];
        $qty = (int) $_SESSION['cart'][$id];

        
        if ((int) $item['quantity'] < 1) {
            unset($_SESSION['cart'][$id]);
            continue;
        }

        if ($qty > (int) $item['quantity']) {
            $qty = (int) $item['quantity'];
            $_SESSION['cart'][$id] = $qty;
        }

        $item['cart_quantity'] = $qty;
        $item['subtotal'] = (float) $item['price'] * $qty;

        $cartProducts[] = $item;
        $cartTotal += $item['subtotal'];
        $cartCount += $qty;
    }

    $stmtCart->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #212529;
        }

        .page-title {
            font-weight: bold;
        }

        .product-card {
            height: 100%;
            border: 0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        }

        .product-card .card-body {
            display: flex;
            flex-direction: column;
        }

        .product-description {
            color: #6c757d;
            min-height: 48px;
            overflow-wrap: anywhere;
        }

        .product-price {
            color: #e85d00;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .btn-orange {
            background: #ff6b00;
            color: white;
            border: none;
        }

        .btn-orange:hover {
            background: #e85d00;
            color: white;
        }

        .cart-box {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 12px 0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="products.php">
            My Store
        </a>

        <div class="d-flex align-items-center gap-3 text-white">
            <span>
                <?= htmlspecialchars($_SESSION['email'] ?? 'User') ?>
            </span>

            <a href="logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="page-title">Our Products</h1>
            <p class="text-secondary mb-0">Find your favorite products.</p>
        </div>

        <span class="badge text-bg-dark fs-6">
            Cart: <?= $cartCount ?> item(s)
        </span>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-7">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search by product name or description..."
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>

        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>
                    Newest
                </option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>
                    Price: Low to High
                </option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>
                    Price: High to Low
                </option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>
                    Name: A to Z
                </option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-orange w-100">
                Search
            </button>
            <a href="products.php" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="row g-4">

        
        <div class="col-lg-8">
            <div class="row g-4">

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($product = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card product-card shadow-sm">

                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </h5>

                                    <p class="product-description">
                                        <?= htmlspecialchars(
                                            $product['description'] ?? ''
                                        ) ?>
                                    </p>

                                    <p class="product-price mb-2">
                                        $<?= number_format((float) $product['price'], 2) ?>
                                    </p>

                                    <p class="small text-secondary">
                                        Available:
                                        <?= (int) $product['quantity'] ?>
                                    </p>

                                    <p class="small text-secondary">
                                        Added:
                                        <?= htmlspecialchars($product['created_at']) ?>
                                    </p>

                                    <form method="POST" class="mt-auto">
                                        <input type="hidden" name="product_id"
                                               value="<?= (int) $product['id'] ?>">

                                        <input type="hidden" name="action" value="add">

                                        <button
                                            type="submit"
                                            class="btn btn-orange w-100"
                                            <?= (int) $product['quantity'] < 1 ? 'disabled' : '' ?>
                                        >
                                            <?= (int) $product['quantity'] < 1
                                                ? 'Out of Stock'
                                                : 'Add to Cart' ?>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="col-12">
                        <div class="alert alert-info">
                            No products found.
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-box">
                <h4 class="mb-3">Your Cart</h4>

                <?php if (empty($cartProducts)): ?>

                    <p class="text-secondary">
                        Your cart is empty.
                    </p>

                <?php else: ?>

                    <?php foreach ($cartProducts as $item): ?>
                        <div class="cart-item">
                            <strong>
                                <?= htmlspecialchars($item['name']) ?>
                            </strong>

                            <p class="small text-secondary mb-2">
                                $<?= number_format((float) $item['price'], 2) ?>
                                each
                            </p>

                            <form method="POST" class="d-flex gap-2 mb-2">
                                <input type="hidden" name="product_id"
                                       value="<?= (int) $item['id'] ?>">

                                <input type="hidden" name="action" value="update">

                                <input
                                    type="number"
                                    name="quantity"
                                    class="form-control form-control-sm"
                                    min="1"
                                    max="<?= (int) $item['quantity'] ?>"
                                    value="<?= (int) $item['cart_quantity'] ?>"
                                    required
                                >

                                <button type="submit"
                                        class="btn btn-sm btn-outline-primary">
                                    Update
                                </button>
                            </form>

                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    Subtotal:
                                    $<?= number_format($item['subtotal'], 2) ?>
                                </span>

                                <form method="POST">
                                    <input type="hidden" name="product_id"
                                           value="<?= (int) $item['id'] ?>">

                                    <input type="hidden" name="action" value="remove">

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span>$<?= number_format($cartTotal, 2) ?></span>
                    </div>

                    <p class="small text-secondary mt-3 mb-0">
                        This is a demo cart. Checkout and payment are not included.
                    </p>

                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>
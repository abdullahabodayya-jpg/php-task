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


$usersResult = $conn->query(
    "SELECT COUNT(*) AS total FROM userdata"
);
$totalUsers = $usersResult->fetch_assoc()['total'];


$productsResult = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
);
$totalProducts = $productsResult->fetch_assoc()['total'];


$users = $conn->query(
    "SELECT id, name, email, role FROM userdata ORDER BY id DESC"
);


$products = $conn->query(
    "SELECT id, name, description, price, quantity, created_at
     FROM products ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            Admin Dashboard
        </a>

        <div class="ms-auto">
            <span class="text-white me-3">
                Welcome, <?= htmlspecialchars($_SESSION['email']) ?>
            </span>

            <a href="logout.php" class="btn btn-light">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h2 class="mb-4">Dashboard Overview</h2>

 
    <div class="row g-3 mb-5">

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-muted">Total Users</h5>
                    <h2><?= (int) $totalUsers ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-muted">Total Products</h5>
                    <h2><?= (int) $totalProducts ?></h2>
                </div>
            </div>
        </div>

    </div>

   
    <section id="users" class="mb-5">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>User Management</h3>

            <a href="user_form.php" class="btn btn-success">
                + Add User
            </a>
        </div>

        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-striped table-hover mb-0">

                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $user['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($user['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user['role']) ?>
                            </td>

                            <td>
                                <a
                                    href="user_form.php?id=<?= (int) $user['id'] ?>"
                                    class="btn btn-sm btn-primary"
                                >
                                    Edit
                                </a>

                                <form
                                    action="delete_user.php"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this user?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $user['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </section>

  
    <section id="products" class="mb-5">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Product Management</h3>

            <a href="product_form.php" class="btn btn-success">
                + Add Product
            </a>
        </div>

        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-striped table-hover mb-0">

                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($products && $products->num_rows > 0): ?>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $product['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($product['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product['description'] ?? '') ?>
                            </td>

                            <td>
                                <?= number_format((float) $product['price'], 2) ?>
                            </td>

                            <td>
                                <?= (int) $product['quantity'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product['created_at']) ?>
                            </td>

                            <td>
                                <a
                                    href="product_form.php?id=<?= (int) $product['id'] ?>"
                                    class="btn btn-sm btn-primary"
                                >
                                    Edit
                                </a>

                                <form
                                    action="delete_product.php"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this product?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $product['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No products found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

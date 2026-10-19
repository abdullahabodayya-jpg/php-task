<?php 

require_once __DIR__ . "/../classes/UserRepository.php";

$database = new Database();
$pdo = $database->connect();

$userRepository = new UserRepository($pdo);

$users = $userRepository->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Users Dashboard</title>
</head>

<body>

    <h1>Users Dashboard</h1>

    <a href="create.php">+ Add New User</a>

    <br><br>

    <?php if (empty($users)): ?>

        <p>No users found.</p>

    <?php else: ?>

        <table border="1" cellpadding="10" cellspacing="0">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars((string) $user["id"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["first_name"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["last_name"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["email"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["contact_number"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["address"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user["created_at"], ENT_QUOTES, "UTF-8") ?>
                        </td>

                        <td>
                            <a href="edit.php?id=<?= (int) $user["id"] ?>">
                                Edit
                            </a>

                            |

                            <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">

                            <input type="hidden" name="id" value="<?=  (int) $user["id"] ?>">

                            <button type="submit">Delete</button>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>

</html>
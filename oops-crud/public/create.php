<?php

require_once __DIR__ . "/../classes/UserRepository.php";

$database = new Database();
$pdo = $database->connect();

$userRepository = new UserRepository($pdo);
$error = "";

if ($_SERVER["REQUEST_METHOD"]==="POST") {
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $contactNumber  = trim($_POST["contact_number"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if ($firstName === "" || $lastName === ""|| $email === "" || $contactNumber === "" || $address === "" ){
        $error = "Please enter valid information.";
    } else {
        try {
            $success = $userRepository->create(
                $firstName,
                $lastName,
                $email,
                $contactNumber,
                $address
            );

            if ($success) {
                header("Location: index.php");
                exit;
            }

            $error = "Failed to create user.";
        }
        catch (PDOException $e){
            $error = "Could not create user. Please check the information and try again.";
        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
</head>
<body>

    <h1>Create New User</h1>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">

        <div>
            <label for="first_name">First Name</label>
            <input
                type="text"
                id="first_name"
                name="first_name"
                maxlength="100"
                value="<?= htmlspecialchars($_POST["first_name"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >
        </div>

        <div>
            <label for="last_name">Last Name</label>
            <input
                type="text"
                id="last_name"
                name="last_name"
                maxlength="100"
                value="<?= htmlspecialchars($_POST["last_name"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >
        </div>

        <div>
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >
        </div>

        <div>
            <label for="contact_number">Contact Number</label>
            <input
                type="text"
                id="contact_number"
                name="contact_number"
                maxlength="20"
                value="<?= htmlspecialchars($_POST["contact_number"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >
        </div>

        <div>
            <label for="address">Address</label>
            <textarea
                id="address"
                name="address"
                maxlength="255"
                required
            ><?= htmlspecialchars($_POST["address"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>
        </div>

        <button type="submit">Create User</button>

    </form>

    <a href="index.php">Back to Dashboard</a>

</body>
</html>


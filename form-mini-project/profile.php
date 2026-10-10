
<?php
session_start();

if (!isset($_SESSION["registration"])) {
    echo "No registration data found.";
    exit;
}

$user = $_SESSION["registration"];

function escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<section>
    <h1>My Registration Information</h1>

    <p>Name: <?= escape($user["name"]) ?></p>
    <p>Email: <?= escape($user["email"]) ?></p>
    <p>Mobile: <?= escape($user["mobile"]) ?></p>
    <p>Governorate: <?= escape($user["governorate"]) ?></p>
    <p>Track: <?= escape($user["track"]) ?></p>
    <p>Skills: <?= escape(implode(", ", $user["skills"])) ?></p>
    <p>Message: <?= escape($user["message"]) ?></p>

    <a href="index.php">Back to registration</a>
</section>

</body>
</html>

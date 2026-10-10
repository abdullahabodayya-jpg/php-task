
<?php
$q = trim($_GET["q"] ?? "");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<form action="search.php" method="get">
    <h2>Search</h2>

    <label for="q">Search keyword</label>
    <input
        type="text"
        id="q"
        name="q"
        value="<?= htmlspecialchars($q, ENT_QUOTES, "UTF-8") ?>"
        placeholder="Search something..."
    >

    <button type="submit">Search</button>
</form>

<?php if ($q !== ""): ?>
    <p>
        Search result for:
        <?= htmlspecialchars($q, ENT_QUOTES, "UTF-8") ?>
    </p>
<?php endif; ?>

</body>
</html>

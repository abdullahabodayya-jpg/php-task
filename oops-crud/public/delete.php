<?php

require_once __DIR__ . "/../classes/UserRepository.php";

$database = new Database();
$pdo = $database->connect();

$userRepository = new UserRepository($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    
    header("Location: index.php");
    exit;
}

try {
    $userRepository->delete($id);

}catch(PDOException $e){
    error_log($e->getMessage());
}

header("Location: index.php");
exit;

?>
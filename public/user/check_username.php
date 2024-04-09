<?php
require_once __DIR__ . '/../../config.php'; 
if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "<span style='color: red;'>Username is already taken.</span>";
    } else {
        echo "<span style='color: green;'>Username is available.</span>";
    }
}

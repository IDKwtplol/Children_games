<?php
// Initialize the session
session_start();

// Include config file
require_once __DIR__ . '/../includes/database.php'; // Ensure the path is correctly adjusted

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch additional user information from the database
$userInfo = []; // Initialize as empty array

try {
    // Adjust SQL based on your users table structure
    $sql = "SELECT email, first_name, last_name FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user_id"]]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error appropriately
    error_log("Error fetching user information: " . $e->getMessage());
    // Consider showing an error message or handling differently
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"] ?? 'User'); ?></b>. Welcome to our site.</h1>
        
        <!-- Display user information if fetched -->
        <?php if (!empty($userInfo)): ?>
            <p>Email: <?php echo htmlspecialchars($userInfo['email']); ?></p>
            <!-- Example: Display user's full name if available -->
            <?php if (!empty($userInfo['first_name']) && !empty($userInfo['last_name'])): ?>
                <p>Name: <?php echo htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']); ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- User Actions -->
        <p>
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
            <a href="profile.php" class="btn btn-primary">My Profile</a> <!-- Link to user's profile -->
            <a href="change_password.php" class="btn btn-warning">Change Password</a> <!-- Link to change password page -->
        </p>
        
        <!-- Game or Site Navigation -->
        <p>
        <a href="../public/game/start.php" class="btn btn-success">Play Game</a> <!-- Link to the game page -->
            <a href="leaderboard.php" class="btn btn-info">Leaderboard</a> <!-- Link to the leaderboard -->
            <!-- Additional links to different sections of the site can be added here -->
        </p>
    </div>
</body>
</html>

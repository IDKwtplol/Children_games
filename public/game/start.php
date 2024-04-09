<?php
require_once __DIR__ . '/../../config.php'; 
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login.php');
    exit;
}

// Function to check for an ongoing game
function checkOngoingGame($pdo, $userId) {
    $sql = "SELECT id, level, lives FROM game_sessions WHERE user_id = ? AND status = 'in_progress' ORDER BY created_at DESC LIMIT 1";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Directly return the fetch
    } catch (PDOException $e) {
        error_log('Error querying ongoing game: ' . $e->getMessage());
        return false; // Return false in case of error
    }
}

// Example usage
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login.php');
    exit;
}

// Checking for ongoing game
$userGame = checkOngoingGame($pdo, $_SESSION['user_id']);

if ($userGame) {
    // Load the game state into the session if an ongoing game is found
    $_SESSION['game_id'] = $userGame['id'];
    $_SESSION['level'] = $userGame['level'];
    $_SESSION['lives'] = $userGame['lives'];
} else {
    // Initialize or reset the game state if no ongoing game is found
    $_SESSION['game_id'] = null; // Ensuring a new game can be differentiated
    $_SESSION['level'] = 1;
    $_SESSION['lives'] = 6;
}

header('Location: level.php');
exit;





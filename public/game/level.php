<?php
require_once __DIR__ . '/../../config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
    // If the user is not logged in or the level is not set, redirect to the start or login page.
    header('Location: /game/start.php');
    exit;
}

$level = $_SESSION['level'];
$lives = $_SESSION['lives'];
$feedback = $_SESSION['feedback'] ?? ''; // Feedback from the previous attempt, if any.

// Function to generate game content and instructions based on the level.
function generateGameContent($level, &$instructions) {
    $content = [];
    switch ($level) {
        case 1:
            $instructions = "Order the following letters in ascending order:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            break;
        case 2:
            $instructions = "Order the following letters in descending order:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            rsort($content);
            break;
        case 3:
            $instructions = "Order the following numbers in ascending order:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            break;
        case 4:
            $instructions = "Order the following numbers in descending order:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            rsort($content);
            break;
        case 5:
            $instructions = "Identify the first (smallest) and last (largest) letter in the set:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            break;
        case 6:
            $instructions = "Identify the smallest and the largest number in the set:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            break;
    }
    return $content;
}

$instructions = ""; // Variable to hold instructions
$content = generateGameContent($level, $instructions);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level <?= htmlspecialchars($level) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles can be added here */
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Level <?= htmlspecialchars($level) ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">Lives: <?= htmlspecialchars($lives) ?></p>
                        <?php if (!empty($feedback)) : ?>
                            <div class="alert alert-warning mb-3" role="alert">
                                <?= htmlspecialchars($feedback) ?>
                            </div>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($instructions) ?></p>
                            <!-- Displaying the items to be arranged directly -->
                            <p><strong>Items:</strong> <?= implode(", ", $content); ?></p>
                        <form action="process.php" method="post">
                            <?php foreach ($content as $index => $item) : ?>
                                <div class="form-group">
                                    <!-- Display content as input fields for user response -->
                                    <input type="text" name="answers[]" value="" placeholder="<?= htmlspecialchars($item) ?>" required />
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary">Submit Answer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


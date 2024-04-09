<?php
require_once __DIR__ . "/../../config.php"; // Adjust the path based on the script's location

session_start();

if (!isset($_SESSION["user_id"], $_SESSION["level"], $_SESSION["lives"])) {
    // Redirect user if essential session variables are missing
    header("Location: /game/start.php");
    exit();
}

// Function to check if the answer is correct
function isAnswerCorrect($level, $userAnswers)
{
    $correctAnswers = []; // Initialize the correct answers array

    switch ($level) {
        case 1:
            // Level 1: Order 6 letters in ascending order
            $correctAnswers = range("a", "f"); // Example set for simplicity
            sort($userAnswers, SORT_STRING);
            break;
        case 2:
            // Level 2: Order 6 letters in descending order
            $correctAnswers = range("a", "f");
            rsort($correctAnswers, SORT_STRING);
            rsort($userAnswers, SORT_STRING);
            break;
        case 3:
            // Level 3: Order 6 numbers in ascending order
            $correctAnswers = range(1, 6); // Example set
            sort($userAnswers, SORT_NUMERIC);
            break;
        case 4:
            // Level 4: Order 6 numbers in descending order
            $correctAnswers = range(1, 6);
            rsort($correctAnswers, SORT_NUMERIC);
            rsort($userAnswers, SORT_NUMERIC);
            break;
        case 5:
            // Level 5: Identify the smallest and largest letters
            $correctAnswers = ["a", "f"]; // Assuming a predefined set for simplicity
            // No sort needed here, as we directly compare to predefined smallest and largest
            break;
        case 6:
            // Level 6: Identify the smallest and largest numbers
            $correctAnswers = [1, 6]; // Assuming a predefined set for simplicity
            // No sort needed here, as we directly compare to predefined smallest and largest
            break;
    }

    // Levels 5 and 6 require exactly two answers; others require answers matching the correctAnswers array's length
    if (in_array($level, [5, 6])) {
        return count($userAnswers) === 2 && $userAnswers === $correctAnswers;
    } else {
        // Ensure user provides the correct number of answers for levels 1-4 and those answers match the correct sequence
        return count($userAnswers) === count($correctAnswers) &&
            $userAnswers === $correctAnswers;
    }
}

// Function to update game progress in database
function updateGameProgress(
    $pdo,
    $userId,
    $level,
    $lives,
    $status = "in_progress"
) {
    $sql =
        "UPDATE game_sessions SET level = :level, lives = :lives, status = :status WHERE user_id = :user_id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":level" => $level,
            ":lives" => $lives,
            ":status" => $status,
            ":user_id" => $userId,
        ]);
    } catch (PDOException $e) {
        // Error handling
        error_log("Failed to update game progress: " . $e->getMessage());
        // Consider how to handle errors: display a message, retry, etc.
    }
}

// Assuming answers are submitted as an array named 'answers'
if (isset($_POST["answers"]) && is_array($_POST["answers"])) {
    $userAnswers = $_POST["answers"];
    $level = $_SESSION["level"];

    // Placeholder for sorting or otherwise preparing answers for checking
    // For example, if it's a sorting game, you might sort the answers
    sort($userAnswers, SORT_STRING); // Sort numerically or alphabetically as required

    if (isAnswerCorrect($level, $userAnswers)) {
        // If the answer is correct, advance to the next level or mark game as completed
        if ($level >= 6) {
            // Assuming 6 is the last level
            $_SESSION["level"] = 1; // Reset or adjust according to your game's logic
            $_SESSION["lives"] = 6; // Reset lives or adjust
            updateGameProgress(
                $pdo,
                $_SESSION["user_id"],
                $level,
                $_SESSION["lives"],
                "completed"
            );
            $feedback = "Congratulations, you've completed the game!";
        } else {
            $_SESSION["level"]++;
            updateGameProgress(
                $pdo,
                $_SESSION["user_id"],
                $level,
                $_SESSION["lives"]
            );
            $feedback = "Correct! Moving to the next level.";
        }
    } else {
        // If the answer is incorrect, deduct a life or reset the game if out of lives
        $_SESSION["lives"]--;
        if ($_SESSION["lives"] <= 0) {
            $_SESSION["level"] = 1; // Reset or adjust according to your game's logic
            $_SESSION["lives"] = 6; // Reset lives or adjust
            updateGameProgress($pdo, $_SESSION["user_id"], $level, 0, "failed");
            $feedback = "Game Over. You've run out of lives.";
        } else {
            updateGameProgress(
                $pdo,
                $_SESSION["user_id"],
                $level,
                $_SESSION["lives"]
            );
            $feedback = "Incorrect. Please try again.";
        }
    }

    // Store feedback in session or another mechanism to display it to the user
    $_SESSION["feedback"] = $feedback;

    // Redirect back to the game level page or to a feedback page
    header("Location: level.php");
    exit();
} else {
    // If no answers were submitted, redirect to the game start or an error page
    header("Location: /game/start.php");
    exit();
}

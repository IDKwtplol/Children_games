<?php
// src/Game.php
class Game {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function initializeGame($userId) {
        // Set up the initial game state for the user
        // You would typically reset the game level and lives in the database
        // For the purpose of the example, we're assuming a table `game_state` exists
        $stmt = $this->db->prepare("INSERT INTO game_state (user_id, level, lives) VALUES (:user_id, 1, 3) ON DUPLICATE KEY UPDATE level = 1, lives = 3");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    public function checkAnswer($userId, $level, $userAnswer) {
        // Check the user's answer against the correct one stored in the database
        // Assuming a table `levels` exists with correct answers for each level
        $stmt = $this->db->prepare("SELECT correct_answer FROM levels WHERE level = :level");
        $stmt->bindParam(':level', $level);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $userAnswer == $result['correct_answer']) {
            $this->advanceLevel($userId);
            return true;
        } else {
            $this->decrementLives($userId);
            return false;
        }
    }

    private function advanceLevel($userId) {
        // Advance the user to the next level
        $stmt = $this->db->prepare("UPDATE game_state SET level = level + 1 WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    private function decrementLives($userId) {
        // Decrement the user's remaining lives
        $stmt = $this->db->prepare("UPDATE game_state SET lives = lives - 1 WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    // ... Additional methods as needed
}
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

// Database connection
require 'db.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $game_team_id = $_POST['game_team_id'];
        $card_id = $_POST['card_id'];

        // Insert new clothing for the person
        $stmt = $pdo->prepare("INSERT INTO game_team_player (game_team_id, card_id) VALUES (:game_team_id, :card_id)");
        $stmt->execute(['game_team_id' => $game_team_id, 'card_id' => $card_id]);

        echo "Card added to team successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

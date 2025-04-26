<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the team
    $stmt = $pdo->prepare("DELETE FROM game_team WHERE game_team_id = ?");
    $stmt->execute([$id]);
}

header('Location: game_teams.php');
exit;
?>

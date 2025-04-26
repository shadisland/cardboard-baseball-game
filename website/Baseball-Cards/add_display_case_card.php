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
        $display_case_id = $_POST['display_case_id'];
        $card_id = $_POST['card_id'];

        // Insert new clothing for the person
        $stmt = $pdo->prepare("INSERT INTO display_case_cards (display_case_id, card_id) VALUES (:display_case_id, :card_id)");
        $stmt->execute(['display_case_id' => $display_case_id, 'card_id' => $card_id]);

        echo "Card added to display case successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

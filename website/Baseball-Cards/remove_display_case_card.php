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

        // Remove card from the display case
        $stmt = $pdo->prepare("DELETE FROM display_case_cards WHERE display_case_id = :display_case_id AND card_id = :card_id");
        $stmt->execute(['display_case_id' => $display_case_id, 'card_id' => $card_id]);

        echo "Card removed successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

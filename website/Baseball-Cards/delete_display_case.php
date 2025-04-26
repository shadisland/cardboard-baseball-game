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
        
        $display_case_id = $_POST['dc'];
        

        // Update display case name
        $stmt = $pdo->prepare("DELETE FROM display_case WHERE display_case_id = :display_case_id");
        $stmt->execute(['display_case_id' => $display_case_id]);

        echo "Display case deleted successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

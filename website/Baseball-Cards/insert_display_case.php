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
        
        $display_case_name = $_POST['display_case_name'];
        $display_case_color = $_POST['display_case_color'];
	$display_case_grid_size = $_POST['display_case_grid_size'];
	$display_case_font = $_POST['display_case_font'];
	$display_case_font_color = $_POST['display_case_font_color'];
	$display_case_font_shadow = $_POST['display_case_font_shadow'];

        // Update display case name
        $stmt = $pdo->prepare("INSERT INTO display_case (display_case_name, display_case_color, display_case_grid_size, display_case_font, display_case_font_color, display_case_font_shadow) VALUES( :display_case_name, :display_case_color, :display_case_grid_size, :display_case_font, :display_case_font_color, :display_case_font_shadow )");
        $stmt->execute(['display_case_name' => $display_case_name, 'display_case_color' => $display_case_color, 'display_case_grid_size' => $display_case_grid_size, 'display_case_font' => $display_case_font, 'display_case_font_color' => $display_case_font_color, 'display_case_font_shadow' => $display_case_font_shadow]);

        echo "Display case inserted successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

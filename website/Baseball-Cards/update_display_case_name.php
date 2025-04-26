<?php
// Database connection
$host = 'p3nlmysql39plsk.secureserver.net';
    	$db   = 'ph21100054196_';
    	$user = 'collector';
    	$pass = 'Piltocat22';
    	$port = "3306";
    	$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";


try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $display_case_id = $_POST['display_case_id'];
        $display_case_name = $_POST['display_case_name'];
        $display_case_color = $_POST['display_case_color'];
        $display_case_grid_size = $_POST['display_case_grid_size'];
        $display_case_font = $_POST['display_case_font'];
        $display_case_font_color = $_POST['display_case_font_color'];
        $display_case_font_shadow = $_POST['display_case_font_shadow'];

        // Update display case name
        $stmt = $pdo->prepare("UPDATE display_case SET display_case_name = :display_case_name, display_case_grid_size = :display_case_grid_size, display_case_color = :display_case_color, display_case_font = :display_case_font, display_case_font_color = :display_case_font_color, display_case_font_shadow = :display_case_font_shadow WHERE display_case_id = :display_case_id");
        $stmt->execute(['display_case_name' => $display_case_name, 'display_case_grid_size' => $display_case_grid_size, 'display_case_color' => $display_case_color, 'display_case_font' => $display_case_font, 'display_case_font_color' => $display_case_font_color, 'display_case_font_shadow' => $display_case_font_shadow ,'display_case_id' => $display_case_id]);

        echo "Display case name, color and grid size updated successfully";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

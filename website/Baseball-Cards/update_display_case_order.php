<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

// Database connection
require 'db.php';
	
try {
    $conn = new \PDO($dsn, $user, $pass);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

	// Prepare and bind     
	$sortedIDs = $_POST['sortedIDs'];
	foreach ($sortedIDs as $index => $display_case_id) {
		$myctr = $index + 1;
		$stmt2 = $conn->prepare("UPDATE display_case SET display_case_sort_order = :display_case_sort_order WHERE display_case_id = :display_case_id");
		$stmt2->bindParam(':display_case_sort_order', $myctr, PDO::PARAM_INT);
		$stmt2->bindParam(':display_case_id', $display_case_id, \PDO::PARAM_INT);
		$stmt2->execute();
	}
        echo json_encode(["success" => true]);
} catch (\PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}     

$conn = null;
?>

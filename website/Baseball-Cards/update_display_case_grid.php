<?php
//update_display_case_grid.php?grid=8

// Database connection
$host = 'p3nlmysql39plsk.secureserver.net';
	$db   = 'ph21100054196_';
	$user = 'collector';
	$pass = 'Piltocat22';
	$port = "3306";
	$charset = 'utf8mb4';

	$options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,\PDO::ATTR_EMULATE_PREPARES => false,];

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
	// $pdo = new \PDO($dsn, $user, $pass, $options);

    $conn = new \PDO($dsn, $user, $pass);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

// Prepare and bind     
$stmt2 = $conn->prepare("UPDATE display_case SET display_case_grid_size = :display_case_grid_size WHERE display_case_id = :display_case_id");

// Set parameters and execute
$display_case_grid_size = 0;
$display_case_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$display_case_grid_size = $_POST['grid'];
	$display_case_id = $_POST['dc'];
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$display_case_grid_size = $_GET['grid'];
	$display_case_id = $_GET['dc'];
}
$stmt2->bindParam(':display_case_grid_size', $display_case_grid_size, \PDO::PARAM_STR);
$stmt2->bindParam(':display_case_id', $display_case_id, \PDO::PARAM_INT);

if ($stmt2->execute()) {
    echo "Record updated successfully: id is " . $display_case_id . " display_case_grid_size is " . $display_case_grid_size ;
} else {
    echo "Error: " . $stmt2->error;
}

$conn = null;
?>



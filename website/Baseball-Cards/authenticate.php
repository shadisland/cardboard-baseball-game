<?php
session_start();

header('Content-Type: application/json');

// Database connection
require 'db.php';

$auth_response = ['success' => false, 'message' => 'Invalid username or password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
	$stmt->execute([$username]);
	$user = $stmt->fetch();

	if ($user && password_verify($password, $user['password'])) {
		// Login successful
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];
		$_SESSION['user_role'] = $user['role'];
		$auth_response['success'] = true;
		$auth_response['message'] = 'Login successful';
	}
	echo json_encode($auth_response);
} else {
	echo 'Invalid request';
}

?>

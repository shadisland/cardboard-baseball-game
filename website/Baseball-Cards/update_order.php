<?php
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

try {
    $conn = new \PDO($dsn, $user, $pass);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sortedIDs = $_POST['sortedIDs'];
        $DisplayCaseId = $_POST['dc'];
        foreach ($sortedIDs as $index => $id) {
          $myctr = $index + 1;
            $stmt = $conn->prepare("UPDATE display_case_cards SET dcc_sort_order = :CardOrder WHERE display_case_id = :DisplayCaseId AND card_id = :CardID");
             $stmt->bindParam(':CardOrder', $myctr, PDO::PARAM_INT);
            $stmt->bindParam(':DisplayCaseId', $DisplayCaseId, PDO::PARAM_INT);
            $stmt->bindParam(':CardID', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        echo json_encode(["success" => true]);
    }
} catch (\PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>

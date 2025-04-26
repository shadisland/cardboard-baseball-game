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

	// Prepare and bind     
	$sortedIDs = $_POST['sortedIDs'];
	$gameTeamID = $_POST['gameTeamID'];
	foreach ($sortedIDs as $index => $card_id) {
		$myctr = $index + 1;
		$stmt2 = $conn->prepare("UPDATE game_team_player SET batting_order = :batting_order WHERE card_id = :card_id and game_team_id = :game_team_id");
		$stmt2->bindParam(':batting_order', $myctr, PDO::PARAM_INT);
		$stmt2->bindParam(':card_id', $card_id, \PDO::PARAM_INT);
		$stmt2->bindParam(':game_team_id', $gameTeamID, \PDO::PARAM_INT);
		$stmt2->execute();
	}
        echo json_encode(["success" => true]);
} catch (\PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}     

$conn = null;
?>

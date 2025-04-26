<?php
echo 'here 0';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'error' => ''];
    $uploadDir = 'images/team-logos/';


// Database connection
$host = 'p3nlmysql39plsk.secureserver.net';
	$db   = 'ph21100054196_';
	$user = 'collector';
	$pass = 'Piltocat22';
	$port = "3306";
	$charset = 'utf8mb4';

	$options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,\PDO::ATTR_EMULATE_PREPARES => false,];

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
	
//--------------
    // Check if name is set
    if (empty($_POST['team_name'])) {
        $response['error'] = 'Team name is required.';
        echo json_encode($response);
        //exit;
    }
    $teamName = htmlspecialchars($_POST['team_name']);
echo 'here 1';
    // Validate image upload
    if (!empty($_FILES['image']['name'])) {
        $fileName = basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (($fileType === 'jpg' || $fileType === 'png') && $_FILES['image']['size'] <= 1548576) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                // Save to database
                try {
                	$conn = new \PDO($dsn, $user, $pass);
    			$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    //$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                    //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $stmt = $conn->prepare('UPDATE game_team SET team_name = :teamName, :logo_url = :logo_url');
                    $stmt->execute(['teamName' => $teamName, 'logo_url' => $filePath]);

                    $response['success'] = true;
           echo 'Success';
                } catch (PDOException $e) {
              echo 'Database error: ' . $e->getMessage();
                    $response['error'] = 'Database error: ' . $e->getMessage();
                }
            } else {
       echo 'Failed to upload image.';
                $response['error'] = 'Failed to upload image.';
            }
        } else {
       echo 'Invalid image file.';
            $response['error'] = 'Invalid image file.';
        }
    } else {
    	echo 'Image required';
        $response['error'] = 'Image is required.';
    }

    echo json_encode($response);
} else {
	echo 'POST required';
}
//exit;
?>

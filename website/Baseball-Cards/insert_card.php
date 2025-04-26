<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

// Database connection
require 'db.php';

    $conn = new \PDO($dsn, $user, $pass);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$image_sub_folder = $_POST['image_sub_folder'];
$uploadDir = '../images/Baseball-Cards/' . $image_sub_folder . '/';
$fileName = basename($_FILES['image_upload']['name']);
$filePath = $uploadDir . $fileName;
$fileType = pathinfo($filePath, PATHINFO_EXTENSION);
$fileNameBack = basename($_FILES['image_upload_back']['name']);
$filePathBack = $uploadDir . $fileNameBack;
$fileTypeBack = pathinfo($filePathBack, PATHINFO_EXTENSION);
$grade_company = 'raw';
if( strpos($fileName, 'SGC') > -1) {
	$grade_company = 'SGC';
} else if( strpos($fileName, 'PSA') > -1) {
	$grade_company = 'PSA';
}
if( !empty( $_FILES['image_upload']['name'] ) ) {
	if (($fileType === 'jpg') && $_FILES['image_upload']['size'] <= 1548576) {
		if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $filePath)) {
			// 		
			//echo ' Saved FRONT fileto ' . $filePath;
		} else {
			echo " Failed to save FRONT file";
		}	
	} else {
		//invalid file
		echo " invalid FRONT file type or size";
	}
} 
if( !empty( $_FILES['image_upload_back']['name'] ) ) {
	if (($fileTypeBack === 'jpg') && $_FILES['image_upload_back']['size'] <= 1548576) {
		if (move_uploaded_file($_FILES['image_upload_back']['tmp_name'], $filePathBack)) {
			// 		
			//echo ' Saved BACK fileto ' . $filePathBack;
		} else {
			echo " Failed to save BACK file";
		}	
	} else {
		//invalid file
		echo " invalid BACK file type or size";
	}
} 
// This insert requires an SGC style, encoded image name
//like this: '1967_Topps_210_Bob_Gibson__SGC-Grade-5_Auth-3146996_Front.jpg'
//Pull info from the encoded filename
$img_url = $image_sub_folder . '/' . $fileName;
$img_back_url = $image_sub_folder . '/' . $fileNameBack;

// Prepare and bind     
$stmt = $conn->prepare("INSERT INTO card (year, brand, number, player_name, img_url, img_back_url, grade_company, grade, grade_serial, i_own_it) VALUES (:year, :brand, :number, :player_name, :img_url, :img_back_url, :grade_company, :grade, :grade_serial, :i_own_it)");

// Set parameters and execute
$field1 = $_POST['year'];
$field2 = $_POST['brand'];
$field3 = $_POST['number'];
$field4 = $_POST['player_name'];
$field5 = $img_url;
$field6 = $img_back_url;
$field7 = $_POST['grade_company'];
$field8 = $_POST['grade'];
$field9 = $_POST['grade_serial'];
$field10 = $_POST['i_own_it'];

 $stmt->bindParam(':year', $field1, \PDO::PARAM_INT);
 $stmt->bindParam(':brand', $field2, \PDO::PARAM_STR);
 $stmt->bindParam(':number', $field3, \PDO::PARAM_INT);
 $stmt->bindParam(':player_name', $field4, \PDO::PARAM_STR);
 $stmt->bindParam(':img_url', $field5, \PDO::PARAM_STR);
 $stmt->bindParam(':img_back_url', $field6, \PDO::PARAM_STR);
 $stmt->bindParam(':grade_company', $field7, \PDO::PARAM_STR);
 $stmt->bindParam(':grade', $field8, \PDO::PARAM_STR);
 $stmt->bindParam(':grade_serial', $field9, \PDO::PARAM_INT);
 $stmt->bindParam(':i_own_it', $field10, \PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

 $conn = null;

?>

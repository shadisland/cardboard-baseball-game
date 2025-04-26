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
	    
//Page requires GET['company'] (PSA, SGC, raw) and GET['hasback'] (1 or 0)
	    
//Attempt to parse values from image_url
//SGC-309-web/
$imgs_array = [
'raw-web/1982_Topps_90_Nolan Ryan__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1977_Topps_42_Jose Cruz__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1974_Topps_200_Cesar Cedano__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1974_Topps_593_Steve Yeager__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1974_Topps_293_Milt May__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1974_Topps_500_Lee May__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1983_Topps_478_Phil Garner__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1981_Topps_411_Terry Puhl__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1983_Topps_498_Wade Boggs__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1982_Topps_668_Dale Murphy__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1983_Topps_275_Ray Knight__raw-Grade-TBD_Auth-11_Front.jpg',
'raw-web/1960_Topps_205_Johnny Logan__raw-Grade-TBD_Auth-11_Front.jpg'
];



foreach ($imgs_array as $img_url):

// This insert requires an SGG formatted image URL
//like this ''SGC-016-web/1967_Topps_210_Bob_Gibson__SGC-Grade-5_Auth-3146996_Front.jpg','
//    'SGC-016-web/1969_Topps_8_NL_Era_Leaders__SGC-Grade-5_Auth-5750685_Front.jpg',<br/>
$year = substr( $img_url, strpos($img_url, '/') + 1, 4 );
$split_array = explode('_', $img_url);
$brand = $split_array[1];
$number = $split_array[2];

//$grade_company = 'SGC';
$grade_company = $_GET['company'];
$has_image_back = $_GET['hasback'];


$player_name = str_replace("_", " ", 
substr( $img_url, 
strpos($img_url, $number) + strlen($number) + 1, 
strpos($img_url, "__" . $grade_company) 
- strpos($img_url, $number) -  1 - strlen($number)) );

//raw-web/1977_Topps_60_Jim_Rice__raw-Grade-TBD_Auth-11_Front.jpg
//strpos($img_url, $number) = 19 + 41 + 1 = 61
//strpos($img_url, "__" . $grade_company) = 
//substr( $img_url, start at 60, length is 

//$player_name = str_replace("_", " ", substr( $img_url, strpos($img_url, $number) + strlen($number) + 1, strpos($img_url, '__' . $grade_company) - strpos($img_url, $number) - 4)); 

$grade = substr( $img_url, strpos($img_url, 'Grade-') + 6, strpos($img_url, '_Auth-') - strpos($img_url, 'Grade-') - 6 ); //substr( $split_array[4], strpos($split_array[4], 'Grade-') + 1 );
$grade_serial = substr( $img_url, strpos($img_url, 'Auth-') + 5, strpos($img_url, '_Front') -  strpos($img_url, 'Auth-') - 5);
$i_own_it = 1;

if( $has_image_back == 1 ) {
	$img_back_url = str_replace("Front", "Back", $img_url);
} else {
	$img_back_url = "default.jpg";
}

// Prepare and bind    
$stmt = $conn->prepare("INSERT INTO card (year, brand, number, player_name, img_url, img_back_url, grade_company, grade, grade_serial, i_own_it) VALUES (:year, :brand, :number, :player_name, :img_url, :img_back_url, :grade_company, :grade, :grade_serial, :i_own_it)");

// Set parameters and execute
$field1 = $year;
$field2 = $brand;
$field3 = $number;
$field4 = $player_name;
$field5 = $img_url;
$field6 = $img_back_url;
$field7 = $grade_company;
$field8 = $grade;
$field9 = $grade_serial;
$field10 = $i_own_it;

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
    echo 'Card inserted: ' . $player_name . '<br/><br/>';
} else {
    echo "Error: " . $stmt->error . '<br/><br/>' ;
}

//TODO Insert a record into card_stats
//retrieve card_id's and batter or pitcher (can add batter_or_pitcher to GET[]
//Or make a stored procedure to do it automatically (but without batter/pitcher)
//INSERT INTO card_stats (card_id)
//VALUES SELECT distinct c.card_id from card c
//        WHERE img_back_url <> "default.jpg"
//        AND c.card_id not in (select cs2.card_id from card_stats cs2)
endforeach;
 $conn = null;

?>


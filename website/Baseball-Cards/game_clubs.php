<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

   
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = $_POST['club_name'];

    // Insert a new season
    $stmt = $pdo->prepare("INSERT INTO game_club (club_name) VALUES (?)");
    $stmt->execute([$club_name]);

    header('Location: game_clubs.php');
    exit;
}

// Fetch all game clubs
$stmt = $pdo->query('SELECT * FROM game_club where game_club_id > 0 ORDER by 1');
$clubs = $stmt->fetchAll();

 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cardboard Baseball Clubs</title>
     <?php require 'head_scripts_include.php';?>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
     <style>     
     	.display-container {
     		box-shadow: 0 0 0 3px #0C2340;
     		border: 7px solid #BD3039 !important; 
     		border-radius: 5px !important; 
     		outline: 3px solid #0C2340; 
     		outline-offset: -10px; 
     		background-color: rgba(255,255,255,0.7) !important;
     		width: 540px;
     		margin-top: 20px !important; 
     		padding-bottom: 20px !important;
	}
     </style>
</head>
<body>
<?php require 'header.php';?> 
<div id="game-form-wrapper">

</div>
<div id="green-space">
    &nbsp;
</div>   
<div id="game-form" style="padding-left: 5px;">

<?php require 'game_nav.php'; ?>
     
	<div id="width-wrapper" style="position: relative; width: 900px; display: grid; grid-template-columns: 1fr 1fr;">
		<div id="teams-table-instructions" style="width: 370px; height: 50px; margin: 20px 0px 0px 15px !important;">
			Click a Club Name to View/Edit<br>
		
			<div class="display-container" style="width: 375px; margin-top: 10px !important;">
				<h3 style="margin-top: 10px; text-align: center; font-size: 25px; color: #0C2340; text-shadow: 1px 1px 1px #BD3039;">Clubs</h3>
				<table id=" id="seasons-table" " style="width: 335px; border-collapse: collapse; border: 0px solid gray; margin: 10px 0px 0px 20px !important; font-size: 16px;">
					<tr style="background-color: rgba(84, 121, 109, 0.65);">
					    <th style="border-top-left-radius: 17px; text-shadow: 0px 0px 0px white; font-weight: normal; font-size: 20px;">ID</th>
					    <th style="border-top-right-radius: 17px;text-shadow: 0px 0px 0px white;">Name</th>
					</tr> 
				<?php foreach ($clubs as $club): ?>
					<tr style="background-color: rgba(1, 50, 32, 0.2);">
					    <td style="text-shadow: 0px 0px 0px white; font-weight: normal; font-size: 20px;"><?= $club['game_club_id']; ?>
					    </td>
					    <td><a class="underlined-link" href="game_club_edit.php?id=<?= $club['game_club_id']; ?>"><?= htmlspecialchars($club['club_name']); ?></a>
					    </td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
<?php
	if( $_SESSION['user_role'] == 'admin' ){
?>		
		<div class="display-container" style="width: 340px; margin-top:53px !important; padding: 0px 0px 0px 15px;">
			<h3 style="margin-top: 10px; text-align: center; font-size: 25px; color: #0C2340; text-shadow: 1px 1px 1px #BD3039;">Create A New Club</h3>
			<form method="POST">	
				<label for="club_name" style="font-weight: bold;">Name:</label>
				<input type="text" style="width: 125px;" name="club_name" required>
				 <label></label>
				 <input type="submit" class="link-button" style="margin-bottom: 10px;" value="New Club">
				 <br>
				 <p>All Teams need to belong to a Club and to a Season in order to play. The Club can have one team in each Season.</p>
			</form>
		</div>   
<?php
	}
?>		 
		<style>
		.copyright {
			margin-top: 650px !important;
		}
		</style>
 <?php require 'footer.php';?> 
	</div>
</div>
</body>
</html>

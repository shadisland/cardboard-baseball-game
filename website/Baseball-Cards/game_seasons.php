<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

   
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $season_name = $_POST['season_name'];

    // Insert a new season
    $stmt = $pdo->prepare("INSERT INTO game_season (season_name) VALUES (?)");
    $stmt->execute([$season_name]);

    header('Location: game_seasons.php');
    exit;
}

// Fetch all game seasons
if( $_SESSION['user_role'] == 'admin' ){
	$stmt = $pdo->query('SELECT * FROM game_season where game_season_id > 0 ORDER by 1');
	$seasons = $stmt->fetchAll();
} else if( $_SESSION['user_role'] == 'user' ){
	$stmt = $pdo->query('SELECT * FROM game_season where access_level = \'user\' ORDER by 1');
	$seasons = $stmt->fetchAll();}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cardboard Baseball Seasons</title>
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

	<div id="width-wrapper" style="position: relative; width: 700px; display: grid; grid-template-columns: 1fr 1fr;">
		<div id="teams-table-instructions" style="width: 280px; height: 50px; margin: 20px 0px 0px 15px !important;">
			Click a Season Name to View/Edit<br>
		
			<div class="display-container" style="width: 250px !important; margin-top: 10px !important;">
				<h3 style="margin-top: 10px; text-align: center; font-size: 25px; color: #0C2340; text-shadow: 1px 1px 1px #BD3039;">Seasons</h3>
				<table id=" id="seasons-table" " style="width: 200px; border-collapse: collapse; border: 0px solid gray; margin: 10px 0px 0px 20px !important; font-size: 16px;">
					<tr style="background-color: rgba(84, 121, 109, 0.65);">
					    <th style="border-top-left-radius: 17px; font-weight: normal; font-size: 20px; text-shadow: 0px 0px 0px white;">ID</th>
					    <th style="text-shadow: 0px 0px 0px white; border-top-right-radius: 17px;">Name</th>
					</tr>
				<?php foreach ($seasons as $season): ?>
					<tr style="background-color: rgba(1, 50, 32, 0.2);">
					    <td style="font-weight: normal; font-size: 20px;"><?= $season['game_season_id']; ?>
					    </td>
					    <td><a class="underlined-link" href="game_season_edit.php?id=<?= $season['game_season_id']; ?>"><?= htmlspecialchars($season['season_name']); ?></a>
					    </td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
<?php
	if( $_SESSION['user_role'] == 'admin' ){
?>
		<div class="display-container" style="width: 340px !important; margin-top:53px !important; padding: 0px 0px 0px 15px;">
		<h3 style="margin-top: 10px; text-align: center; font-size: 25px; color: #0C2340; text-shadow: 1px 1px 1px #BD3039;">Create A New Season</h3>
		<form method="POST">	
			<label for="season_name" style="font-weight: bold;">Name:</label>
			<input type="text" style="width: 125px;" name="season_name" required>
			 <label></label>
			 <input type="submit" class="link-button" style="margin-bottom: 10px;" value="New Season">
		</form>
		</div>  
<?php
	}
?>
	<style>
	.copyright {
		margin-top: 320px !important;
	}
	</style>
	 <?php require 'footer.php';?>   
	</div>
</div>
</body>
</html>

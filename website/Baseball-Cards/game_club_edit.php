<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

if ( !isset($_GET['id']) && !isset($_POST['id']) ) {
    header('Location: game_clubs.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Card Game Club</title>
     <?php require 'head_scripts_include.php';?>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
</head>
<body>
<?php require 'header.php';?>

<?php

//Requires game_club_id as GET['id']
$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	//if 'Update Club' was clicked, update the fields
	if( isset( $_POST['club_name'] ) ) {
	    $club_name = $_POST['club_name'];

	    // Update team information
	    $stmt = $pdo->prepare("UPDATE game_club
	    				SET club_name = ? 
	    				WHERE game_club_id = ?");
	    $stmt->execute([$club_name, $id]);	
	    header('Location: game_clubs.php');
	    exit;
    	} 
    		
}

// Fetch the club data
$stmt = $pdo->prepare("SELECT * FROM game_club WHERE game_club_id = ?");
$stmt->execute([$id]);
$club = $stmt->fetch();

// Get all current teams for the season
$stmt2 = $pdo->prepare("SELECT gt.*, gs.*
			FROM game_team gt, game_season gs
			WHERE gt.game_club_id = ? 
			AND gt.game_season_id = gs.game_season_id
			ORDER BY 1 ASC ");
$stmt2->execute([$id]);
$club_teams = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$stmt2->closeCursor();

$stmt->closeCursor();

?>

<div id="game-form-wrapper">
</div>
<div id="green-space">
    &nbsp;
</div>
<div id="game-form">

<?php require 'game_nav.php'; ?>

	<div id="width-wrapper" style="width: 850px;">
<?php
	if( $_SESSION['user_role'] == 'admin' ){
?>
		<div id="season-div">
			<h3 style="text-align: center;">Update <span style="text-shadow: 2px 2px 2px #BD3039;"><?= htmlspecialchars($club['club_name']); ?></span> Club</h3>
			<div id="game-club" >
				<form method="POST">
					<label for="club_name">Club Name:</label>
					<input type="text" style="width: 150px;" name="club_name" value="<?= htmlspecialchars($club['club_name']); ?>" required>
					<br>
					<br>
					<input type="submit" class="link-button" value="Update Club">
				</form>
			</div>
		 </div>
<?php
	}
?>		 
		 <div id="season-teams">
			<h3><span style="text-shadow: 2px 2px 2px #BD3039;"><?= htmlspecialchars($club['club_name']); ?></span> Club Teams</h3>
			<table id="team-table" style="">
				<tr>
					<th>ID</th>
					<th>Team Name</th>
					<th>Season</th>
					<th>Logo</th>

				</tr>

			<?php 	
				foreach ($club_teams as $club_team): ?> 			
					<td><?= $club_team['game_team_id']; ?>
					</td>
					<td>
				<?php
					if( $_SESSION['user_role'] == 'admin' || $club_team['game_season_id'] == 1 ){
				?>
							<a href="game_team_edit.php?id=<?= $club_team['game_team_id']; ?>" style="text-decoration: underline;"><?= $club_team['team_name']; ?></a>
				<?php
					} else{
				?>
							<?= $club_team['team_name']; ?>
				<?php
					}
				?>
					</td>
					<td><?= $club_team['season_name']; ?>
					</td>
					<td><img style="max-height: 75px;" src="<?= $club_team['logo_url']; ?>">
					</td>

				</tr>

			<?php endforeach; ?>
			</table>
		 </div>
    
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


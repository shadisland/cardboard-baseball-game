<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

if ( !isset($_GET['id']) && !isset($_POST['id']) ) {
    header('Location: game_seasons.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Season</title>
     <?php require 'head_scripts_include.php';?>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
</head>
<body>
<?php require 'header.php';?>

<?php

//Requires game_season_id as GET['id']
$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	//if 'Update Season' was clicked, update the fields
	if( isset( $_POST['season_name'] ) ) {
		$season_name = $_POST['season_name'];
		$access_level = 'user';
		if( isset( $_POST['role_visibility'] ) ) {
			//admin is able to set the access_level
			$access_level = $_POST['role_visibility'];
		}
	    	// Update team information
	    	$stmt = $pdo->prepare("UPDATE game_season 
	    				SET season_name = ? ,
	    				access_level = ?
	    				WHERE game_season_id = ?");
	    $stmt->execute([$season_name, $access_level, $id]);	
	    header('Location: game_seasons.php');
	    exit;
    	} else if( isset( $_POST['game_team_id'] ) ) {
    		// Add a team to the season
    		$game_team_id = $_POST['game_team_id'];
		$stmt = $pdo->prepare("UPDATE game_team SET game_season_id = ? WHERE game_team_id = ? ");
		$stmt->execute([$id, $game_team_id]);
    	}
    		
}

// Fetch the current season data
$stmt = $pdo->prepare("SELECT * FROM game_season WHERE game_season_id = ?");
$stmt->execute([$id]);
$season = $stmt->fetch();

$stmt->closeCursor();

// Get all current teams for the season
$stmt2 = $pdo->prepare("SELECT gt.* 
			FROM game_team gt
			WHERE gt.game_season_id = ? 
			ORDER BY 1 ASC ");
$stmt2->execute([$id]);
$season_teams = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$stmt2->closeCursor();

// Get all available teams for drop-down list
$stmt3 = $pdo->prepare("SELECT * FROM game_team
			WHERE game_season_id = 0
			AND game_club_id > 0
			ORDER BY 1 ASC ");
$stmt3->execute();
$all_teams = $stmt3->fetchAll(PDO::FETCH_ASSOC);
$stmt3->closeCursor();
?>

<div id="game-form-wrapper">
</div>
<div id="green-space">
    &nbsp;
</div>
<div id="game-form">

<?php require 'game_nav.php'; ?>

  <div id="width-wrapper" style="width: 875px;">
   
   <div id="season-div">
   <h3 style="text-align: center;">Update <span style="text-shadow: 2px 2px 2px #BD3039;"><?= htmlspecialchars($season['season_name']); ?></span> Season</h3>
   <div id="game-season" >
    <form method="POST">
        <label for="season_name">Season Name:</label>
        <input type="text" style="width: 85px;" name="season_name" value="<?= htmlspecialchars($season['season_name']); ?>" required>
     <br>
     <br>
<?php
if( $_SESSION['user_role'] == 'admin' ){
	$adminSelected = 'selected';
	$userSelected = '';
	if( $season['access_level'] == 'user' ){
		$adminSelected = '';
		$userSelected = 'selected';
	}
	
?>
     	<label for="role_visibility">Role Visibility:</label>
	<select name="role_visibility">
		<option value="admin" <?= $adminSelected; ?> >Admin Only</option>
		<option value="user" <?= $userSelected; ?>>User & Admin</option>
	</select>
<?php
} else {
?>
	<!--No role setting -->
<?php
}
?>
     <br>
     <br>
        <input type="submit" class="link-button" value="Update Season">
    </form>
     </div>
    
     
    <br>
    <hr>
    <br>
   
   <h3 style="text-align: center;">Add A team To This Season</h3>
   <div id="add-team">
     <form id="addCardForm" method="POST">
         <label for="team">Available Teams: </label>
         <select id="team" name="game_team_id" required>
             <?php foreach ($all_teams as $team): ?>
                 <option value="<?php echo htmlspecialchars($team['game_team_id']); ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
             <?php endforeach; ?>
         </select>
        
         <input type="hidden" name="id" value="<?php echo $id; ?>">
    	<br>
    	<br>
    	<input type="submit" class="link-button" value="Add Team To Season">
    	<br>
    	<br>
    </form>
    </div>
    </div>
    <div id="season-teams">
    	<h3><span style="text-shadow: 2px 2px 2px #BD3039;"><?= htmlspecialchars($season['season_name']); ?></span> Season Teams</h3>
    	<table id="team-table" style="">
    		<tr>
    			<th>ID</th>
    			<th>Team Name</th>
    			<th>Logo</th>
    			
    		</tr>
    		
        <?php 	
        	foreach ($season_teams as $season_team): ?> 			
    			<td><?= $season_team['game_team_id']; ?>
    			</td>
    			<td><a href="game_team_edit.php?id=<?= $season_team['game_team_id']; ?>" style="text-decoration: underline;"><?= $season_team['team_name']; ?></a>
    			</td>
    			<td><img style="max-height: 75px;" src="<?= $season_team['logo_url']; ?>">
    			</td>
    			
			<!--<td>
				<table>
					<tr>
						<td>
							<form method="POST" id="bo-card_id-<?php echo $season_team['game_team_id']; ?>" >
							<input type="hidden" name="game_team_id" value="<?= $season_team['game_team_id']; ?>">
							&nbsp; <input type="submit" style="background-color: lightgray;" class="link-button" value="Remove" disabled>
							</form>
						</td>

					</tr>
				</table>    			  
    			</td>
    			-->
    			
    		</tr>
    	
        <?php endforeach; ?>
    	</table>
    </div>
    </div>
    
 </div>   
    
</body>
</html>


<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

$stmt1 = $pdo->prepare("
 			SELECT *
 			FROM game_club ");
   
$stmt1->execute();
$all_clubs = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_name = $_POST['team_name'];
    $game_season_id = '0';
    $game_club_id = $_POST['game_club_id'];

    // Insert a new team
    $stmt = $pdo->prepare("INSERT INTO game_team (team_name, game_season_id,game_club_id, pitcher_card_id, catcher_card_id, first_base_card_id, second_base_card_id, third_base_card_id, short_stop_card_id, left_field_card_id, center_field_card_id, right_field_card_id, dh_card_id) VALUES (?, 0, ?,0,0,0,0,0,0,0,0,0,0)");
    $stmt->execute([$team_name, $game_club_id]);

    header('Location: game_teams.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Game Team</title>
     <?php require 'head_scripts_include.php';?>
	
	<style>

	input[type=text] {
		width: 35px;
	}
	</style>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
</head>
<body>
<?php require 'header.php';?>
<div id="game-form-wrapper">
</div>
<div id="green-space">
    &nbsp;
</div>
<div id="game-form">

<?php require 'game_nav.php'; ?>

    <form method="POST">
    <div id="team-div"  style="width: 420px;">
    <h3 style="text-align: center;">Add A New Team</h3>
   <div id="game-team" >
        <label for="team_name" style="font-weight: bold;">Team Name:</label>
        <input type="text" style="width: 85px;" name="team_name" required>
        <label style="font-weight: bold;">Game Season ID:</label>
        <label style="font-weight: normal;">0 (<span style="color: red;">***</span>add this team to a season later)</label>
        
        <label for="game_club_id">Club:</label>
        <select name="game_club_id">
        	 <?php foreach ($all_clubs as $club): 
        	 		if( $club['game_club_id'] > 0) {?> 	
        	 			<option value="<?= $club['game_club_id']; ?>"><?= $club['club_name']; ?></option>
        	 <?php 
        	 		}
        	 		endforeach; ?>
        </select>
        
     <!--   
        <span style="font-weight: bold;">Active Players</span>
        <label style="font-weight: bold;"></label>
        <label for="pitcher_card_id">Pitcher Card ID:</label>
        <input type="text" name="pitcher_card_id">
        <label for="catcher_card_id">Catcher Card ID:</label>
        <input type="text" name="catcher_card_id">
        <label for="first_base_card_id">First Base Card ID:</label>
        <input type="text" name="first_base_card_id">
        <label for="second_base_card_id">Second Base Card ID:</label>
        <input type="text" name="second_base_card_id">
        <label for="third_base_card_id">Third Base Card ID:</label>
        <input type="text" name="third_base_card_id">
        <label for="short_stop_card_id">Short Stop Card ID:</label>
        <input type="text" name="short_stop_card_id">
        <label for="left_field_card_id">Left Field Card ID:</label>
        <input type="text" name="left_field_card_id">
        <label for="center_field_card_id">Center Field Card ID:</label>
        <input type="text" name="center_field_card_id">
        <label for="right_field_card_id">Right Field Card ID:</label>
        <input type="text" name="right_field_card_id">
        <label for="dh_card_id">DH Card ID:</label>
        <input type="text" name="dh_card_id">      
        <br>
      -->  
        <label></label>
        <input type="submit" class="link-button" style="margin-bottom: 10px;" value="Add Team">
      
        
        <br>
      </div>
      
        <input type="hidden" name="game_season_id" value="0">
    </form>
  </div>
  </div>
</body>
</html>

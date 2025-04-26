<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

if (!isset($_GET['id'])) {
    //header('Location: game_teams.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cardboard Baseball Team Detail </title>  
     <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     <?php 
     	//require 'head_scripts_include.php';
     ?>   
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
 
     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
     <style>
     	.diamond-shape {
		display: block;
		position: relative;
		left: 10px;
		top: -50px;
		float: left;
		margin: 10px 0px 0px 15px;
		writing-mode: horizontal-tb; 
		transform:rotate(45deg);		    
		color: #FFF;
		font-size: 17px;
		font-weight: bold;
		text-shadow: 0px 0px 10px #000;
	}
     	.diamond-shape button {		   
     		font-size: 17px;
	}
     	.diamond-tan {
		width: 250px;
		background: tan;
		height: 250px;
		position: relative;
		left: 90px;
		top: -230px;
		/*margin: 150px 0px 20px 125px;*/
	}
	.diamond-green {
		display: block;
		position: relative;
		left: -10px;
		top: 15px;
		margin: 70px 0px 20px 125px;
		background: darkgreen;
		height: 200px;
		/* text-align: center;*/
		width: 200px;
		z-index: 900;
     	}
	.diamond-position {
		z-index: 1000;
	}
	.diamond-card {
		position: relative;
		display: none;
	}
	.diamond-button {	  		    
		color: #FFF;
		font-weight: bold;
		text-shadow: 2px 2px 8px #000;
		border: none;
		margin: 0;
		padding: 0;
		background-color: transparent;
	}
	.team-card:hover {
		position: relative;
		display: block;
		transform: scale(8) translate(0%, -20%);
		margin-top: -30px;
	}
	.roster-card:hover {
		position: relative;
		display: block;
		transform: scale(5);
	}
	.modal-card:hover {
		/*position: relative;
		display: block;
		margin: 20px 0px 0px 120px;
		transform: scale(3);
		*/
	}
	.diamond-position:hover {
		position: relative;
		/*show a popup with dropdown list to select player from this team or select 'None'*/
		color: #0C2340;
	}
	.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		left: 0;
		top: 0;
		width: 450px; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: transparent;
	}
	.modal-content {
		background-color: #fefefe;
		margin: 15% auto; /* 15% from the top and centered */
		padding: 20px;
		border: 1px solid #888;
		width: 80%; 
	}
	.close {
		color: #aaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover, .close:focus {
		color: black;
		text-decoration: none;
		cursor: pointer;
	}
	#add-card-div {
		float: left;
		border: 15px solid #0C2340;
		border-radius: 17px;
		padding: 10px 20px;
		margin: 30px 20px 0px 10px;
		background-color: rgba(84, 121, 109, 0.65);
		background-blend-mode: luminosity;
	}
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
	#roster-table tr {
		border-collapse: collapse; 
		border: 1px solid #0C2340;
	}
	.left-column {
		/*width: 600px;*/
	}
	.right-column {
		/*width: 600px;*/
	}
	#bullpen-table td, #bullpen-table th {
		font-size: 18px;
		padding: 5px;
		font-weight: bold;
	}
     </style>
</head>
<body>
<?php require 'header.php';?>

<?php
$id = $_GET['id'];
$databaseMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//if 'Update Player Position' was clicked, update the ID in the team record
	if( isset( $_POST['update-position'] ) ) {
	
		//Pull the current position IDs
		$stmt = $pdo->prepare("SELECT *
				FROM game_team gt
				WHERE gt.game_team_id = ?");
		$stmt->execute([$id]);
		$activePositions = $stmt->fetch();
	
		// [0] is pitcher, [1] is catcher, [2] is first base, [3] is second base, [4] is shortstop, 
		// [5] is third base, [6] is left field, [7] is center field, [8] is right field, 
		// [9] is designated hitter, [10] is reliever
		$field_name = '';
		$field_value = $_POST['modal-player-select'];
		if( $_POST['position-index'] == '0' ) {
			//Update pitcher_card_id
			$field_name = "pitcher_card_id";
		} else if( $_POST['position-index'] == '1' ) {
			//Update right_field_card_id
			$field_name = "catcher_card_id";
		} else if( $_POST['position-index'] == '2' ) {
			//Update right_field_card_id
			$field_name = "first_base_card_id";
		} else if( $_POST['position-index'] == '3' ) {
			//Update right_field_card_id
			$field_name = "second_base_card_id";
		} else if( $_POST['position-index'] == '4' ) {
			//Update right_field_card_id
			$field_name = "short_stop_card_id";
		} else if( $_POST['position-index'] == '5' ) {
			//Update right_field_card_id
			$field_name = "third_base_card_id";
		} else if( $_POST['position-index'] == '6' ) {
			//Update right_field_card_id
			$field_name = "left_field_card_id";
		} else if( $_POST['position-index'] == '7' ) {
			//Update right_field_card_id
			$field_name = "center_field_card_id";
		} else if( $_POST['position-index'] == '8' ) {
			//Update right_field_card_id
			$field_name = "right_field_card_id";
		} else if( $_POST['position-index'] == '9' ) {
			//Update right_field_card_id
			$field_name = "dh_card_id";
		} else if( $_POST['position-index'] == '10' ) {
			//Update right_field_card_id
			$field_name = "reliever_card_id";
		} 
		$query_string = "UPDATE game_team SET " . $field_name . " = ? WHERE game_team_id = ? ";
		$stmt = $pdo->prepare($query_string);
		$stmt->execute([$field_value, $id]);
		
		$battingOrderValue = 0;
		//Set the batting_order value to 1 if the newly assigned player is a batter, 0 if it's a starting pitcher, -1 if it's the relief pitcher
		if( $_POST['position-index'] == '0' ) {
			$battingOrderValue = 0;
		} else if( $_POST['position-index'] == '10' ) {
			$battingOrderValue = -1;
		} else {
			$battingOrderValue = 1;
		}
		
		if( $field_value == '0' ) {
			//There is no longer a selected card for this position
		} else {
			$query_string = "UPDATE game_team_player SET batting_order = ? WHERE card_id = ? and game_team_id = ? ";
			$stmt = $pdo->prepare($query_string);
			$stmt->execute([$battingOrderValue,$field_value, $id]);
		}
		//Set batting_order for the guy who was sent off, 20 for batter or -2 for pitcher
		//Unless they hit refresh and the new guy is the same as the current guy
		$prevCardID = $activePositions[$field_name];
		if( $prevCardID <>  $field_value ) {
			$query_string = "UPDATE game_team_player SET batting_order = 20 WHERE card_id = ? and game_team_id = ? ";
						$stmt = $pdo->prepare($query_string);
			$stmt->execute([ $prevCardID, $id]);
		}		
	}
	
	
	//if Team Update was clicked, update the team fields
	if( isset( $_POST['team_name'] ) ) {
		$team_name = $_POST['team_name'];
		$team_card_id = $_POST['team-card-id'];
		$uploadDir = 'team-logos/';
		$uploadDir = '../images/team-logos/';
		$fileName = basename($_FILES['image']['name']);
		$filePath = $uploadDir . $fileName;
		$fileType = pathinfo($filePath, PATHINFO_EXTENSION);
		
		if( !empty( $_FILES['image']['name'] ) ) {
			if (($fileType === 'jpg' || $fileType === 'png') && $_FILES['image']['size'] <= 1548576) {
				if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
					// Update team information
					$stmt = $pdo->prepare("UPDATE game_team SET team_name = ?, game_team_card_id = ?, logo_url = ? WHERE game_team_id = ?");
					$stmt->execute([$team_name, $team_card_id, $filePath, $id]);				
				} else {
					echo "Failed to save file";
				}	
			} else {
				//invalid file
				echo "invalid file type or size";
			}
		} else {
			// Update team information
			$stmt = $pdo->prepare("UPDATE game_team SET team_name = ?, game_team_card_id = ? WHERE game_team_id = ?");
			$stmt->execute([$team_name, $team_card_id, $id]);		
		}
		$databaseMsg = 'Updates complete';
		
	    //Reload the page to display new values
	    //header('Location: game_team_edit2.php');
	    //exit;
    	} else if( isset( $_POST['game_team_id'] ) ) {
    		// Add player to team
    		$card_id = $_POST['card_id'];
    		$pitcher_or_batter = $_POST['pitcher_or_batter'];
		$stmt = $pdo->prepare("INSERT INTO game_team_player (game_team_id, card_id)
		    VALUES( ?, ?) ");
		$stmt->execute([$id, $card_id]);
		
    		// Insert a game_player_stats record for this player if it doesn't exist
    		$card_id = $_POST['card_id'];
    		
		$stmt2 = $pdo->prepare("
			INSERT INTO game_player_stats (game_team_id, card_id, pitcher_or_batter)
				SELECT distinct ?, card_id, ? as pitcher_or_batter FROM game_team_player
				WHERE card_id = ? 
				AND card_id NOT IN (
					Select distinct card_id 
					FROM game_player_stats
					WHERE game_team_id = ?
				) 
		    	");
		$stmt2->execute([$id, $pitcher_or_batter, $card_id, $id]);

		//INSERT INTO card_stats if it doesn't exist	
		$stmt3 = $pdo->prepare("INSERT INTO card_stats (card_id, pitcher_or_batter)
		    SELECT card_id, ? as pitcher_or_batter FROM game_team_player
		    WHERE card_id = ? 
		    AND card_id NOT IN (Select distinct card_id FROM card_stats) ");
		$stmt3->execute([$pitcher_or_batter, $card_id]);

		//UPDATE card_stats in case it existed without pitcher_or_batter
		$stmt3 = $pdo->prepare("UPDATE card_stats 
					SET pitcher_or_batter = ?
					WHERE card_id = ? ");
		$stmt3->execute([$pitcher_or_batter, $card_id]);
		
		//Try to match a playerID from People table
		$stmt4 = $pdo->prepare("UPDATE card c, People p
					SET c.playerID = p.playerID
					WHERE c.player_name like ( CONCAT(p.nameFirst, ' ', p.nameLast, '%') )
					AND c.card_id = ?");
		$stmt4->execute([$card_id]);	

		//Pull season stats from batting or pitching table
		//Sometimes this pulls nothing or incorrect data, if playerID wasn't updated correctly, above
		if( $pitcher_or_batter == 'batter' ) {
			$stmt5 = $pdo->prepare("UPDATE card_stats cs, card c, batting b
						SET cs.hits = b.H, 
						    cs.walks = b.BB, 
						    cs.strikeouts = b.SO, 
						    cs.doubles = b.2B, 
						    cs.triples = b.3B, 
						    cs.home_runs = b.HR, 
						    cs.stolen_bases = b.SB,
						    cs.at_bats = b.AB,
						    cs.batting_avg = b.H/b.AB 
						WHERE b.playerID = c.playerID
						AND b.yearID = c.year - 1
						AND cs.card_id = c.card_id
						AND cs.at_bats = 0
						and cs.card_id = ?");
			$stmt5->execute([$card_id]);	
		} else {
			$stmt5 = $pdo->prepare("UPDATE card_stats cs, card c, pitching p
						SET cs.era = p.era, 
						    cs.wins = p.W, 
						    cs.losses = p.L, 
						    cs.innings_pitched = IPouts/3, 
						    cs.strikeouts = p.SO, 
						    cs.walks = p.BB
						WHERE p.playerID = c.playerID
						AND p.yearID = c.year - 1
						AND cs.card_id = c.card_id
						AND cs.at_bats = 0
						and cs.card_id = ?");
			$stmt5->execute([$card_id]);	
			
		}
		
    	
    		echo '<div >Inserted successfully</div>';
    	} else if( isset( $_POST['batting_order'] ) ) {
    		$batting_order = $_POST['batting_order'];
    		$card_id = $_POST['card_id'];
		$stmt6 = $pdo->prepare("UPDATE game_team_player 
					SET batting_order = ?
					WHERE card_id = ?");
		$stmt6->execute([$batting_order, $card_id]);	
    		
    	}
}

// Fetch the current team data
	$stmt = $pdo->prepare("SELECT *
			FROM game_team gt, game_season gs, game_club gc 
			WHERE gt.game_season_id = gs.game_season_id 
			AND gc.game_club_id = gt.game_club_id
			AND gt.game_team_id = ?");
	$stmt->execute([$id]);
	$team = $stmt->fetch();

	$stmt->closeCursor();

//Get the whole team bench
	$stmt2 = $pdo->prepare("SELECT c.*, gps.pitcher_or_batter, gtp.batting_order, cs.* , gtp.game_team_id
			FROM game_team_player gtp, game_player_stats gps, card c, card_stats cs
			WHERE c.card_id = gtp.card_id 
			AND c.card_id = cs.card_id
			AND c.card_id = gps.card_id 
            		AND gtp.game_team_id = gps.game_team_id
			AND gtp.game_team_id = ?
			ORDER BY gps.pitcher_or_batter, ABS(gtp.batting_order) ASC");
$stmt2->execute([$id]);
$team_players = $stmt2->fetchAll();


// Get all cards for 'Available Players' drop-down list
$game_season_id = $team['game_season_id'];
$stmt = $pdo->prepare("SELECT c.* 
			FROM card c 
			WHERE card_id not in ( 
				select distinct gtp.card_id 
				from game_team_player gtp, game_team gt
				WHERE gtp.game_team_id = gt.game_team_id
				AND gt.game_season_id = ?
				) 
			ORDER BY c.year, c.brand, c.player_name ASC 
			");
$stmt->execute([$game_season_id]);
$all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all team cards for team card drop-down list
$stmt = $pdo->prepare("SELECT c.* 
			FROM card c 
			WHERE is_team_card = 1	
			ORDER BY c.year, c.brand, c.player_name ASC 
			");
$stmt->execute();
$team_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="game-form-wrapper">

</div>

<div id="green-space">
    &nbsp;
</div>

<div id="game-form" style="padding-left: 5px;">

<?php require 'game_nav.php'; ?>

	<div id="width-wrapper" style="width: 1250px; display: grid; grid-template-columns: 1fr 1fr;">
		
<!--****Team Name & Logo -->
  		<div id="team-div-x" class="left-column display-container" style="margin-left: 15px; padding: 10px 20px;">
   			<h3 style="font-size: 25px; margin-top: 5px; text-align: center; text-shadow: 1px 1px 1px #BD3039;"><span style="color: #BD3039; text-shadow: 1px 1px 1px #BD3039;"><?= htmlspecialchars($team['season_name']); ?> Season</span> <?= htmlspecialchars($team['team_name']); ?> <span style="text-shadow: 1px 1px 1px #0C2340;">Team Profile</span></h3>
   
  			<form id="team-profile-form" method="POST" enctype='multipart/form-data'>    
  				<div id="game-team" style="position: relative; margin-top: 35px;" >
					<div style="float: left; padding-right: 15px; font-weight: bold;">
						Team Name: 
						<input type="text" class="team-form-item" style="font-size: 20px; width: 125px;" name="team_name" value="<?= htmlspecialchars($team['team_name']); ?>" required>
					</div>
					<div style="float: right; margin: 0px 0px 0px 15px; font-weight: bold;">Season:&nbsp;&nbsp;
						<span style="font-size: 20px; text-shadow: 1px 1px 1px #BD3039;"><?= $team['season_name']; ?></span>
					</div>
					<div style="float: left; margin: 20px 0px 0px 0px; font-weight: bold;">Club:&nbsp;&nbsp;
						<span style="font-size: 20px; text-shadow: 1px 1px 1px #BD3039;"><?= $team['club_name']; ?></span>
					</div>
					<div style="float: right; margin: 20px 0px 0px 15px; font-weight: bold;">Update Logo URL: <br>
					<input type="file" id="image-upload" name="image" accept="image/jpeg, image/png"  class="team-form-item"  style="font-size: 16px;" >
						
					</div>
					
					<button class="link-button team-profile-submit team-form-item" style="max-height: 30px; margin-top: 30px; margin-left: 20px; width: 150px;" >Update Team</button>
					
					<div style="float: right; margin: 10px 0px 0px -15px; font-weight: bold;">
						<div style="margin: 0px 0px 0px 30px;">
							Select Team Card:
							<br>
						
		<?php
				foreach ($team_cards as $team_card):		
					if( $team_card['card_id'] == $team['game_team_card_id'] ) {
						echo '<img class="team-card" src="/images/Baseball-Cards/' . $team_card['img_url'] . '" style="max-width: 70px;max-height: 70px; z-index: 1000;">';
					}
				endforeach;
				
					echo '</div>';
					echo '<select id="team-card-select" name="team-card-id"  class="team-form-item" style="font-size: 16px;">';
					//echo '<option value-"0">No image</option>';
				foreach ($team_cards as $team_card):	
					
					echo '<option value="' . $team_card['card_id'] . '" ';
					if( $team_card['card_id'] == $team['game_team_card_id'] ) {
						echo ' selected ';
					} 
					if(  $team_card['card_id'] == 0 ){
						echo '>' .$team_card['player_name'] . '</option>';
					} else {
						echo '>' . $team_card['year'] . ' ' . $team_card['brand'] . ' #' . $team_card['number'] . ' ' .$team_card['player_name'] . ' ' . $team_card['card_id'] . '</option>';
					}
	
				endforeach;
		
		?>													
						</select>
					</div>					
					<div id="team-logo-div" style="position: absolute; top: -35px; right: 20px; text-align: center; z-index: 800;">
						<img style="border: 1px solid black; max-width: 75px; max-height: 75px;" src="<?= $team['logo_url']; ?>">
						<div style="margin: -5px 0px; font-size: 14px;">
							LOGO
						</div>
					</div>
					<p class="error" id="error-message"></p>
        				<p id="success-message" style="color: green;"><?= $databaseMsg; ?></p>
				</div>
		    	</form>
		</div>				
<!--****Add Card -->
  		<div id="add-card-div" class="right-column display-container" style="position: relative; margin-left: 10px;">
		    	
			<div id="card-form-notes" style="display: none; color: rgb(12, 35, 64); margin-top: 10px;">
				<span style="color: red;">***</span>If you update the pitcher or reliever card ID, that pitcher card will get batting_order=0, the reliver card will get batting order = -1 and the previous pitcher/reliever card will get batting_order=20.<br><br>
				<span style="color: red;">****IMPORTANT: </span>The active team cards must have a batting_order value that is below 10. The active pitcher is always batting_order=0.<br>Non-active players are always batting_order=20.
			</div>			
			<h3 style="margin-top: 10px; text-align: center; font-size: 25px; color: #0C2340; text-shadow: 1px 1px 1px #BD3039;">Recruit A Free Agent</h3>
     			<form id="addCardForm" method="POST">
         			<label for="card" style="color: rgb(12, 35, 64); font-weight: bold;">Available Cards: </label>
         			<br>
				 <select id="card" name="card_id" style="font-size: 16px;" required>
			<?php foreach ($all_cards as $card): 
				     	if( $card['card_id'] > 0 ) { 
			?>
					 	<option value="<?php echo htmlspecialchars($card['card_id']); ?>"><?php echo htmlspecialchars($card['year']) . ' ' . htmlspecialchars($card['brand']) . ' #' . htmlspecialchars($card['number']) . ' ' . htmlspecialchars($card['player_name']) . ' [' . htmlspecialchars($card['card_id']) . ']'; ?></option>
			<?php 		}
				     	endforeach; 
			?>
				 </select>
				<br>
				<br>
				 <select id="pitcher_or_batter" name="pitcher_or_batter" style="font-size: 16px;"  required>
					<option value="batter">batter</option>
					<option value="pitcher">pitcher</option>
				 </select>
				 <span style="color: red;">***</span><span style="color: rgb(12, 35, 64);"><b>Important:</b> select pitcher, if it's a pitcher</span>
				 <input type="hidden" name="game_team_id" value="<?php echo $id; ?>">
				<br>
				<br>
				<input type="submit" class="link-button" value="Recruit Player">
			</form>
		</div>

		<div id="row-wrapper-1" class="left-column" style="">	
		
<!--****Diamond Display -->
		<div id="diamond-wrapper" class="left-column" style="position: relative; max-height: 470px; overflow: clip; float: left; margin: 20px 0px 0px 20px; background-color:rgba(1, 50, 32, 0.2); border: 3px solid #000;">
			<!--**** The Modal -->
			<div id="diamond-modal" class="modal">	
				<!-- Modal content -->
				<div class="modal-content" style="height: 450px;">
					<span id="close-x" class="close">&times;</span>
					<div style="font-weight: bold; font-size: 20px; margin: 0 0 15px 0;">
						<span id="modal-position" style="text-transform: capitalize;"></span>
						<br>
						<span id="modal-player-name"></span><br>ID: <span id="modal-player-id"></span>
					</div>

					<img id="modal-card" class="modal-card" style="float: left; max-height: 250px;" src="/images/Baseball-Cards/default.jpg">
					<br>
					<p><button id="remove-button" class="remove-button" style="display: none; white-space: normal; width: 120px; float: right;font-weight: bold;">Move This Player To The Bench</button></p>

					<div style="float: left;font-weight: bold; font-size: 16px;margin: 10px 0 0 0;">Select A Different Player For This Position
					<br>
				<script>
					var ddlData = [];
				</script>
					<form id="modal-form" method="POST">
						<input type="hidden" name="update-position" value="1"/>
						<input id="position-index" type="hidden" name="position-index" value="0"/>

						<select id="modal-player-select" name="modal-player-select">
							<option value="0">Bench this player</option>
			<?php 	//TODO: Create team_roster table
				//Then write a query that shows only cards that are not already on the roster
				//(It's no longer enough to have the pitcher_card_id field and the cather_card_id field, etc.

				foreach ($team_players as $team_player):		
				?>
							
					<script>

						ddlData.push({ key: '<?= $team_player['card_id']; ?>', value: '<?= $team_player['year']; ?> <?= $team_player['brand']; ?> #<?= $team_player['number']; ?> <?= $team_player['player_name']; ?> <?= $team_player['card_id']; ?> <?= $team_player['pitcher_or_batter']; ?>'});
					</script>

				<?php endforeach; 						
				?>
						</select>

						<br>
						<div id="important-msg" style="display: none;">
							<span style="color: red;">***</span><span style="color: black;">Important: select a pitcher, not a batter</span>
						</div>
						<br>
						<input type="submit" class="link-button" value="Update Player">
					</form>
					</div>
				</div>		
			</div>	
			<div id="diamond-header" style="position: relative; top: 0px; left: 0px; width: 575px; height: 45px; background-color: transparent;">		
				<h3 style="margin-top: 10px; color: #0C2340; text-align: center; font-size: 25px; text-shadow: 1px 1px 1px #BD3039;">Starting Team</h3>
			</div>

			<div id="diamond" class="diamond-shape">			
				<div id="diamond-green" class="diamond-green">

				</div>
				<div class="diamond-tan">
			<?php 	
					$current_roster = array( $team['pitcher_card_id'],$team['catcher_card_id'],$team['first_base_card_id'],$team['second_base_card_id'],$team['short_stop_card_id'],$team['third_base_card_id'],$team['left_field_card_id'],$team['center_field_card_id'],$team['right_field_card_id'],$team['dh_card_id'],$team['reliever_card_id'] );

					echo '<span id="roster-array" roster="' . $team['pitcher_card_id'] . ',' . $team['catcher_card_id'] . ',' . $team['first_base_card_id'] . ',' . $team['second_base_card_id'] . ',' . $team['short_stop_card_id'] . ',' . $team['third_base_card_id'] . ',' . $team['left_field_card_id'] . ',' . $team['center_field_card_id'] . ',' . $team['right_field_card_id'] . ',' . $team['dh_card_id'] . ',' . $team['reliever_card_id'] . '"></span>';

					//$positionsArray = array('pitcher','catcher','1b','2b','ss','3b','lf','cf','rf','dh','reliever' );
					$positionsArray = array('pitcher','catcher','first-base','second-base','shortstop','third-base','left-field','center-field','right-field','designated-hitter','relief-pitcher' );
					$abbreviationsArray = array('P','C','1B','2B','SS','3B','LF','CF','RF','DH','RP' );
					$stylesArray = array(' top: 95px; left: 40px; ', ' top: 165px; left: 125px; ',' top: -100px; left: 135px; ',' top: -175px; left: 75px; ' ,' top: 0px; left: -95px; ',' top: 70px; left: -100px; ' ,' top: -30px; left: -200px; ',' top: -220px; left: -105px; ',' top: -305px; left: 25px; ',' top: -160px; left: 200px; ',' top: 50px; left: -25px; ');
					$positionIndex = 0;
					foreach ($current_roster as $roster_position): 	
						if( $roster_position == 0 ) {
							//The ID for this position is currenlty set to 0
				?>
				<div id="diamond-<?= $positionsArray[$positionIndex]; ?>" class="diamond-position" style="position: relative; <?= $stylesArray[$positionIndex]; ?>  transform:rotate(-45deg);"> 
						<?= $abbreviationsArray[$positionIndex]; ?>: <button id="0" name="Not Selected" image="/images/Baseball-Cards/default.jpg" position="<?= $positionsArray[$positionIndex]; ?>" class="diamond-button">Not Selected</button> <?= $current_roster[$positionIndex]; ?>
						<img class="diamond-card" style="width: 45px;" src="/images/Baseball-Cards/default.jpg">
					</div>
			<?php
					} else {
						foreach ($team_players as $team_player2):
							if( $team_player2['card_id'] == $roster_position && $positionIndex < 11) {				
			?>
					<div id="diamond-<?= $positionsArray[$positionIndex]; ?>" class="diamond-position" style="position: relative; <?= $stylesArray[$positionIndex]; ?>  transform:rotate(-45deg);"> 
						<?= $abbreviationsArray[$positionIndex]; ?>: <button id="<?= $team_player2['card_id']; ?>" name="<?= $team_player2['year']; ?> <?= $team_player2['brand']; ?> #<?= $team_player2['number']; ?> <?= $team_player2['player_name']; ?>" image="/images/Baseball-Cards/<?= $team_player2['img_url']; ?>" position="<?= $positionsArray[$positionIndex]; ?>" class="diamond-button"><?= $team_player2['player_name']; ?></button> <?= $current_roster[$positionIndex]; ?>
						<img class="diamond-card" style="width: 45px;" src="/images/Baseball-Cards/<?= $team_player2['img_url']; ?>">
					</div>
	<?php
					} else {
						//echo ":" . $positionIndex;
					}

				endforeach;
			}
			$positionIndex += 1;
		endforeach; 
	?>			


				</div>
			</div>
			<div style="text-align: left; padding: 0px; margin: 0px; font-size: 20px; position: absolute; top: 440px; left: 130px; width: 600px; font-weight: bold; color: #BD3039; text-shadow: 1px 1px 1px #0C2340;">Click a player to change a position</div>
		</div>	
		
		<script>
			//Move a position player to the Bench
			$("#remove-button").click(function(e) {
				//Set this position to ID 0 on the team 
				var cardID = $("#modal-player-id").html();
			  //***TODO
			});

			//Modal Window popup
			$(".diamond-button").click(function(e) {
				$("#diamond-modal").css("display", "block");	
				//Get the clicked button's id
				var cardId = $(this).attr("id"); 
				var modalImg = $(this).attr("image"); 
				var modalplayerName = $(this).attr("name"); 
				var modalPosition = $(this).attr("position"); // first-base
				var positionIndex = 0;
				//alert(cardId);
				$("#modal-player-id").html( cardId );
				$("#modal-player-name").html( modalplayerName );
				$("#modal-position").html( modalPosition.replace("-", " ") ); //first base
				if( modalPosition == 'pitcher' ) {
					$("#important-msg").css("display", "block");
				} else {
					$("#important-msg").css("display", "none");
					//alert("test"+ cardId);
				}

				//Empty and refill the dropdown list 
				var select = $("#modal-player-select");
				select.empty();
				select.append($('<option>', { 
				      value: '0',
				      text: 'Bench This Player'  
				}));
				$.each(ddlData, function(index, item) {
				    select.append($('<option>', { 
				      value: item.key,
				      text: item.value 
				    }));
				});
				//Make an array of the roster id's that are currently assigned
				var rosterIDs = [<?= $team['pitcher_card_id']; ?>,<?= $team['catcher_card_id']; ?>,<?= $team['first_base_card_id']; ?>,<?= $team['second_base_card_id']; ?>,<?= $team['short_stop_card_id']; ?>,<?= $team['third_base_card_id']; ?>,<?= $team['left_field_card_id']; ?>,<?= $team['center_field_card_id']; ?>,<?= $team['right_field_card_id']; ?>,<?= $team['dh_card_id']; ?>,<?= $team['reliever_card_id']; ?>];
				var positionIDs = ['pitcher','catcher','first-base','second-base','shortstop','third-base','left-field','center-field','right-field','designated-hitter','relief-pitcher' ];
				$.each(positionIDs, function(index, value) {
					if( value == modalPosition ) {
						positionIndex = index;
					}
				});
				//Loop through the player drop down list and remove any that are in the array of current rosterIDs
				$.each(rosterIDs, function(index, value) {
					//alert(value);					
					$("#modal-player-select > option").each(function() {
					    //alert(this.text + ' ' + this.value);
					    if( value == this.value && this.value > 0) {
							$(this).remove();
						}
					});

				});

				//Update the position-index field
				//This index is the same value as the index for this card_id in rosterIDs
				// [0] is pitcher, [1] is catcher, [2] is first base, [3] is second base, [4] is shortstop, 
				// [5] is third base, [6] is left field, [7] is center field, [8] is right field, 
				// [9] is designated hitter, [10] is reliever		
				$("#position-index").val( positionIndex );

				//Update the player's image
				$("#modal-card").attr("src", modalImg);
			});
			$("#close-x").click(function(e) {
				$("#diamond-modal").css("display", "none");
			});

		</script>

          		<?php 	
          			//Make an array of the current active starting team
          			$isBench = 0;
          			$isBullpen = 0;
          			$current_roster = array( $team['pitcher_card_id'],$team['catcher_card_id'],$team['first_base_card_id'],$team['second_base_card_id'],$team['short_stop_card_id'],$team['third_base_card_id'],$team['left_field_card_id'],$team['center_field_card_id'],$team['right_field_card_id'],$team['dh_card_id'],$team['reliever_card_id'] );
				$positionsArray = array('pitcher','catcher','first-base','second-base','shortstop','third-base','left-field','center-field','right-field','designated-hitter','relief-pitcher' );
				//triage and distribute players to the appropriate bucket
				$activeBatters = array();
				$benchBatters = array();
				$bullpenPitchers = array();
          			foreach ($team_players as $team_player): 
					$isOnActiveRoster = false;
					$myCtr = 0;
					//Check whether this player is in the current roster array
					foreach ($current_roster as $roster_position):
						if( $roster_position == $team_player['card_id'] ) {
							//Found a match. This player is on the current active roster
							$isOnActiveRoster = true;
							if($myCtr == 0 || $myCtr == 10){
								array_push($bullpenPitchers, $team_player);
							} else {
								array_push($activeBatters, $team_player);
							}
						}
						$myCtr++;
					endforeach;
					if(!$isOnActiveRoster) {
						//Didn't find a match on the current roster
						//Now check whether this guy is on the bench or in the bullpen
						if( $team_player['pitcher_or_batter'] == 'batter' ) {
							array_push($benchBatters, $team_player);
						} else {
							array_push($bullpenPitchers, $team_player);
						}
					}
				endforeach;
			?>

<!--****Display the Bullpen -->   
				<div id="bullpen-div" class="display-container" style="width: 540px; margin: 20px 0px 0px 15px !important; padding: 10px 20px; float: left;">
				<h3 style="font-size: 25px; margin-top: 5px; text-align: center; text-shadow: 1px 1px 1px #BD3039;">Bullpen</h3>
		          		<table id="bullpen-table" style="background-color: rgba(1, 50, 32, 0.2); border: 1px solid #0C2340; border-radius: 0px; margin-top: 30px; padding: 15px 20px 20px 20px; text-align: center;border-collapse: collapse;">		
					<thead>
						<tr>	
							<th>Player Name</th>
							<th>Position</th>
							<th></th>
							<th>Stats</th>
							<th>O</th>
							<th>ID</th>
							<th></th>
						</tr>
					</thead>
					<tbody>  
		          			
		          		<?php	
		          			foreach ($bullpenPitchers as $batter):  
		          		?>
		          				<tr  id="<?= $batter['card_id']; ?>"  style="border-top: 6px solid darkgreen;">					
								<td style="width: 162px;"><?= substr($batter['player_name'], 0, 17); ?>
								</td>
								<td>
								<?php
								if( $batter['batting_order'] == 0 ) {
									echo 'Starting Pitcher';
								} else if( $batter['batting_order'] == -1 ) {
									echo 'Relief Pitcher';
								} else {
									echo 'Pitcher';
								}
								?>
								</td>
								<td><img class="roster-card" style="width: 45px;" src="/images/Baseball-Cards/<?= $batter['img_url']; ?>">
								</td>		
								<td>ERA: <?= $batter['era']; ?><br>SO: <?= $batter['strikeouts']; ?></td>
								<td><?= $batter['batting_order'] ?></td>
								<td><?= $batter['card_id']; ?>
								</td>
								<td><button class="release-button" style="color: #BD3039; background-color: rgba(255, 255, 255, 0.3); margin: 5px; border: 2px solid #0C2340;max-width: 60px;font-weight: normal; font-size: 12px">
										Release Player
									</button>
								</td>
							</tr>
					<?php
						endforeach; 
					?>
							</tbody>
						</table>
		    		</div>

<!--***Display the Bench -->	
		<div id="bench-div" class="right-column display-container" style=" margin: 20px 0px 0px 15px; float: left; padding: 10px 20px;">
				<h3 style="font-size: 25px; margin-top: 5px; text-align: center; text-shadow: 1px 1px 1px #BD3039;">Bench</h3>
				<table id="bench-table" style="float: right; border: 15px solid #0C2340; border-radius: 17px; margin-top: 0px; padding: 15px 20px 20px 20px; text-align: center;border-collapse: collapse; border: 1px solid gray;">
					<thead>
						<tr>	
							<th>Player Name</th>
							<th>Position</th>
							<th></th>
							<th>Stats</th>
							<th>O</th>
							<th>ID</th>
							<th></th>
						</tr>
					</thead>
							<tbody>
			<?php 					
				//Display the benched batters
				foreach ($benchBatters as $batter):  
          				echo '<tr  id="'. $batter['card_id'] . '" style="border-top: 6px solid darkred;">';
			?>						
						<td style="width: 162px;"><?= substr($batter['player_name'], 0, 17); ?>
						</td>
						<td><?= $batter['pitcher_or_batter']; ?>
						</td>
						<td><img class="roster-card" style="width: 45px;" src="/images/Baseball-Cards/<?= $batter['img_url']; ?>">
						</td>
         		<?php 
					
					$hrAvg = 0;
					if( $batter['at_bats'] > 0 ) {
						$hrAvg = (600 * $batter['home_runs']) / $batter['at_bats'];
					}
					echo '<td>AVG: ' . number_format($batter['batting_avg'], 3) . '<br>HR: ' . $batter['home_runs'] . '<br>HRA:' . number_format($hrAvg, 1) . '</td>';
					
			?>
						<td><?= $batter['batting_order'] ?></td>
						<td><?= $batter['card_id']; ?>
						</td>
						<td><button class="release-button" style="color: #BD3039; background-color: rgba(255, 255, 255, 0.3); margin: 5px; border: 2px solid #0C2340;max-width: 60px;font-weight: normal; font-size: 12px">
								Release Player
							</button>
						</td>
  					</tr>
  					

  			<?php
          			endforeach; 
          		?>
          				</tbody>
				</table>
		</div>

	</div>
		
<!--****Display batting order-->
     		<div id="team-players" class="right-column display-container" style="max-height: fit-content; margin: 20px 0px 0px 15px !important;">
  			<h3 style="font-size: 25px; margin-top: 5px; text-align: center; text-shadow: 1px 1px 1px #BD3039;">Batting Order</h3>
  			<table id="roster-table" style="background-color: rgba(1, 50, 32, 0.2);border-collapse: collapse; border: 1px solid gray;">
  				<thead>
					<tr>
						<th></th>
						<th>Player Name</th>
						<th>Pos</th>
						<th></th>
						<th>Stats</th>
						<th>O</th>
						<th>ID</th>
						<th></th>
					</tr>
      				</thead>
      				<tbody class="batting-order-body">

			<?php	
				//Display the batting order of the active batters
				//echo '<tr  style="border-top: 6px solid black"><td colspan="6" style="font-weight: bold; font-size: 22px;">&#x25BC; BATTING ORDER &nbsp; <span style="font-size: 18px;"></span></td></tr>';
				//the array is already sorted by batting_order
				$abbreviationsArray = array('P','C','1B','2B','SS','3B','LF','CF','RF','DH','REL' );
          			foreach ($activeBatters as $batter): 
          				echo '<tr  id="'. $batter['card_id'] . '" style="border-top: 6px solid #0C2340;">';
			?>
						
						<td><img src="/images/dots-six-vertical-bold.svg" style="float: left; width: 30px; height: 30px; cursor:move;"></td>
						
						<td style="text-align:left; width: 162px;"><?= substr($batter['player_name'], 0, 17); ?>
						</td>
			<?php
				$myCtr = 0;
				foreach ($current_roster as $roster):	
					if( $roster == $batter['card_id'] ) {
						echo '<td>' . $abbreviationsArray[$myCtr] . '</td>';
					}
					$myCtr++;
				endforeach;
			?>			
						</td>
						<td><img class="roster-card" style="width: 45px;" src="/images/Baseball-Cards/<?= $batter['img_url']; ?>">
						</td>
         		<?php 
					
					$hrAvg = 0;
					if( $batter['at_bats'] > 0 ) {
						$hrAvg = (600 * $batter['home_runs']) / $batter['at_bats'];
					}
					echo '<td>AVG: ' . number_format($batter['batting_avg'], 3) . '<br>HR: ' . $batter['home_runs'] . '<br>HRA:' . number_format($hrAvg, 1) . '</td>';
					
			?>
						
						<td><?= $batter['batting_order'] ?> 
						</td>
						<td><?= $batter['card_id']; ?>
						</td>
						<td><button class="release-button" style="color: #BD3039; background-color: rgba(255, 255, 255, 0.3); margin: 5px; border: 2px solid #0C2340;max-width: 60px;font-weight: normal; font-size: 12px">
								Release Player
							</button>
						</td>
  					</tr>
  			<?php
  				endforeach;
  			?>
				</tbody>
				</table>
				
				
		</div>
				

		


 <?php require 'footer.php';?>   		
    	</div>  

 </div>   
 


<script>
        $(function () {
            // Make the list sortable
            $(".batting-order-body").sortable({
                update: function (event, ui) {
                    var sortedIDs = $(this).sortable("toArray");
		    var gameTeamID = <?= $_GET['id']; ?>;
		    var activeRosterIDs = $("#roster-array").attr("roster");
                    //alert("test");
                    $.ajax({
                        type: "POST",
                        url: "save_batting_order.php",
                        data: { sortedIDs: sortedIDs, gameTeamID: gameTeamID, activeRosterIDs: activeRosterIDs },
                        success: function (response) {
                            //alert("Order updated successfully!");
                            $('#status-msg-text').text("Status: Sort Order updated successfully");
			    
			    setTimeout(function() { $('#status-msg-text').text("Status:"); },3000);
                        },
                        error: function (response) {
                            alert("Error updating batting order.");
                        }
                    });
                }
            });
        });
        
	$(".team-form-item").on("click", function() {
		$("#success-message").html(" ");
	});
    </script>    
 
</body>
</html>


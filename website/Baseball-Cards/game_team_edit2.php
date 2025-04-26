<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: game_teams.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Team</title>
     <?php require 'head_scripts_include.php';?>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
</head>
<body>
<?php require 'header.php';?>

<?php
$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//if Team Update was clicked, update the team fields
	if( isset( $_POST['team_name'] ) ) {
	    $team_name = $_POST['team_name'];
	    $pitcher_card_id = $_POST['pitcher_card_id'];
	    $reliever_card_id = $_POST['reliever_card_id'];
	    $catcher_card_id = $_POST['catcher_card_id'];
	    $first_base_card_id = $_POST['first_base_card_id'];
	    $second_base_card_id = $_POST['second_base_card_id'];
	    $third_base_card_id = $_POST['third_base_card_id'];
	    $short_stop_card_id = $_POST['short_stop_card_id'];
	    $left_field_card_id = $_POST['left_field_card_id'];
	    $center_field_card_id = $_POST['center_field_card_id'];
	    $right_field_card_id = $_POST['right_field_card_id'];
	    $dh_card_id = $_POST['dh_card_id'];

	    // Update team information
	    $stmt = $pdo->prepare("UPDATE game_team SET team_name = ?, pitcher_card_id = ?, reliever_card_id = ?, catcher_card_id = ?, first_base_card_id = ?, second_base_card_id = ?, third_base_card_id = ?, short_stop_card_id = ?, left_field_card_id = ?, center_field_card_id = ?, right_field_card_id = ?, dh_card_id = ? WHERE game_team_id = ?");
	    $stmt->execute([$team_name, $pitcher_card_id, $reliever_card_id, $catcher_card_id, $first_base_card_id, $second_base_card_id, $third_base_card_id, $short_stop_card_id, $left_field_card_id, $center_field_card_id, $right_field_card_id, $dh_card_id, $id]);
	    
	    //Update the pitcher's batting order, in case pitcher was changed.
	    $stmt2 = $pdo->prepare("UPDATE game_team_player SET batting_order = 0 WHERE card_id = ?");
	    $stmt2->execute([$pitcher_card_id]);
	    
	    //Update the reliever's batting order, in case reliever was changed.
	    $stmt4 = $pdo->prepare("UPDATE game_team_player SET batting_order = -1 WHERE card_id = ?");
	    $stmt4->execute([$reliever_card_id]);
	    
	    $stmt3 = $pdo->prepare("UPDATE game_team_player gtp, game_player_stats gps 
	    				SET gtp.batting_order = 20 
	    				WHERE gps.card_id = gtp.card_id 
	    				AND gps.pitcher_or_batter = 'pitcher' 
	    				AND gtp.game_team_id = ? 
	    				AND gps.card_id NOT IN( ?, ? )");
	    $stmt3->execute([$id, $pitcher_card_id, $reliever_card_id]);

	    header('Location: game_teams.php');
	    exit;
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
			ORDER BY gtp.batting_order ASC");
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
			ORDER BY c.year, c.brand, c.number, c.player_name ASC 
			");
$stmt->execute([$game_season_id]);
$all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div id="game-form-wrapper">
</div>
<div id="green-space">
    &nbsp;
</div>
<div id="game-form">
    <h1>Edit Game Team</h1>
     <a href="game_teams.php">ALL GAME TEAMS</a> &nbsp; | &nbsp; <a href="game_seasons.php">ALL GAME SEASONS</a> &nbsp; | &nbsp; <a href="card_game.php">START A NEW GAME</a>
     <br><br>
  <div id="width-wrapper" style="width: 1260px;">
    <div id="team-div"  style="width: 420px;">
   <h3 style="text-align: center;">Update Team</h3>
   
  <form method="POST">    
   <div id="game-team">
        <label for="team_name" style="font-weight: bold;">Team Name:</label>
        <input type="text" style="width: 85px;" name="team_name" value="<?= htmlspecialchars($team['team_name']); ?>" required>
        <label style="font-weight: bold;">Season:</label>
        <label style="font-weight: bold; text-align: left;"><?= $team['season_name']; ?></label>
        <label style="font-weight: bold;">Club:</label>
        <label style="font-weight: bold; text-align: left;"><?= $team['club_name']; ?></label>
        <label style="font-weight: bold;">Active Players</label>
        <label style="font-weight: bold;"></label>
        <label for="pitcher_card_id"><span style="color: red;">***</span>Pitcher Card ID:</label>
        <input type="text" style="width: 35px;" name="pitcher_card_id" value="<?= $team['pitcher_card_id']; ?>"> 
        <label for="reliever_card_id"><span style="color: red;">***</span>Reliever Card ID:</label>
        <input type="text" style="width: 35px;" name="reliever_card_id" value="<?= $team['reliever_card_id']; ?>"> 
        <label for="catcher_card_id">Catcher Card ID:</label>
        <input type="text" style="width: 35px;" name="catcher_card_id" value="<?= $team['catcher_card_id']; ?>">
        <label for="first_base_card_id">First Base Card ID:</label>
        <input type="text" style="width: 35px;" name="first_base_card_id" value="<?= $team['first_base_card_id']; ?>">
        <label for="second_base_card_id">Second Base Card ID:</label>
        <input type="text" style="width: 35px;" name="second_base_card_id" value="<?= $team['second_base_card_id']; ?>">
        <label for="third_base_card_id">Third Base Card ID:</label>
        <input type="text" style="width: 35px;" name="third_base_card_id" value="<?= $team['third_base_card_id']; ?>">
        <label for="short_stop_card_id">Short Stop Card ID:</label>
        <input type="text" style="width: 35px;" name="short_stop_card_id" value="<?= $team['short_stop_card_id']; ?>">
        <label for="left_field_card_id">Left Field Card ID:</label>
        <input type="text" style="width: 35px;" name="left_field_card_id" value="<?= $team['left_field_card_id']; ?>">
        <label for="center_field_card_id">Center Field Card ID:</label>
        <input type="text" style="width: 35px;" name="center_field_card_id" value="<?= $team['center_field_card_id']; ?>">
        <label for="right_field_card_id">Right Field Card ID:</label>
        <input type="text" style="width: 35px;" name="right_field_card_id" value="<?= $team['right_field_card_id']; ?>">
        <label for="dh_card_id">DH Card ID:</label>
        <input type="text" style="width: 35px;" name="dh_card_id" value="<?= $team['dh_card_id']; ?>">
        
     
        <input type="submit" class="link-button" value="Update Team">
      
     </div>
    </form>
        <div style="color: white; margin-top: 10px;">
		<span style="color: red;">***</span>If you update the pitcher or reliever card ID, that pitcher card will get batting_order=0, the reliver card will get batting order = -1 and the previous pitcher/reliever card will get batting_order=20.<br><br>
		<span style="color: red;">****IMPORTANT: </span>The active team cards must have a batting_order value that is below 10. The active pitcher is always batting_order=0.<br>Non-active players are always batting_order=20.
        </div>
   
    <br>
    <hr>
    <br>
    
   <h3 style="text-align: center;">Add A Card To This Game Team</h3>
     <form id="addCardForm" method="POST">
         <label for="card" style="color: white; font-weight: bold;">Available Cards: </label>
         <br>
         <select id="card" name="card_id" style="font-size: 16px;" required>
             <?php foreach ($all_cards as $card): ?>
                 <option value="<?php echo htmlspecialchars($card['card_id']); ?>"><?php echo htmlspecialchars($card['year']) . ' ' . htmlspecialchars($card['brand']) . ' #' . htmlspecialchars($card['number']) . ' ' . htmlspecialchars($card['player_name']) . ' [' . htmlspecialchars($card['card_id']) . ']'; ?></option>
             <?php endforeach; ?>
         </select>
        <br>
        <br>
         <select id="pitcher_or_batter" name="pitcher_or_batter" style="font-size: 16px;"  required>
         	<option value="batter">batter</option>
         	<option value="pitcher">pitcher</option>
         </select>
         <span style="color: red;">***</span><span style="color: white;">Important: select pitcher, if it's a pitcher</span>
         <input type="hidden" name="game_team_id" value="<?php echo $id; ?>">
    	<br>
    	<br>
    	<input type="submit" class="link-button" value="Add Team Player">
    </form>
    
    <br>
    <hr>
    <br>
    </div>
    <div id="team-players" style="float: right;">
    	<!--display available players from the bench-->
    	<h3>Team Roster</h3>
    	<table id="roster-table" style="border-collapse: collapse; border: 1px solid gray;">
    		<tr>
    			<th>card_id</th>
    			<th>Player Name</th>
    			<th>Position</th>
    			<th></th>
    			<th>Batting Order</th>
    		</tr>
    		
        <?php 	$isBench = 0;
        	foreach ($team_players as $team_player): ?>
       
        	<?php 	
        		if( $team_player['batting_order'] > 9 && $isBench == 0) {
        			echo '<tr style="border-top: 6px solid black"><td colspan="6" style="font-weight: bold; font-size: 22px;">&#x25BC; BENCH &nbsp; <span style="font-size: 18px;">(*Non-active players have batting_order=20)</span></td></tr>';
        			echo '<tr style="border-top: 6px solid black">';
        			$isBench = 1;
        		} if( $team_player['batting_order'] > 9 && $isBench == 1) {
        			echo '<tr style="border-top: 2px solid red;">';		
        		} else {
        			echo '<tr>';
        		}
        	?>
    		 
    			
    			<td><?= $team_player['card_id']; ?>
    			</td>
    			<td><?= $team_player['player_name']; ?>
    			</td>
    			<td><?= $team_player['pitcher_or_batter']; ?>
    			</td>
    			<td><img style="width: 45px;" src="/images/Baseball-Cards/<?= $team_player['img_url']; ?>">
    			</td>
    			<td>
    				<table><tr style="background-color: transparent;"><td>
    					<form method="POST" id="bo-card_id-<?php echo $team_player['card_id']; ?>" >
    						<input type="hidden" name="card_id" value="<?= $team_player['card_id']; ?>">
    						<input type="text" name="batting_order" style="width: 25px;" value="<?= $team_player['batting_order'] ?>">
    			 			&nbsp; <input type="submit" class="link-button" value="Update B_O">
    			 		</form>
    			 	</td></tr>
    			 	</table>
    			</td>
    			
    		</tr>
    		<tr>
    			
    			<td></td>
    			
    				<?php if ($team_player['pitcher_or_batter'] == 'pitcher'){
    					echo '<td>ERA: ' . $team_player['era'] . '</td><td>SO: ' . $team_player['strikeouts'] . '</td><td></td>';
    					
    				} else {
    					$hrAvg = 0;
    					if( $team_player['at_bats'] > 0 ) {
    						$hrAvg = (600 * $team_player['home_runs']) / $team_player['at_bats'];
    					}
    					echo '<td>AVG: ' . number_format($team_player['batting_avg'], 3) . '</td><td>HR: ' . $team_player['home_runs'] . '</td><td>HRA:' . number_format($hrAvg, 1) . '</td>';
    					
    				}
    				
    				?>
    			<td>
    			</td>
    		</tr>
    	
        <?php endforeach; ?>
    	</table>
    </div>
    </div>
    
 </div>   
    
</body>
</html>


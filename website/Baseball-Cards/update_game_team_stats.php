<?php

//Make sure they are logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

// Database connection
$host = 'p3nlmysql39plsk.secureserver.net';
    	$db   = 'ph21100054196_';
    	$user = 'collector';
    	$pass = 'Piltocat22';
    	$port = "3306";
    	$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";


try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    	$player_stats = json_decode( $_POST['player_stats'] );
        $home_team_id = $_POST['home_team_id'];
        $away_team_id = $_POST['away_team_id'];
        $home_team_runs = $_POST['home_team_runs'];
        $away_team_runs = $_POST['away_team_runs'];
        $game_season_id = $_POST['game_season_id'];
        $home_team_win = 0;
        $away_team_win = 0;
        $home_team_loss = 0;
        $away_team_loss = 0;
        
        if( $home_team_runs > $away_team_runs) {
        	$home_team_win = "1";
        	$away_team_loss = "1";
        } else {
        	$away_team_win = "1";
        	$home_team_loss = "1";
        }

	//Insert final record of the game
        $stmt0 = $pdo->prepare("INSERT INTO game 
        	(home_team_id, away_team_id,
        	total_runs_home, total_runs_away,
        	is_final, game_season_id)
        	VALUES ( :home_team_id,:away_team_id,
        	:total_runs_home, :total_runs_away,
        	1, :game_season_id
        	)
        	");
        $stmt0->bindParam(':home_team_id', $home_team_id, PDO::PARAM_INT);
        $stmt0->bindParam(':away_team_id', $away_team_id, PDO::PARAM_INT);
        $stmt0->bindParam(':total_runs_home', $home_team_runs, PDO::PARAM_INT);
        $stmt0->bindParam(':total_runs_away', $away_team_runs, PDO::PARAM_INT);
        $stmt0->bindParam(':game_season_id', $game_season_id, PDO::PARAM_INT);
        $stmt0->execute();
	$stmt0->closeCursor();
		
        // Update home team
        $stmt = $pdo->prepare("UPDATE game_team 
        	SET wins = wins + :home_team_win,
        	losses = losses + :home_team_loss
        	WHERE game_team_id = :home_team_id;
        	UPDATE game_team 
		SET wins = wins + :away_team_win,
		losses = losses + :away_team_loss
        	WHERE game_team_id = :away_team_id;
        	");
        $stmt->bindParam(':home_team_win', $home_team_win, PDO::PARAM_INT);
        $stmt->bindParam(':home_team_loss', $home_team_loss, PDO::PARAM_INT);
        $stmt->bindParam(':home_team_id', $home_team_id, PDO::PARAM_INT);
        $stmt->bindParam(':away_team_win', $away_team_win, PDO::PARAM_INT);
        $stmt->bindParam(':away_team_loss', $away_team_loss, PDO::PARAM_INT);
        $stmt->bindParam(':away_team_id', $away_team_id, PDO::PARAM_INT);
        $stmt->execute();
	$stmt->closeCursor();
        // Update players
        $debug_string = "Debug: ";
        foreach( $player_stats as $player ){
        
        	$debug_string = $debug_string . ", " . implode( " x ", $player[0]) . " * " . implode( " x ", $player[1]) . " * " . implode( " x ", $player[2]);
        	$playerAtbats = $player[1][1];
        	//convert null to zero
        	$playerAtbats ??= '0';
        	
        	$playerHits = $player[2][1];
        	$playerHits ??= '0';
        	
        	$playerStrikeouts = $player[3][1];
        	$playerStrikeouts ??= '0';
        	
        	$playerWalks = $player[4][1];
        	$playerWalks ??= '0';
        	
        	$playerHomeRuns = $player[5][1];
        	$playerHomeRuns ??= '0';
        	
        	$playerStolenBases = $player[6][1];
        	$playerStolenBases ??= '0';
        	
        	$playerWins = $player[7][1];
        	$playerWins ??= '0';
        	
        	$playerLosses = $player[8][1];
        	$playerLosses ??= '0';
        	
        	$playerInningsPitched = $player[9][1];
        	$playerInningsPitched ??= '0';
        	
        	$playerRunsAgainst = $player[10][1];
        	$playerRunsAgainst ??= '0';
        	
        	$playerStrikeoutsAgainst = $player[11][1];
        	$playerStrikeoutsAgainst ??= '0';
        	
        	$playerWalksAgainst = $player[12][1];
        	$playerWalksAgainst ??= '0';
 
		$stmt2 = $pdo->prepare("
			UPDATE game_player_stats gps, game_team gt
			SET at_bats = at_bats + :atbats,
			hits = hits + :hits,
			strikeouts = strikeouts + :strikeouts,
			walks = walks + :walks,
			home_runs = home_runs + :home_runs,
			stolen_bases = stolen_bases + :stolen_bases,
			gps.wins = gps.wins + :wins,
			gps.losses = gps.losses + :losses,
			innings_pitched = innings_pitched + :innings_pitched,
			runs_against = runs_against + :runs_against,
			strikeouts_against = strikeouts_against + :strikeouts_against,
			walks_against = walks_against + :walks_against		
			WHERE card_id = :card_id
			AND gps.game_team_id = gt.game_team_id 
			AND gt.game_season_id = :game_season_id ");
		$stmt2->bindParam(':atbats', $playerAtbats, PDO::PARAM_INT);
		$stmt2->bindParam(':hits', $playerHits, PDO::PARAM_INT);
		$stmt2->bindParam(':strikeouts', $playerStrikeouts, PDO::PARAM_INT);
		$stmt2->bindParam(':walks', $playerWalks, PDO::PARAM_INT);
		$stmt2->bindParam(':home_runs', $playerHomeRuns, PDO::PARAM_INT);
		$stmt2->bindParam(':stolen_bases', $playerStolenBases, PDO::PARAM_INT);
		$stmt2->bindParam(':wins', $playerWins, PDO::PARAM_INT);
		$stmt2->bindParam(':losses', $playerLosses, PDO::PARAM_INT);
		$stmt2->bindParam(':card_id', $player[0][1], PDO::PARAM_INT);
		$stmt2->bindParam(':innings_pitched', $playerInningsPitched, PDO::PARAM_INT);
		$stmt2->bindParam(':runs_against', $playerRunsAgainst, PDO::PARAM_INT);
		$stmt2->bindParam(':strikeouts_against', $playerStrikeoutsAgainst, PDO::PARAM_INT);
		$stmt2->bindParam(':walks_against', $playerWalksAgainst, PDO::PARAM_INT);
        	$stmt2->bindParam(':game_season_id', $game_season_id, PDO::PARAM_INT);
		
		$stmt2->execute();
		$stmt2->closeCursor();
	}
        echo "Teams and players updated successfully "; // . $debug_string;
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<?php

//Make sure they are logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

// Database connection
require 'db.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
        $home_team_id = $_POST['home_team_id'];
        $away_team_id = $_POST['away_team_id'];
	
	//Pull the card_id's for the teams' full benches of players
	$stmt2 = $pdo->prepare("
		SELECT :home_team_id_select as game_team_id, c.*, cs.*, gtp.batting_order 
		FROM card c, card_stats cs, game_team_player gtp 
				WHERE c.card_id=cs.card_id 
				AND gtp.card_id = c.card_id
				AND gtp.batting_order < 10
				AND gtp.game_team_id = :home_team_id
			UNION
		SELECT :away_team_id_select as game_team_id, c.*, cs.*, gtp.batting_order 
				FROM card c, card_stats cs, game_team_player gtp
				WHERE c.card_id=cs.card_id 
				AND gtp.card_id = c.card_id
				AND gtp.batting_order < 10
				AND gtp.game_team_id = :away_team_id
		ORDER BY game_team_id, batting_order
	" );
        
	
        $stmt2->execute(['home_team_id' => $home_team_id, 'home_team_id_select' => $home_team_id, 'away_team_id_select' => $away_team_id, 'away_team_id' => $away_team_id] );
        
	$cards = [];
	$myCtr = 0;
	$batterNumberHome = 1;
	$batterNumberAway = 1;
	while ($row = $stmt2->fetch(PDO::FETCH_ASSOC) ) {
		$dugout_img = "";
		$img_back_data = "";
		$seasonYear = $row['year'] - 1;
		if( $row['game_team_id'] == $home_team_id ) {
			if( $row['pitcher_or_batter'] == 'batter' ) {
				$dugout_img = "<img id=home-batter-" . $batterNumberHome . " data-card-id=" . $row['card_id'] . " data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-ingame-walks=\"0\"  data-ingame-atbats=\"0\" data-ingame-home-runs=\"0\" data-ingame-stolen-bases=\"0\" class=\"player " . $row['grade_company'] .  "\" src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
				
				$img_back_data = "<img id=home-batter-" . $batterNumberHome . "-back data-card-id=" . $row['card_id'] . " data-player-name=\"". $row['player_name'] . "\" data-season-batting-avg=\"". $row['batting_avg'] . "\"  data-season-at-bats=\"". $row['at_bats'] . "\" data-season-assists=\"". $row['assists'] . "\" data-season-putouts=\"". $row['putouts'] . "\" data-season-games-in-position=\"". $row['games_in_position'] . "\" data-season-2b=\"". $row['doubles'] . "\" data-season-3b=\"". $row['triples'] . "\" data-season-hr=\"". $row['home_runs'] . "\" data-season-sb=\"". $row['stolen_bases'] . "\" data-season-so=". $row['strikeouts'] . " data-season-bb=\"". $row['walks'] . "\"  data-season-year=\"". $seasonYear . "\" src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\" class=\"" . $row['grade_company'] .  "\"  >";
				$batterNumberHome++;
			} else if ( $row['pitcher_or_batter'] == 'pitcher' ) {				
				//pitcher or reliever?
				if( $row['batting_order'] == 0) {
					$dugout_img = "<img id=home-pitcher class=\"player " . $row['grade_company'] .  "\"  data-card-id=" . $row['card_id'] . "   data-ingame-wins=0   data-ingame-losses=0  data-ingame-ip=0 data-ingame-runs-against=0 data-ingame-strikeouts-against=0 data-ingame-walks-against=0 src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
					$img_back_data = "<img id=home-pitcher-back  data-card-id=" . $row['card_id'] . " data-player-name=\"". $row['player_name'] . "\"   data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-season-era=". $row['era'] . " data-season-ip=". $row['innings_pitched'] . " data-season-so=". $row['strikeouts'] . " data-season-bb=". $row['walks'] . "  data-season-year=\"". $seasonYear . "\" src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\"  class=\"" . $row['grade_company'] .  "\" >";
				} else if( $row['batting_order'] == -1) {
					$dugout_img = "<img id=home-reliever class=\"player " . $row['grade_company'] .  "\"  data-card-id=" . $row['card_id'] . "   data-ingame-wins=0   data-ingame-losses=0  data-ingame-ip=0 data-ingame-runs-against=0 data-ingame-strikeouts-against=0 data-ingame-walks-against=0 src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
					$img_back_data = "<img id=home-reliever-back  data-card-id=" . $row['card_id'] . " data-player-name=\"". $row['player_name'] . "\"   data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-season-era=". $row['era'] . " data-season-ip=". $row['innings_pitched'] . " data-season-so=". $row['strikeouts'] . " data-season-bb=". $row['walks'] . "  data-season-year=\"". $seasonYear . "\" src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\"  class=\"" . $row['grade_company'] .  "\" >";
				}
			}
		} else {
			if( $row['pitcher_or_batter'] == 'batter' ) {
				$dugout_img = "<img id=away-batter-" . $batterNumberAway . "  data-ingame-atbats=\"0\" data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-ingame-walks=\"0\"  data-ingame-home-runs=\"0\" data-ingame-stolen-bases=\"0\"  class=\"player " . $row['grade_company'] .  "\"   data-card-id=" . $row['card_id'] . " src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
				
				$img_back_data = "<img id=away-batter-" . $batterNumberAway . "-back data-card-id=" . $row['card_id'] . "  data-player-name=\"". $row['player_name'] . "\"  data-season-batting-avg=\"". $row['batting_avg'] . "\"  data-season-at-bats=\"". $row['at_bats'] . "\" data-season-assists=\"". $row['assists'] . "\" data-season-putouts=\"". $row['putouts'] . "\" data-season-games-in-position=\"". $row['games_in_position'] . "\" data-season-2b=\"". $row['doubles'] . "\" data-season-3b=\"". $row['triples'] . "\" data-season-hr=\"". $row['home_runs'] . "\" data-season-sb=\"". $row['stolen_bases'] . "\" data-season-so=\"". $row['strikeouts'] . "\" data-season-bb=\"". $row['walks'] . "\" data-season-year=\"". $seasonYear . "\" src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\"  class=\"" . $row['grade_company'] .  "\" >";
				$batterNumberAway++;
			} else if ( $row['pitcher_or_batter'] == 'pitcher' ) {
				
				//pitcher or reliever?
				if( $row['batting_order'] == 0) {
					$dugout_img = "<img id=away-pitcher class=\"player " . $row['grade_company'] .  "\" data-card-id=" . $row['card_id'] . "   data-ingame-wins=0   data-ingame-losses=0  data-ingame-ip=0  data-ingame-runs-against=0 data-ingame-strikeouts-against=0 data-ingame-walks-against=0 src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
					$img_back_data = "<img id=away-pitcher-back data-card-id=" . $row['card_id'] . "  data-player-name=\"". $row['player_name'] . "\"   data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-season-era=\"". $row['era'] . "\" data-season-ip=\"". $row['innings_pitched'] . "\" data-season-so=\"". $row['strikeouts'] . "\"; data-season-bb=\"". $row['walks'] . "\" data-season-year=\"". $seasonYear . "\"  src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\"  class=\"" . $row['grade_company'] .  "\" >";
				} else if( $row['batting_order'] == -1) {
					$dugout_img = "<img id=away-reliever class=\"player " . $row['grade_company'] .  "\" data-card-id=" . $row['card_id'] . "   data-ingame-wins=0   data-ingame-losses=0  data-ingame-ip=0  data-ingame-runs-against=0 data-ingame-strikeouts-against=0 data-ingame-walks-against=0 src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"position: relative; z-index: 850;\">";
					$img_back_data = "<img id=away-reliever-back data-card-id=" . $row['card_id'] . "  data-player-name=\"". $row['player_name'] . "\"   data-ingame-hits=\"0\" data-ingame-strikeouts=\"0\" data-season-era=\"". $row['era'] . "\" data-season-ip=\"". $row['innings_pitched'] . "\" data-season-so=\"". $row['strikeouts'] . "\"; data-season-bb=\"". $row['walks'] . "\" data-season-year=\"". $seasonYear . "\"  src=\"/images/Baseball-Cards/" . $row['img_url'] . "\" style=\"width: 150px;\"  class=\"" . $row['grade_company'] .  "\" >";
				}
			}
		}
		
		$cards[$myCtr]['dugout_img'] = $dugout_img;
		$cards[$myCtr]['img_back_data'] = $img_back_data;
		$myCtr++;
	}
	echo json_encode( $cards );
	
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

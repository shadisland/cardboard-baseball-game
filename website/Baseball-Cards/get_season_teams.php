   <?php
//Make sure they are logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}  
 $game_season_id = $_POST['game_season_id'];
 $game_season_id2 = $game_season_id;
 
    	// Database connection
    	require 'db.php';
    	
    	$game_teams = [];
        try {
                $conn = new PDO($dsn, $user, $pass);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//Load teams Query    
    		$stmt = $conn->prepare("
    			SELECT distinct team_name, game_team_id, wins, losses, game_team_card_id, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(runs_for) as runs, SUM(runs_against) as runs_against FROM
			(
			SELECT gt.team_name, gt.game_team_id, gt.wins, gt.losses, game_team_card_id, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(total_runs_home) as runs_for, SUM(total_runs_away) as runs_against
			FROM game_team gt, game g, game_season_team gst
			WHERE gt.game_team_id = g.home_team_id
			AND gst.game_season_id = ?
			    GROUP BY gt.team_name, gt.wins, gt.losses
			UNION ALL
			SELECT gt.team_name, gt.game_team_id, gt.wins, gt.losses, game_team_card_id, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(total_runs_away) as runs_for, SUM(total_runs_home) as runs_against
			FROM game_team gt, game g, game_season_team gst
			WHERE gt.game_team_id = g.away_team_id
			AND gst.game_season_id = ?
			    GROUP BY gt.team_name, gt.wins, gt.losses
			) t1
			GROUP BY team_name, game_team_id, wins, losses, game_team_card_id, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id
			ORDER BY 3 DESC, 4 ASC, 14 DESC, 15 ASC ");

    		$stmt->execute([$game_season_id, $game_season_id2]);
    		$game_teams = $stmt->fetchAll();
    		//Return game_teams object
		echo json_encode( $game_teams );
		
            } catch (\PDOException $e) {
	        echo "Connection failed: " . $e->getMessage();
	    }
	    $conn = null;
	    
	    
?>
<?php
//Page requires 'user' or 'admin' level authentication
require 'session_verification_user_level.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Shadisland - Cardboard Baseball Game</title>
    
    <style> 
	#team-card-away:hover {
		transform: scale(4) translate(25%, -40%) !important;
		z-index: 1000 !important;
	}
	#team-card-home:hover {
		transform: scale(4) translate(-25%, -40%) !important;
		z-index: 1000 !important;
	}
	#away-dugout img:hover{
		transform: scale(3) ;
		z-index: 1000 !important;
	}
	#home-dugout img:hover{
		transform: scale(3) ;
		z-index: 1000 !important;
	}
	.game-title::before{
		content: url('/images/lightning-bolt2-left.png');
		/*transform: scaleX(-1);*/
		width: 35px;
		filter: hue-rotate(180deg) brightness(0.4);
		display: inline;
	}
	.game-title::after{
		content: url('/images/lightning-bolt2.png');
		width: 35px;
		filter: hue-rotate(180deg) brightness(0.4);
		display: inline;
	}
    </style>
     <link rel="stylesheet" href="/css/game.css">
     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/animation.css">
     <link rel="stylesheet" type="text/css" href="/water-animation/waterstyle.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" />
    
    <?php require 'head_scripts_include.php';?>
    <script src="card_game.js"></script>
    
    <script>

    </script>
    
</head>
<body>

    <?php
    	// Database connection
    	require 'db.php';
          	
    	$game_teams = [];
    	$season_standings = [];
    	$game_season_id = "-1";
    	$game_season_id2 = "-1";
    	$game_season_id3 = "-1";
    	$game_season_id4 = "-1";
        try {
                $conn = new PDO($dsn, $user, $pass);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $game_teams = [];
                if( isset($_GET['game_season_id']) ) {
                	$game_season_id = $_GET['game_season_id'];
                	$game_season_id2 = $game_season_id;
                	if( $user_role != 'admin' ){
                		//this is for security.
                		//if role='user' force season 1, even if they change the URL query string to a different game_season_id
                		$game_season_id = 1;
                	}
	//Load teams Query    
			$stmt = $conn->prepare("    			
				SELECT c.img_url, gt.team_name, gt.game_team_id, gt.game_team_card_id, gt.logo_url, gt.pitcher_card_id, gt.reliever_card_id, gt.catcher_card_id, gt.first_base_card_id,gt.second_base_card_id, gt.short_stop_card_id, gt.third_base_card_id, gt.left_field_card_id, gt.center_field_card_id, gt.right_field_card_id
				FROM game_team gt, card c
				WHERE gt.game_season_id = ?
				AND c.card_id = gt.game_team_card_id
				GROUP BY gt.team_name, gt.game_team_id, gt.game_team_card_id, gt.logo_url, gt.pitcher_card_id, gt.reliever_card_id, gt.catcher_card_id, gt.first_base_card_id, gt.second_base_card_id, gt.short_stop_card_id, third_base_card_id, gt.left_field_card_id, gt.center_field_card_id, gt.right_field_card_id		
		");

			
			$stmt->execute([$game_season_id ]);
			$game_teams = $stmt->fetchAll();
	    	}
	// Fetch all seasons
			$tmpQueryString = '';
			if( $_SESSION['user_role'] == 'admin' ) {
				$tmpQueryString = 'SELECT * FROM game_season 
						WHERE game_season_id > 0  
						ORDER by 1';
			} else {
				//Limited season choices for non-admin user
				//'Alpha' is game_season_id 1
				$tmpQueryString = 'SELECT * 
						FROM game_season 
						WHERE game_season_id > 0  
						AND access_level = \'user\'
						ORDER by 1';
			}
			
			
			$stmt10 = $conn->query( $tmpQueryString );
			$stmt10->execute();
			$seasons = $stmt10->fetchAll();
			$game_season_name = "";
			if( isset( $_GET['game_season_id'] ) ) {
				foreach ($seasons as $season): 
					if( $season['game_season_id'] == $_GET['game_season_id'] ) {			
						$game_season_name = $season['season_name']; 
					}
				endforeach;
			}
			
	// Fetch all games from this season to use in the standings			
			$stmt11 = $conn->prepare("SELECT  team_name, sum(wins) as total_wins, sum(losses) as total_losses, sum(runs_for) as total_runs_for, sum(runs_against) as total_runs_against FROM
				(
					SELECT  gt.team_name, count(g.game_id) as wins, 0 as losses, sum(g.total_runs_home) as runs_for, sum(g.total_runs_away) as runs_against
							FROM game g,  game_team gt
							WHERE g.game_season_id = ?
							AND g.total_runs_home > g.total_runs_away
							AND gt.game_team_id = g.home_team_id 
					GROUP BY gt.team_name 
					UNION ALL
					SELECT  gt.team_name, count(g.game_id) as wins, 0 as losses, sum(g.total_runs_away) as runs_for, sum(g.total_runs_home) as runs_against
							FROM game g,  game_team gt
							WHERE g.game_season_id = ?
							AND g.total_runs_away > total_runs_home
							AND gt.game_team_id = g.away_team_id                       
					GROUP BY gt.team_name  
					UNION ALL
					SELECT gt.team_name, 0 as wins, count(g.game_id)  as losses, sum(g.total_runs_home) as runs_for, sum(g.total_runs_away) as runs_against
							FROM game g,  game_team gt
							WHERE g.game_season_id = ?
							AND g.total_runs_home < g.total_runs_away
							AND gt.game_team_id = g.home_team_id 
					GROUP BY gt.team_name 
					UNION ALL
					SELECT gt.team_name, 0 as wins, count(g.game_id)  as losses, sum(g.total_runs_away) as runs_for, sum(g.total_runs_home) as runs_against
							FROM game g,  game_team gt
							WHERE g.game_season_id = ?
							AND g.total_runs_away < total_runs_home
							AND gt.game_team_id = g.away_team_id                       
					GROUP BY gt.team_name 
					UNION ALL
					SELECT gt.team_name, 0 as wins, 0  as losses, 0 as runs_for, 0 as runs_against
							FROM game_team gt
							WHERE gt.game_season_id = ?
							AND gt.game_team_id not in (
								SELECT home_team_id
								FROM game
								WHERE game_season_id = ?
							)
							AND gt.game_team_id not in (
								SELECT away_team_id
								FROM game
								WHERE game_season_id = ?
							)
					GROUP BY gt.team_name   
				) t1
				GROUP BY team_name			 
				ORDER by 2 DESC, 3 ASC, 4 DESC, 5 ASC
			");
			$game_season_id2 = $game_season_id;
			$game_season_id3 = $game_season_id;
			$game_season_id4 = $game_season_id;
			$stmt11->execute([$game_season_id, $game_season_id2, $game_season_id3, $game_season_id4,$game_season_id, $game_season_id2, $game_season_id3]);
			//$stmt11->execute([1,1,1,1]);
			$season_standings = $stmt11->fetchAll();
	
	
            } catch (\PDOException $e) {
	        echo "Connection failedaa: " . $e->getMessage();
	    }
	    $conn = null;
         
    ?>
<?php require 'header.php';?>
 
	<h2>Cardboard Baseball</h2>

    <div id=stadium-wrapper>
    	
    	<div id="bleachers-scoreboard" style="top: 112px;left: 17px;padding: 10px; position: absolute;border: 2px solid white; background-color: #54796d;color: white;font-weight: 400; font-size: 22px;font-family: Overpass; border-top: 2px solid yellow; border-left: 2px solid yellow; z-index: 1100;">
	    		<div style="text-align: center; letter-spacing: 10px;">VISIBLE CONTACT PARK</div>
			   <table class="scoreboard-table" style="">
			   	    <tr  class="scoreboard-row">
			   		<th></th><th>&nbsp;</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th style=" letter-spacing: -3px;">10</th>
			   		<th class="scoreboard-extra-innings">11</th><th class="scoreboard-extra-innings">12</th><th class="scoreboard-extra-innings">13</th><th class="scoreboard-extra-innings">14</th><th class="scoreboard-extra-innings">15</th><th class="scoreboard-extra-innings">16</th><th class="scoreboard-extra-innings">17</th><th class="scoreboard-extra-innings">18</th><th class="scoreboard-extra-innings">19</th><th class="scoreboard-extra-innings" style="">20</th>
			   		<th>R</th><th>H</th><th>E</th><th>S</th><th>B</th>
			   	    </tr>
			   	    <tr class="scoreboard-row">
			   		<td><span id="scoreboard-top-inning" >&#9679;</span></td><td  style="width: 86px;">
			   		<span id="away-team-name">&nbsp;</span>
			   		</td>
			   		<td><span id="inning-1-away">0</span></td><td><span id="inning-2-away"></span></td><td><span id="inning-3-away"></span></td><td><span id="inning-4-away"></span></td><td><span id="inning-5-away"></span></td><td><span id="inning-6-away"></span></td><td><span id="inning-7-away"></span></td><td><span id="inning-8-away"></span></td><td><span id="inning-9-away"></span></td><td><span id="inning-10-away"></span></td>
			   		
			   		<td class="scoreboard-extra-innings"><span id="inning-11-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-12-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-13-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-14-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-15-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-16-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-17-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-18-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-19-away"></span></td><td class="scoreboard-extra-innings"><span id="inning-20-away"></span></td>
			   		
			   		<td><span id="total-runs-away">0</span></td><td><span id="total-hits-away">0</span></td><td><span id="total-errors-away">0</span></td><td><span id="total-so-away">0</span></td><td><span id="total-bb-away">0</span></td>
			   	    </tr>
			   	    <tr class="scoreboard-row"> 
			   		<td><span id="scoreboard-bottom-inning"></td><td style="font-weight: 600;">
			   		<span id="home-team-name"></span>
			   		</td><td><span id="inning-1-home"></span></td><td><span id="inning-2-home"></span></td><td><span id="inning-3-home"></span></td><td><span id="inning-4-home"></span></td><td><span id="inning-5-home"></span></td><td><span id="inning-6-home"></span></td><td><span id="inning-7-home"></span></td><td><span id="inning-8-home"></span></td><td><span id="inning-9-home"></span></td><td><span id="inning-10-home"></span></td>
			   		
			   		<td class="scoreboard-extra-innings"><span id="inning-11-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-12-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-13-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-14-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-15-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-16-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-17-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-18-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-19-home"></span></td><td class="scoreboard-extra-innings"><span id="inning-20-home"></span></td>
			   		
			   		<td><span id="total-runs-home">0</td><td><span id="total-hits-home">0</span></td><td><span id="total-errors-home">0</td><td><span id="total-so-home">0</span></td><td><span id="total-bb-home">0</span></td>
			   	    </tr>
			   	    <tr class="scoreboard-row" style="margin-top: 10px;"> 
			   		<th colspan="2">AT BAT</th>
			   		<th colspan="3">OUTS</th>
			   		<th colspan="3">HITS</th>
			   		<th colspan="3">E</th>
			   		<th colspan="3">SO</th>
			   		<th colspan="3">BB</th>
			   	    </tr>
			   	    <tr class="scoreboard-row"> 
			   	    	<td></td>
			   	    	<td><span id="scoreboard-batter">1</span></td>
			   	    	<td colspan="3">
			   	    		<div id="scoreboard-outs">
			   	    			<span id="out-1" class="out-dot"></span>
			   	    			<span id="out-2" class="out-dot"></span>
			   	    			<span id="out-3" class="out-dot"></span>
			   	    		</div>
			   	    	</td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-hits">0</span></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-errors">0</span></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-strikeouts">0</span></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-walks">0</span></td>
			   	    </tr>
			   </table>   
    	</div>
	<div id="green-monster-season-name" style="width: 350px; z-index: 1000; position: absolute; top: 110px; left: 880px; text-align: right; color: white; font-size: 40px; text-shadow: 4px 4px 8px darkblue; margin: 0px 0px 0px 0px;">
		<?php 
			if( isset( $_GET['game_season_id'] ) ) {
		?>
			<div class="game-title"><span style="display: inline; font-size: 22px; font-weight: 300; margin-bottom: 0px; font-family: Eurostile_MN_Extended_Bold;">Cardboard Baseball</span>
			</div>

			<div id="game-season" style="text-align: center;" data-game-season-id="<?= $_GET['game_season_id']; ?>"><?= $game_season_name; ?> Season&nbsp;</div>
		<?php
			}
		?>
	</div>
	<div style="position: absolute; display: block; width: 700px; top: 135px; left: 315px; z-index: 1000;">
		<div style="position: relative; margin: auto; perspective: 300px; perspective-origin: center bottom; height: 90px; width: 101px;">
			<img src="/images/Visible-Contact-BW.jpg" style="position: absolute; opacity: 0.8; max-height: 90px; float: left; margin: 0px 15px 0px 0px; transform: rotateX(-45deg);">
		</div>
    	</div>
    	<div id="green-monster-1" style="top:112px; left: 519px;position: absolute;width: 401px; height: 125px;background-color: #54796d; border-top: 2px solid yellow; border-bottom: 15px solid tan; z-index: 500;">
		&nbsp; 
    	</div>
    	<div id="green-monster-2" style="top:112px; left: 920px;position: absolute;width: 317px; height: 125px;background-color: #54796d; border-top: 2px solid yellow; border-right: 2px solid yellow; text-align: right; z-index: 500;">   	
    		&nbsp;
    	</div>
    	<div id="outfield" style="overflow:hidden;position: relative;width: 1215px; height: 400px; border-left: 2px solid yellow; border-right: 2px solid yellow;background-color: #54796d;border-radius: 0px;">
    		&nbsp;
    		<div id="deep-centerfield" style="width: 1335px;height: 1335px;border-top: 25px solid tan;background-color: lightgreen;border-radius: 50%;position: absolute; top:0px; left: -60px;"">
    		   CTR
    		</div>
    		<div id="left-fielder" style="width: 50px; height: 50px; position: absolute;top: 225px; left: 200px; z-index: 400;">
			
		</div>
    		<div id="center-fielder" style="width: 50px; height: 50px; position: absolute;top: 105px; left: 590px; z-index: 400;">
			
		</div>
    		<div id="right-fielder" style="width: 50px; height: 50px; position: absolute;top: 225px; right: 200px; z-index: 400;">
    			
		</div>

		 <div id=bleachers-left style="margin: -240px 0px 0px -245px;padding: 200px; position: absolute; background-color: #54796d;border-right: 15px solid #54796d; rotate: z 45deg;">
		   BL
    		</div>
    		<div id=bleachers-right style="margin: -239px 0px 0px 1026px;padding: 200px; position: absolute; background-color: #54796d;border-left: 15px solid #54796d; rotate: z -45deg;">
		   BR
    		</div>
    			
    		<div id=bleachers-outs style="top: 20px;left: 25px;padding: 10px; position: absolute;border: 1px solid gray; background-color: #54796d;color: white;font-weight: bold; font-size: 22px;">
		   Outs: <span id="inning-outs">0</span>
    		</div>
    		<div id=bleachers-inning style="top: 80px;left: 25px;padding: 10px; position: absolute;border: 1px solid gray; background-color: #54796d;color: white;font-weight: bold; font-size: 22px;">
		   Inning: <span id="inning-number">1</span>
    		</div>
    	</div>
    	<div id="infield" style="overflow:hidden;position: relative;width: 1217px; height: 750px;border: 0px solid darkgray;background-color: lightgreen;">
    		&nbsp;
    		<div id=diamond style="width: 325px; height: 325px;position: absolute; border: 30px solid tan;background-color: lightgreen; rotate: z 45deg; bottom: 250px; left: 413px;z-index: 140;">
    		   &nbsp;
    		</div>
    		<div id="infield-circle" style="background-color: tan;width: 700px;height: 700px; border-radius: 50%;position: absolute; top: 0px; left: 250px; z-index: 0;">
    		   &nbsp;
    		</div>
    		<div id="first-base-circle" style="background-color: tan;width: 125px;height: 125px; border-radius: 50%;position: absolute; top: 240px; right: 278px; z-index: 200;">
    		   &nbsp;
    		</div>
    		<div id="second-base-circle" style="background-color: tan;width: 110px;height: 110px; border-radius: 50%;position: absolute; top: 0px; left: 552px; z-index: 200;">
    		   &nbsp;
    		</div>
    		<div id="third-base-circle" style="background-color: tan;width: 125px;height: 125px; border-radius: 50%;position: absolute; top: 240px; left: 273px; z-index: 200;">
    		   &nbsp;
    		</div>
    		<div id=foul-line-left style="width: 3px; height: 900px; position: absolute; background-color: white; rotate: z -45deg; bottom: 56px; left: 287px; z-index: 250;">
		   L
    		</div>
    		<div id=stands-left style="padding: 400px; position: absolute; background-color: #54796d;border-top: 15px solid #54796d; border-right: 15px solid #54796d;top: 140px; left: -535px; rotate: z 45deg;z-index: 300;">
		   SL
    		</div>
    		<div id=foul-line-right style="width: 3px; height: 900px; position: absolute; background-color: white; rotate: z 45deg; bottom: 60px; right: 290px; z-index: 250; ">
		   R
    		</div>
    		<div id=stands-right style="padding: 400px; position: absolute; background-color: #54796d;border-top: 15px solid #54796d; border-left: 15px solid #54796d;rotate: z -45deg; top: 140px; right: -525px;z-index: 300;">
		   SR
    		</div>
    		<div id=grass-left style="padding: 400px; position: absolute; background-color: lightgreen; top: 175px; left: -400px; rotate: z 45deg; z-index: 250;">
		   GL
    		</div>
    		<div id=grass-right style="padding: 400px; position: absolute; background-color: lightgreen; top: 175px; right: -400px; rotate: z 45deg; z-index: 250;">
		   GL
    		</div>
    		<div id="third-baseman"  style="width: 50px; position: absolute; top: 140px; left: 325px; z-index: 400;">
    		</div>
    		<div id="short-stop"  style="width: 50px; position: absolute; top: 20px; left: 455px; z-index: 400;">
    		</div>
    		<div id="second-baseman" style="width: 50px; position: absolute; top: 20px; left: 700px; z-index: 400;">
    		</div>
    		<div id="first-baseman" style="width: 50px; position: absolute; top: 110px; left: 830px; z-index: 375;">
    		</div>
    		<div id="pitcher" style="width: 50px; position: absolute; top: 235px; left: 555px; z-index: 400;">
    		</div>
    		<div id="catcher" style="width: 50px; position: absolute; bottom: -50px; left: 555px; z-index: 850;">
    		</div>
    		
		   <div id="first-base" style="width: 15px; height: 15px;  background-color: white; position: absolute; bottom: 435px; right: 365px;rotate: z -45deg; z-index: 250;">
			&nbsp;
		   </div>
		   <div id="second-base" style="width: 15px; height: 15px;  background-color: white; position: absolute; top: 75px; right: 601px;rotate: z -45deg; z-index: 250;">
			&nbsp;
		   </div>
		   <div id="third-base" style="width: 15px; height: 15px;  background-color: white; position: absolute; bottom: 435px; left: 358px;rotate: z -45deg; z-index: 250;">
		   	&nbsp;
		   </div>
    		
		   <div id="coach-box-left" style="width: 18px; height: 45px;  border: 2px solid white; position: absolute; bottom: 360px; left: 328px;rotate: z -45deg;z-index: 250; ">
			&nbsp;
		   </div>
		   <div id="coach-box-right" style="width: 18px; height: 45px; border: 2px solid white; position: absolute; bottom: 360px; right: 328px;rotate: z 45deg; z-index: 250; ">
			&nbsp;
    		   </div>
    		
			<span style="color: white;font-weight: bold;"></span>
			<style>
			.new-console-list {
				padding: 0px;
				margin: 0px;
				list-style-type: none;
				margin-block-start: 0em;
				margin-block-end: 0em;
				font-size: 0px;
				position: relative;
			}
			li.new-console-list-item {
				padding: 0px;
				margin: 0px;
				list-style-type: none;
			}

			.new-console-list-item::after {
			    content: '';
			    height: 0px;
			    width: 0px;
			    background: white;
			    display: none !important;
			    position: absolute;
			    top: 0px;
			    left: 0;
			}				   
    			</style>
    	<div id="gas-pump-div" style="background-image: url(/images/gas-pump.jpg); width: 360px; height: 475px; border-radius: 30px; position: absolute; top: 0px; right: -52px; z-index: 800; opacity: 0.5;">
    		<div id="new-console" style="position: relative; top: 52px; left: 52px; width: 249px; height: 368px; padding: 3px; z-index: 800;">
    			<UL class="new-console-list">
				<li class="new-console-list-item" style="width: 250px; height: 57.5px; background-image: url('/images/window1a.jpg'); background-size: cover;"><img src="/images/window-glass.jpg" id="out-window" style="position: absolute; top:5px; left: 40px; width: 180px;filter: brightness(75%) contrast(100%);">
				<div id="out-window-text" style="display: block; text-transform: uppercase; font-size: 21px; font-weight: bold; color: red; position: absolute; top: 11px; left: 55px; height: 30px; width: 245px; opacity: 80%;"></div></li>
				<li class="new-console-list-item" style="width: 250px; height: 57.5px; background-image: url('/images/window1a.jpg'); background-size: cover;"><img src="/images/window-glass.jpg" id="safe-window" style="position: absolute; top:5px; left: 40px; width: 180px;filter: brightness(75%) contrast(100%);">
				<div id="safe-window-text" style="display: block; text-transform: uppercase; font-size: 21px; font-weight: bold; color: green; position: absolute; top: 11px; left: 55px; height: 30px; width: 245px; text-shadow: 4px 4px 8px yellow;"></div></li>
				<li class="new-console-list-item" style="width: 250px; height: 57.5px; background-image: url('/images/window1a.jpg'); background-size: cover;"><img src="/images/window-glass.jpg" id="extra-window" style="position: absolute; top:5px; left: 40px; width: 180px;filter: brightness(75%) contrast(100%);">
				<div id="extra-window-text" style="display: block; text-transform: uppercase; font-size: 18px; font-weight: bold; color: black; position: absolute; top: 11px; left: 55px; height: 30px; width: 245px; "></div></li>
				
				<li class="new-console-list-item"><img src="/images/dice_roll1a.jpg" style="width: 250px;filter: brightness(100%) contrast(100%);">
				<div id="window-1" style="display: block; font-size: 16px; font-weight: bold; position: absolute; top: 4px; left: 7px; height: 30px; width: 245px; "></div>
				<div id="window-1-roll" style="display: block; letter-spacing: 11px; font-size: 26px; font-weight: bold; color: white; position: absolute; top: 28px; left: 92px; height: 30px; width: 245px; "></div>
				</li>
				<li class="new-console-list-item"><img src="/images/dice_roll1a.jpg" style="width: 250px;filter: brightness(100%) contrast(100%);">
				<div id="window-2" style="display: block; font-size: 16px; font-weight: bold; position: absolute; top: 4px; left: 7px; height: 30px; width: 245px; "></div>
				<div id="window-2-roll" style="display: block; letter-spacing: 11px; font-size: 26px; font-weight: bold; color: white; position: absolute; top: 28px; left: 92px; height: 30px; width: 245px; "></div></li>
				<li class="new-console-list-item"><img src="/images/dice_roll1a.jpg" style="width: 250px;filter: brightness(100%) contrast(100%);">
				<div id="window-3" style="display: block; font-size: 16px; font-weight: bold; position: absolute; top: 4px; left: 7px; height: 30px; width: 245px; "></div>
				<div id="window-3-roll" style="display: block; letter-spacing: 11px; font-size: 26px; font-weight: bold; color: white; position: absolute; top: 28px; left: 92px; height: 30px; width: 245px; "></div></li>
    			</UL>
    		</div>
    	  </div>
    		<div id="home-dugout" style="width: 300px; height: 200px; position: absolute; top: 475px; right: 15px;padding: 3px 7px 3px 7px;background-color: gray; z-index: 350;">
    			<span style="color: white;font-weight: bold;">HOME TEAM</span><br>
    			
    		</div>
    		
    		<div id="away-dugout" style="width: 300px; height: 200px; position: absolute; top: 475px; left: 15px;padding: 3px 7px 3px 7px;background-color: gray; z-index: 350;">
    			<span style="color: white;font-weight: bold;">AWAY TEAM</span><br>
    			
    		</div>
    	
    	<div id="on-deck">
    	
    	</div>
    	
    	<div id="homeplate-wrapper" style="width: 550px; height: 250px; position: absolute; bottom: 80px; left: 350px; z-index: 825; ">
    		<div id="homeplate-circle" style="background-color: tan;width: 200px;height: 200px;border: 5px solid white;border-radius: 50%;position: absolute; bottom: 0px; left: 150px; z-index: 825;">
    		   &nbsp;
    		   <div id="home-plate" style="width: 15px; height: 15px; background-color: white; position: absolute; bottom: 97px; right: 92px;">
    		   	&nbsp;
    		   </div>
    		   <div id="batters-box-left" style="width: 18px; height: 45px;  border: 2px solid white; position: absolute; bottom: 80px; left: 60px; z-index: 850">
    		   
    		   </div>
    		   <div id="batters-box-right" style="width: 18px; height: 45px; border: 2px solid white; position: absolute; bottom: 80px; right: 60px;">
    		   	&nbsp;
    		   </div>
    		</div>
    	</div>
		<div id="behind-homeplate" style="position: absolute;width: 1217px; height: 78px;z-index: -1;border: 1px solid darkgray;background-color: tan;bottom: 0px;left: 0px;z-index: 350;">
			<img id="logo-away" src="../images/team-logos/Red-Sox-Logo-1.jpg" style="max-width: 54px; max-height: 54px; position: absolute; top: 15px; left: 20px; z-index: 999;">
			<img id="team-card-away" class="team-card" src="/images/Baseball-Cards/SGC-309-web/1963_Topps_503_Milwaukee_Braves__SGC-Grade-6_Auth-3045059_Front.jpg" style="width: 85px; position: absolute; top: 10px; left: 120px; z-index: 999;">

			<img id="team-card-home" class="team-card" src="/images/Baseball-Cards/SGC-016-web/1977_Topps_309_Red_Sox__SGC-Grade-8_Auth-5745654_Front.jpg" style="width: 85px; position: absolute; top: 10px; right: 120px; z-index: 999;">
			<img id="logo-home" src="../images/team-logos/Red-Sox-Logo-1.jpg" style="max-width: 54px; max-height: 54px; position: absolute; top: 15px; right: 20px; z-index: 999;">
			
			<div id="play-buttons" style="position: absolute; left: 300px; bottom: 8px;">
				<button class="batter-up link-button disabled-link-button" style="border: 3px solid yellow;" >Batter Up</button> &nbsp; <button class="send-pitch link-button disabled-link-button" style="font-size: 1px; padding: 0px !important" disabled>Pitch</button> 
				
				&nbsp; <button class="relieve-pitcher link-button disabled-link-button" style="" disabled>Relieve Pitcher</button>
			</div>
			
			<div id="mini-score-display" style="border: 1px solid gray; font-weight: bold; font-size: 16px; background-color: #54796d; color: white;position: absolute; right: 230px; bottom: 8px;">
				<span id="mini-toporbottom">&#x25B2; &#x25BC;</span> <span id="mini-inning">1st</span> &nbsp; <span id="mini-outs">O: 0</span> &nbsp; <span id="mini-away-score">A:0</span> &nbsp; <span id="mini-home-score">H:0</span>
			</div>
		</div>
		<div id="show-pitch" style="position: absolute;width: 1217px; height: 380px; border: 0px solid darkgray; top: 0px; left: 0px; z-index: 850; padding: 0px; font-family: monospace; font-size: 16px; display: none;">
			<div style="width: 321px; background-color: gray; color: white; text-align: center;"><span style="font-weight: bold;">Matchup</span></div>
			<div id="pitch-cards" style="float: left; background-color: white; border: 1px solid darkgray;">
				<div id="pitch-pitcher" style="float: left; padding: 0px 15px 0px 0px; border: 1px solid darkgray;">
					<div id="pitch-pitcher-img" style=""></div>
					<div id="pitch-pitcher-name" style="font-weight: bold; text-align: center;"></div>
					<UL id="pitch-pitcher-data" style=""></UL>
				</div>
				<div id="pitch-batter" style="float: right; font-family: monospace; background-color: white; border: 1px solid darkgray;">
					<div id="pitch-batter-img" style=""></div>
					<div id="pitch-batter-name" style="font-weight: bold; text-align: center;"></div>
					<UL id="pitch-batter-data" style=""></UL>
				</div>
			</div>
			
			<div id="pitch-results" style="position: absolute; z-index: 500; top: 1500px; left: 0px; width: 140px; height: 150px; padding: 8px;border: 1px dashed gray; background-color: white; font-size: 5px;">
				Safe/Out factor: <br>
				Out Type: (SO, Ground Out, Fly Out)<br>
				Safe Type: (BB, 1st, 2nd, 3rd, HR)<br><br>
				
			</div>
			<div id="inning-announcement-wrapper" style="display: none;">
				<div style="background-color: white; opacity: 0.1; margin-left: 326px; margin-right: auto; text-align: center; height: 368px; width: 602px;">
					&nbsp;
				</div>
				<div style="position: relative; top: -280px; width: 602px; background: transparent; opacity: 1; margin-left: auto; margin-right: auto; text-align: center; ">
					<span id="inning-announcement" style="font-size: 60px; font-weight: bold; color: #54796d; text-shadow: 8px 8px 10px #FFF, -8px -8px 10px #FFF, -8px 8px 10px #FFF, 8px -8px 10px #FFF;">BOTTOM<br>OF THE<br>1ST</span>
				</div>
			</div>
			<div id="steal-base-wrapper" style="display: none; z-index: 850;">
				<div style="background-color: transparent; opacity: 0.7; margin-left: 326px; margin-right: auto; text-align: center; height: 345px; width: 602px;">
					&nbsp;
				</div>
				<div style="position: relative; top: -195px; width: 602px; background: transparent; opacity: 1; margin-left: auto; margin-right: auto; text-align: center; ">
					<span id="steal-base-question" style="font-size: 20px; font-weight: bold; color: blue; text-shadow: 8px 8px 10px #FFF, -8px -8px 10px #FFF, -8px 8px 10px #FFF, 8px -8px 10px #FFF;">Steal?</span>
				</div>
				<div style="position: relative; top: -30px; width: 602px; background: transparent; opacity: 1; margin-left: auto; margin-right: auto; text-align: center; z-index: 850;">
					<button class="link-button steal-yes" style="font-size: 22px; text-shadow: 0px 0px 0px #FFF">Steal</button> 
					&nbsp; <button class="link-button steal-no" style="font-size: 22px; text-shadow: 0px 0px 0px #FFF">No</button>
					
					<br>
					<img src="/images/dice_roll1a.jpg" style="width: 250px;filter: brightness(100%) contrast(100%);">
					<div id="steal-window-roll" style="display: block; letter-spacing: 11px; font-size: 30px; font-weight: bold; color: white; position: absolute; top: 58px; left: 34px; height: 30px; width: 545px; ">
						<span id="steal-segment" style="font-size: 26px;">000</span>
					</div>
				</div>
			</div>
		</div>
    	</div>
	<div id="preload-card-backs" style="display: none;">
		
	</div>
    </div>
    
 
 
    <div id="status-msg" style="display: none; position: absolute; top: 550px; left: 22px; background-color:rgba(12, 35, 64, 0.7); z-index: 800; text-align: center; outline-offset: -10px; outline: 2px solid #FFF;">
    	<div class="game-title">
    		<span style="display: inline; color: #FFF; text-shadow: #0C2340 1px 3px 3px; font-size: 65px; font-weight: 300; margin: 20px 0px 0px 0px; font-family: Eurostile_MN_Extended_Bold">Cardboard Baseball</span>
    	</div>
    	<div id="status-msg-text" style="background-color: transparent;  color: white; font-size: 55px; font-weight: bold; width: 825px; height: 450px; text-align: left; z-index: 800; margin: 15px 0px 0px 390px; padding-top: 15px;">
    		FINAL		
    		<button id="status-button" class="link-button" style="font-size: 18px; position: absolute; bottom: 10px; left: 50px; width: 250px; font-family:sans-serif; z-index: 800;">START A NEW GAME</button>  
    	</div>
    </div>
   
    <div id="new-game-msg" style="display: block; position: absolute; top: 370px; left: 415px; border: 12px solid #FFF; background-image: url(/images/Fenway-Park-1.jpg);background-color: rgba(84, 121, 109, 0.99); background-blend-mode: luminosity; background-repeat: repeat; background-position: -140px 0px; height: 420px; width: 420px; z-index: 800;">
    	
    	<div id="logos-left-side" style="width: 80px; float: left; margin: 0px 0px 0px -100px; text-align: center;">
    		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/St.-Louis-Cardinals-logo-1998.png">
    		<img style="filter: grayscale(.8) brightness(100%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/Cincinnati-Reds-logo.png">
    		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/Houston-Astros-logo.png">
    		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/Milwaukee-Braves-Logo-1956-1965.png">
    		<img style="filter: grayscale(.8) brightness(100%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/SF-Giants.jpg">
    	</div>
    	<div id="logos-right-side" style="width: 80px; float: right; margin: 0px -100px 0px 0px; text-align: center;">
		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/oakland-athletics-elephant-logo.png">
		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/Pirates.png">
		<img style="filter: grayscale(.8) brightness(100%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/New-York-Yankees-Logo-2.png">
		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/trolley_dodgers.png">
		<img style="filter: grayscale(.8) brightness(140%); border: 0px solid black; max-width: 75px; max-height: 75px; margin: 10px 0px 0px 0px;" src="../images/team-logos/Red-Sox-Logo-2.png">
    	</div>
    	
    	<div id="team_select-wrapper" style="background-color: transparent; color: green;font-size: 22px;font-weight: bold;width: 420px;height: 320px;text-align: center; z-index: 800;">
  
		<div id="loading-logo" style="display: block; position: absolute; top: 0px; left: 0px;background-color: transparent; height: 500px; z-index: 800;">		
			<div class="circle-container" style="display: none;" >
				<div class="circle" style="background-image: url(/images/Shad-Island-Logo-6-BW.png); background-repeat: no-repeat; background-size: 400px 400px;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave-below _100" style="opacity: 90%; clip-path: polygon(0% 110%, 0% 125px, 110% 125px, 110% 110%);"></div>
				<div class="desc _100">
				</div>
			</div>
   	    	
			<div id="team-select" class="fadeIn-animation" style="z-index: 1100; background: transparent; position: relative; top: 20px; left: 0px; width: 420px; ">
			<form id="team-select-form">	
   	    	<?php
   	    		if( !isset($_GET['game_season_id']) ) {
   	    	?>
   	    				<img src="/images/lightning-bolt2.png" style="transform: scaleX(-1); width: 35px; filter: hue-rotate(180deg) brightness(0.4);"><label for="season-select" style="margin: 0px 0px 15px 0px; font-size: 30px; color: #0C2340; text-shadow: white 1px 3px 3px;">PICK A SEASON</label><img src="/images/lightning-bolt2.png" style="width: 35px; filter: hue-rotate(180deg) brightness(0.4);">
   	    				<br><br>
   	    				<select id="season-select" name="game_season_id" class="link-button" style="background-color: #54796d; font-size: 18px; border-radius: 50px 20px; padding: 10px 35px 10px 20px; border: 3px solid #0C2340; background: url(/images/select-arrow.jpg) no-repeat right #54796d; -webkit-appearance: none; background-position-x: 93%;" required>
					     	<?php foreach ($seasons as $season): ?>
						 	<option value="<?php echo htmlspecialchars($season['game_season_id']); ?>">
								<?php echo htmlspecialchars($season['season_name']); ?> Season
						 	</option>
						<?php endforeach; ?>
					 </select>
					 <button id="season-select-button"  type="button" style="border-radius: 50px 20px; font-size: 18px; padding: 10px 15px;" class="link-button">Select</button>
					 
					 <div class="game-title" style="margin: 235px 0px 0px 0px;">
					 	<span style="display: inline; color: #FFF; text-shadow: #0C2340 1px 3px 3px; font-size: 28px; font-weight: 300;  font-family: Eurostile_MN_Extended_Bold">Cardboard Baseball</span>
					 </div>
		<?php
			} else {
		?>	
					<div class="game-title" style="top: 335px ; left: 0px; position: absolute;">
						<span style=" display: inline; color: #FFF; text-shadow: #0C2340 1px 3px 3px; font-size: 28px; font-weight: 300;  font-family: Eurostile_MN_Extended_Bold;">Cardboard Baseball</span>
					</div>
					
					<span id="selected-season" style="font-size: 38px; color: #245ca2; text-shadow: 6px 6px 8px #FFF, -6px -6px 8px #FFF, -6px 6px 8px #FFF, 6px -6px 8px #FFF;">
					
					</span>
					 
					<div style="margin-top: 15px;">
						&nbsp;
					</div>	
			 				
					 <label for="home-team-select" style="font-size: 30px; color: #0C2340; text-shadow: white 1px 3px 3px">HOME TEAM</label><br/>
					 <select id="home-team-select" name="home_team_id" class="link-button" style="border: 3px solid #0C2340; background-color: #54796d; font-size: 20px; border-radius: 50px 20px; padding: 10px 35px 10px 20px; background: url(/images/select-arrow.jpg) no-repeat right #54796d; -webkit-appearance: none; background-position-x: 93%;" required>
					     <?php foreach ($game_teams as $team): ?>
						 <option value="<?php echo htmlspecialchars($team['game_team_id']); ?>">
							<?php echo htmlspecialchars($team['team_name']); ?>
						 </option>
					     <?php endforeach; ?>
					 </select>

					 <span id="selected-home-team" style="font-size: 38px; color: #245ca2; text-shadow: 6px 6px 8px #FFF, -6px -6px 8px #FFF, -6px 6px 8px #FFF, 6px -6px 8px #FFF;"></span>


					<button id="home-team-select-button"  type="button" style="border-radius: 50px 20px; font-size: 20px; padding: 10px 15px;" class="link-button">Select</button>
					
					<div style="margin-top: 10px;">
						&nbsp;
					</div>

		<?php
			}
		?>
					 
					<div id="away-team-select-wrapper" style="display: none;">
						<label for="away-team-select" style="font-size: 30px; color: #54796d; text-shadow: 1px 3px 3px #FFF, -1px -3px 3px #FFF;">AWAY TEAM</label>
						<br/>
						<select id="away-team-select" name="away_team_id"  class="link-button" style="border: 3px solid #0C2340; background-color: #54796d; font-size: 20px; margin-top: 10px; border-radius: 50px 20px; padding: 10px 35px 10px 20px; background: url(/images/select-arrow.jpg) no-repeat right #54796d; -webkit-appearance: none; background-position-x: 93%;" required>
					     	<?php foreach ($game_teams as $team2): ?>
						 	<option value="<?php echo htmlspecialchars($team2['game_team_id']); ?>">
								<?php echo htmlspecialchars($team2['team_name']); ?>
						 	</option>
					     	<?php endforeach; ?>
					 	</select>
					 	
					 	<input id="team-select-submit"  style="display: inline; font-size: 20px; border-radius: 50px 20px; padding: 10px 15px;" class="link-button" type="submit" value="Select">  
					</div>
					 <?php 
					 $gameTeams = [];
					 foreach ($game_teams as $team): 
					 	//Why was this here? commented it out.
					 	//$gameTeams[] = $team['team_name'];
					 	?>
						<input type="hidden" id="game-team-<?php echo htmlspecialchars($team['game_team_id']); ?>"
						 data-team-logo-url="<?php echo htmlspecialchars($team['logo_url']); ?>" data-team-card-url="<?php echo htmlspecialchars($team['img_url']); ?>" data-home-team-card-id="<?php echo htmlspecialchars($team['game_team_card_id']); ?>" data-pitcher-id="<?php echo htmlspecialchars($team['pitcher_card_id']); ?>"  data-reliever-id="<?php echo htmlspecialchars($team['reliever_card_id']); ?>" data-catcher-id="<?php echo htmlspecialchars($team['catcher_card_id']); ?>" data-first-base-id="<?php echo htmlspecialchars($team['first_base_card_id']); ?>" data-second-base-id="<?php echo htmlspecialchars($team['second_base_card_id']); ?>" data-short-stop-id="<?php echo htmlspecialchars($team['short_stop_card_id']); ?>" data-third-base-id="<?php echo htmlspecialchars($team['third_base_card_id']); ?>" data-left-field-id="<?php echo htmlspecialchars($team['left_field_card_id']); ?>" data-center-field-id="<?php echo htmlspecialchars($team['center_field_card_id']); ?>" data-right-field-id="<?php echo htmlspecialchars($team['right_field_card_id']); ?>"   >
					 <?php endforeach; ?>
					 																	
			</form>	
			</div>
    

    			<div style="display: none; position: absolute; bottom: 10px; left: 160px;">
    				<button id="status-button" class="link-button" style="font-size: 14px; background-color: lightgray; z-index: 800;">Load A Previous Game</button>
    			</div>
    	</div>
    </div>
</div>    
    <input type="hidden" id="home-team-id" value="0" >
    <input type="hidden" id="away-team-id" value="0" >
    <input type="hidden" id="home-team-name-holder" value="x" >
    <input type="hidden" id="away-team-name-holder" value="x" >
    
    <?php
    
//Batting Leaders Query - Batting AVG
            $league_batting_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    $league_batting_leaders = [];
    	    	
			if( isset($_GET['game_season_id']) ) {
				$stmt = $conn->prepare(" 
					SELECT c.player_name, c.card_id, gps.at_bats, gps.hits, gps.home_runs, gps.strikeouts, gps.walks, gt.team_name, gps.hits/(gps.at_bats) as batting_avg 
						FROM game_player_stats gps, card c, game_team gt 
						WHERE gps.card_id = c.card_id 
						AND gps.hits > 0 
						AND gps.game_team_id = gt.game_team_id
						AND gt.game_season_id = ?
					GROUP BY c.player_name, c.card_id, gps.at_bats, gps.hits, gps.home_runs, gps.strikeouts, gps.walks, gt.team_name
					ORDER BY 9 DESC, 3 DESC
					LIMIT 10 OFFSET 0
				");

				$stmt->execute([$game_season_id]);
				$league_batting_leaders = $stmt->fetchAll();
			}
                } catch (\PDOException $e) {
    	        echo "Connection failedbb: " . $e->getMessage();
    	    }
    	    $conn = null;

//Batting Leaders Query - Home Runs    	    
            $league_home_run_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
    	    	
        		$stmt3 = $conn->prepare(" 
				SELECT c.player_name, gps.card_id, gps.home_runs, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name, gps.hits/(gps.at_bats) as batting_avg 
					FROM game_player_stats gps, card c, game_team gt 
					WHERE gps.card_id = c.card_id 
					AND gps.home_runs > 0 
					AND gps.game_team_id = gt.game_team_id
					AND gt.game_season_id = ?
				GROUP BY c.player_name, gps.card_id, gps.home_runs, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name
				ORDER BY home_runs DESC, at_bats ASC, 9 DESC
				LIMIT 10 OFFSET 0
			");

        		$stmt3->execute([$game_season_id]);
        		$league_home_run_leaders = $stmt3->fetchAll();
    		
                } catch (\PDOException $e) {
    	        echo "Connection failedcc: " . $e->getMessage();
    	    }
    	    $conn = null;


//Batting Leaders Query - Stolen Bases    	    
            $league_stolen_base_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
    	    	
        		$stmt3 = $conn->prepare(" 
				SELECT c.player_name, gps.card_id, gps.home_runs, gps.stolen_bases, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name, gps.hits/(gps.at_bats) as batting_avg 
					FROM game_player_stats gps, card c, game_team gt 
					WHERE gps.card_id = c.card_id 
					AND gps.stolen_bases > 0 
					AND gps.game_team_id = gt.game_team_id
					AND gt.game_season_id = ?
				GROUP BY c.player_name, gps.card_id, gps.home_runs, gps.stolen_bases, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name
				ORDER BY stolen_bases DESC, at_bats ASC, 9 DESC
				LIMIT 10 OFFSET 0
			");

        		$stmt3->execute([$game_season_id]);
        		$league_stolen_base_leaders = $stmt3->fetchAll();
    		
                } catch (\PDOException $e) {
    	        echo "Connection failedcc: " . $e->getMessage();
    	    }
    	    $conn = null;


//Pitching Leaders Query - ERA
            $league_pitching_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
    	    
    	    		//ERA is: 9 x earned runs / innings pitched
        		$stmt = $conn->prepare(" 								
				SELECT c.player_name, c.card_id, gps.innings_pitched, gps.wins, gps.losses, gps.strikeouts_against, gps.walks_against, gt.team_name, (gps.runs_against * 9) / gps.innings_pitched as era
					FROM game_player_stats gps, card c, game_team gt 
					WHERE gps.card_id = c.card_id 
					AND gps.pitcher_or_batter = 'pitcher'
					AND gps.game_team_id = gt.game_team_id
                   			AND gps.innings_pitched > 0
					AND gt.game_season_id = ?
					GROUP BY c.player_name, c.card_id, gps.innings_pitched, gps.wins, gps.losses, gps.strikeouts_against, gps.walks_against, gt.team_name
					ORDER BY 9 ASC, 3 DESC, 4 DESC
				LIMIT 10 OFFSET 0
			");
        		$stmt->execute([$game_season_id]);
        		$league_pitching_leaders = $stmt->fetchAll();
    		
                } catch (\PDOException $e) {
    	        echo "Connection faileddd: " . $e->getMessage();
    	    }
    	    $conn = null;
          
//Pitching Leaders Query - Strikeouts
            $league_strikeout_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
        		$stmt4 = $conn->prepare(" 
				SELECT c.player_name, gps.*, gt.team_name, (gps.runs_against * 9) / gps.innings_pitched as era, ((gps.strikeouts_against/gps.innings_pitched) ) -  ((gps.walks_against/gps.innings_pitched) ) as kbb
				FROM game_player_stats gps, card c, game_team gt 
				WHERE gps.card_id = c.card_id 
				AND gps.strikeouts_against > 0 
				AND gps.innings_pitched > 0
				AND gps.game_team_id = gt.game_team_id
				AND gt.game_season_id = ?
				ORDER BY ((gps.strikeouts_against/gps.innings_pitched) * 9) - ((gps.walks_against/gps.innings_pitched) * 9) DESC, gps.strikeouts_against DESC, gps.innings_pitched DESC
				LIMIT 10 OFFSET 0
			");
			
        		$stmt4->execute([$game_season_id]);
        		$league_strikeout_leaders = $stmt4->fetchAll();
        		
                } catch (\PDOException $e) {
    	        echo "Connection failedee: " . $e->getMessage();
    	    }
    	    $conn = null;  
    	    
    	
//Season Games Played - Game matchup table Query
            $game_team_matchups = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
        		$stmt5 = $conn->prepare(" 
        			SELECT g.home_team_id, gt.team_name as homeTeamName, gt2.team_name as awayTeamName, g.away_team_id, count(g.away_team_id) as theCount
				FROM game g, game_team gt, game_team gt2
				WHERE gt.game_team_id = g.home_team_id   
				AND gt2.game_team_id = g.away_team_id   
				AND g.game_season_id = ?
				GROUP BY g.home_team_id, homeTeamName, awayTeamName
				ORDER BY 1, 3 ASC
			");
			
        		$stmt5->execute([$game_season_id]);
        		$game_team_matchups = $stmt5->fetchAll();
    		
                } catch (\PDOException $e) {
    	        echo "Connection failedff: " . $e->getMessage();
    	    }
    	    $conn = null;              
    ?>

<!--SEASON STANDINGS-->   
<div id="box-stats" style="width: 560px; position: absolute; top:130px; left: 1300px; text-align: center;" >
	<div class="game-title" style="width: 700px; color: #0C2340; text-shadow: #FFF 1px 3px 3px; font-size: 50px; font-weight: 300; margin-right: 15px; font-family: Eurostile_MN_Extended_Bold">
		Cardboard Baseball
	</div>
<?php
 if( isset($_GET['game_season_id']) ) {
?>
   	<span style=" font-weight: bold; font-size: 22px;"><?php echo $game_season_name; ?> SEASON STANDINGS</span>
    	<table class="season-standings-table" style="margin-left: auto; margin-right: auto;">
    		<tr>
    			<th></th><th>Wins</th><th>Losses</th><th>Runs For</th><th>Runs Against</th><th>Pct.</th>
    		</tr>
    	<?php 
    		$seasonTeams = [];
    		foreach ($season_standings as $season_team): 
    			$seasonTeams[] = $season_team['team_name'];
    	?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($season_team['team_name']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($season_team['total_wins']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($season_team['total_losses']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($season_team['total_runs_for']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($season_team['total_runs_against']); ?>
    			</td>
    			<td><?php     			
    				//echo "[" . $season_team['total_wins']+$season_team['total_losses'] ."]";
    				if( $season_team['total_wins']+$season_team['total_losses'] == 0 ) {  				
    					echo ".000";
    				} else {
    					echo htmlspecialchars( ltrim(number_format($season_team['total_wins'] / ($season_team['total_wins']+$season_team['total_losses']), 3), '0') );
    				}
    				?>
    			</td>
    		</tr>
    		
    		
    	<?php 
    		endforeach; 
    		//Add any teams from $game_teams that have played zero games  	
		//loop through the teams and check
    		foreach ($gameTeams as $gameTeam ):   			
			if ( !in_array($gameTeam, $seasonTeams) ) {
				echo '
					<tr>
						<td>' . htmlspecialchars($gameTeam) . '
						</td>
						<td>0
						</td>
						<td>0
						</td>
						<td>0
						</td>
						<td>0
						</td>
						<td>0
						</td>
					</tr>';
		
			}
    		
    		endforeach;
    		?>
    	</table>
    	<br>
<!--LEAGUE LEADERS -->
    <span style="font-weight: bold; font-size:22px;">LEAGUE LEADERS</span>
    	<table id="box-stats-table" >

<!--ERA LEADERS-->
    		<tr>
    			<th colspan="8">&nbsp;ERA Leaders&nbsp;</th>
    		</tr>
    		<tr>
    			<th>&nbsp;Pitcher&nbsp;Name&nbsp;</th><th>ERA</th><th>IP</th><th>W</th><th>L</th><th>K</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_pitching_leaders as $pitching_leader): ?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($pitching_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars(number_format($pitching_leader['era'], 2)); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['innings_pitched']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['wins']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['losses']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['strikeouts_against']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['walks_against']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($pitching_leader['team_name']); ?>
    			</td>
    		</tr>
    		
    		<?php endforeach; ?>
    		
<!--STRIKEOUT LEADERS-->
    		<tr>
    			<td colspan="8">&nbsp;</td>
    		</tr>
    		<tr>
    			<th colspan="8">&nbsp;Strikeout Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Pitcher&nbsp;Name&nbsp;</th><th>K-BB%</th><th>K</th><th>BB</th><th>IP</th><th>ERA</th><th>W-L</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_strikeout_leaders as $strikeout_leader): ?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($strikeout_leader['player_name']); ?>
    			</td>
    			<td><?php echo htmlspecialchars(ltrim(number_format($strikeout_leader['kbb'], 3), '0') ); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($strikeout_leader['strikeouts_against'] ); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars( $strikeout_leader['walks_against'] ); ?>
    			</td>
    			<td><?php echo htmlspecialchars( $strikeout_leader['innings_pitched'] ); ?>
    			</td>
    			<td><?php echo htmlspecialchars(number_format($strikeout_leader['era'], 2) ); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['wins'] . '-' . $strikeout_leader['losses'] ); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['team_name']); ?>
    			</td>
    		</tr>
    		
    		<?php endforeach; ?>

<!--BATTING LEADERS-->   		
    		<tr>
    			<td colspan="8">&nbsp;</td>
    		</tr>  			
    		
    		<tr>
    			<td colspan="8" style="background-color: gray;">&nbsp;</td>
    		</tr>
    		<tr>
    			<th colspan="8">&nbsp;Batting Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Batter&nbsp;Name&nbsp;</th><th>AVG</th><th>At Bats</th><th>Hits</th><th>HR</th><th>K</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_batting_leaders as $batting_leader): ?>
    		<tr>
    			<td ><?php echo htmlspecialchars($batting_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars(ltrim(number_format($batting_leader['batting_avg'], 3), '0') ); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['at_bats']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['hits']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['home_runs']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['strikeouts']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['walks']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($batting_leader['team_name']); ?>
    			</td>
    		</tr>
    		<?php endforeach; ?>
 
 <!--HOME RUN LEADERS-->
 <tr>
    			<td colspan="8">&nbsp;
    			</td>
    		</tr>
    		<tr>
    			<th colspan="8">&nbsp;Home Run Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Batter&nbsp;Name&nbsp;</th><th>HR</th><th>At Bats</th><th>AVG</th><th>Hits</th><th>K</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_home_run_leaders as $home_run_leader): ?>
    		<tr>
    			<td ><?php echo htmlspecialchars($home_run_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($home_run_leader['home_runs']); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['at_bats']); ?>
    			</td>
    			<td><?php echo htmlspecialchars(ltrim(number_format($home_run_leader['batting_avg'], 3), '0') ); ?>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['hits']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['strikeouts']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['walks']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['team_name']); ?>
    			</td>
    		</tr>
    		<?php endforeach; ?>
 
  <!--STOLEN BASE LEADERS-->
  <tr>
     			<td colspan="8">&nbsp;
     			</td>
     		</tr>
     		<tr>
     			<th colspan="8">&nbsp;Stolen Base Leaders&nbsp;</th>
     		</tr>
     		<tr >
     			<th>&nbsp;Batter&nbsp;Name&nbsp;</th><th>SB</th><th>At Bats</th><th>AVG</th><th>Hits</th><th>K</th><th>BB</th><th>Team</th>
     		</tr>
     	
     		<?php foreach ($league_stolen_base_leaders as $stolen_base_leader): ?>
     		<tr>
     			<td ><?php echo htmlspecialchars($stolen_base_leader['player_name']); ?>
     			</td>
     			<td><span style="font-style: italic;"><?php echo htmlspecialchars($stolen_base_leader['stolen_bases']); ?></span>
     			</td>
     			<td><?php echo htmlspecialchars($stolen_base_leader['at_bats']); ?>
     			</td>
     			<td><?php echo htmlspecialchars(ltrim(number_format($stolen_base_leader['batting_avg'], 3), '0') ); ?>
     			</td>
     			<td><?php echo htmlspecialchars($stolen_base_leader['hits']); ?>
     			</td>
     			<td><?php echo htmlspecialchars($stolen_base_leader['strikeouts']); ?>
     			</td>
     			<td><?php echo htmlspecialchars($stolen_base_leader['walks']); ?>
     			</td>
     			<td><?php echo htmlspecialchars($stolen_base_leader['team_name']); ?>
     			</td>
     		</tr>
    		<?php endforeach; ?>
 
 
    	</table>
<!--SEASON GAMES PLAYED -->    	
	<br>
	<span style="font-weight: bold;"><?php echo $game_season_name; ?> SEASON GAMES PLAYED</span>
	<table class="season-standings-table" >
		<tr>
			<th colspan="7" style="text-align: center;">Away</th>
		</td>
		<tr>
			<th></th>		
	<?php 

		$homeTeams = $game_teams;
		$awayTeams = $game_teams;
		//$all_possible_matchups = [];
		foreach ($awayTeams as $awayTeam): 
			echo '<th>' . $awayTeam['team_name']. '</th>';
		endforeach;
		$currentHomeTeamID = 0;
		foreach ($homeTeams as $homeTeam): 
			if( $currentHomeTeamID <> $homeTeam['game_team_id'] ) {
				if( $currentHomeTeamID > 0 ) {
					echo '</tr>';
				}
				//New row
				echo '<tr><td>' . $homeTeam['team_name'] . '</td>';	
			} 		
			foreach ($awayTeams as $awayTeam): 
				//$all_possible_matchups[] = array("home_team_id"=> $homeTeam['game_team_id'], "away_team_id"=>$awayTeam['game_team_id'], "theCount"=>0);
				//Look for a game record with this home team and this away team
				$foundMatch = false;
				if( $homeTeam['game_team_id'] == $awayTeam['game_team_id'] ) {
						echo '<td> X </td>';
				} else {
					foreach ($game_team_matchups as $matchup): 

						if( $matchup['home_team_id'] == $homeTeam['game_team_id'] && $matchup['away_team_id'] == $awayTeam['game_team_id'] ) {						
							echo '<td>' . $matchup['theCount'] . '</td>';
							$foundMatch = true;
						}

					endforeach;

					if( !$foundMatch ) {
						echo '<td style="color: green;"> 0 </td>';
					}
				}
			endforeach;

			$currentHomeTeamID = $homeTeam['game_team_id'];
		endforeach;

		?>
		</tr>
	  </table>

	  <br>
 <?php
 	} else {
?>
<img id="card-img-378" src="/images/Baseball-Cards/raw-web/1954_Topps_10_Jackie Robinson__raw-Grade-TBD_Auth-11_Front.jpg" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.865, 0.865); height: 400px;">
<img id="card-img-107" src="/images/Baseball-Cards/PSA-Scans-web/1958_Topps_485_Ted Williams__PSA-Grade-5_Auth-26735764_Front.jpg" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.91, 0.91); height: 530px; clip-path: inset(24% -70% 5% 7%); margin-top: -100px;">
<img id="card-img-379" src="/images/Baseball-Cards/raw-web/1954_Topps_50_Yogi Berra__raw-Grade-TBD_Auth-11_Front.jpg" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.865, 0.865); height: 400px;">
<?php
 	}
 ?>
	</div>
    	<div style="width: 1230px; text-align: center;">
		<div class="game-title" style="display: inline; color: #0C2340; text-shadow: #FFF 1px 3px 3px; font-size: 50px; font-weight: 300; font-family: Eurostile_MN_Extended_Bold">
    		Cardboard&nbsp;Baseball
		</div>
	</div>
      <?php require 'footer.php';?>
      
</body>
</html>
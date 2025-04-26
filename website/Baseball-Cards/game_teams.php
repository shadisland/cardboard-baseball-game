<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

// Fetch all game teams
if( $_SESSION['user_role'] == 'admin' ){
	$stmt = $pdo->query('SELECT * 
				FROM game_team gt, game_season gs, game_club gc
				WHERE gt.game_season_id = gs.game_season_id 
				AND gt.game_club_id = gc.game_club_id 
				ORDER BY gs.season_name ASC, gt.team_name ASC');
} else if( $_SESSION['user_role'] == 'user' ){
	$stmt = $pdo->query('SELECT * 
				FROM game_team gt, game_season gs, game_club gc
				WHERE gt.game_season_id = gs.game_season_id 
				AND gt.game_club_id = gc.game_club_id 
				AND gt.game_season_id = 1
				ORDER BY gs.season_name ASC, gt.team_name ASC');

}
$teams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cardboard Baseball Teams</title>
     <?php require 'head_scripts_include.php';?>

     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/game_styles.css">
     <style>
     </style>
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
     
    <div id="teams-table-instructions" style="margin-top: 20px;">
    	Click a Season, then Click a Team Name to View/Edit  
    </div>

    <table id="teams-table" style="width: 525px; border-collapse: collapse; border: 0px solid gray;">
        <tr>
            <th>Season</th>
            <th>Team Name</th>
            <th>Club</th>
            <th>Logo</th>
            <th>ID</th>
        </tr>
    <?php 
    	$season_name = '';
	foreach ($teams as $team): 
		if( $team['season_name'] != $season_name ) {
			echo '<tr class="season" style="border: 1px solid gray;" value="' . $team['season_name'] . '">';
			echo '<td colspan="5" style="padding: 0px 0px 0px 15px; color: #0C2340; font-weight: bold; font-size: 20px; background-color: white; height: 55px;">';
			echo ' <span id="' . $team['season_name'] . '-symbol" style="font-size: 25px;">+</span> &nbsp;&nbsp; ' . $team['season_name'] . ' Season';
			echo '</td>';
			echo '</tr>';
		}
		echo '<tr class="' . $team['season_name'] . '" style="display: none;border-collapse: collapse; border: 1px solid gray;">';
		echo 	'<td style="">' . $team['season_name'] . ' Season</td>';
		echo 	'<td><a class="underlined-link" href="game_team_edit.php?id=' . $team['game_team_id'] . '">' . htmlspecialchars($team['team_name']) . '</a></td>';
		echo 	'<td>' . $team['club_name'] . ' Club</td>';
		echo 	'<td><a class="underlined-link" href="game_team_edit.php?id=' . $team['game_team_id'] . '">' . '<img src ="' . $team['logo_url'] . '" class="team-logo" style="max-height:55px;"></a></td>';
		echo 	'<td>' . $team['game_team_id'] . '</td>';
		echo '</tr>';
		$season_name = $team['season_name'];
	endforeach; ?>
    </table>
 </div> 
 
<style>
.copyright {
	margin-top: 620px !important;
}
</style>
 <?php require 'footer.php';?> 
 <script>
 $(".season").click(function(e) {
 	var $seasonName = $(this).attr("value");
 	if( $("#" + $seasonName + "-symbol").text() == "+" ) {
 		//close any seasons that are open
 		//loop through the seasons and close all of them 
 		//before opening this one
 <?php
		foreach ($teams as $team): 
?>
			$("#<?= $team['season_name']; ?>" + "-symbol").text("+");
			$(".<?= $team['season_name']; ?>").css("display", "none");
<?php			 
		endforeach;
 ?>
 		$("#" + $seasonName + "-symbol").text("-");
 		$("." + $seasonName).css("display", "table-row");
 	} else {
 		$("." + $seasonName).css("display", "none");
 		$("#" + $seasonName + "-symbol").text("+");
 	}
 });
 
 $(".season").first().click();
 </script>
</body>
</html>

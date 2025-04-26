<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}

require 'db.php';

// Fetch all game teams
$stmt = $pdo->query('SELECT * 
			FROM game_team gt, game_season gs, game_club gc
			WHERE gt.game_season_id = gs.game_season_id 
			AND gt.game_club_id = gc.game_club_id ');
$teams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game Teams</title>
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
    <h1>Game Teams</h1>
    <a href="game_team_create.php">ADD A NEW TEAM</a> &nbsp; | &nbsp; <a href="game_seasons.php">ALL GAME SEASONS</a> &nbsp; | &nbsp; <a href="card_game.php">START A NEW GAME</a>
    <br><br>
    <div id="teams-table-instructions">
    	Click a Team Name to View/Edit &nbsp; (All player ID's refer to card_id)
    </div>
    <table id="teams-table" >
        <tr>
            <th>ID</th>
            <th>Team Name</th>
            <th>Club</th>
            <th>Season</th>
            <th>Pitcher</th>
            <th>Catcher</th>
            <th>First</th>
            <th>Second</th>
            <th>Third</th>
            <th>Short</th>
            <th>Left</th>
            <th>Center</th>
            <th>Right</th>
            <th>DH</th>
        </tr>
        <?php foreach ($teams as $team): ?>
        <tr>
            <td><?= $team['game_team_id']; ?></td>
            <td><a class="underlined-link" href="game_team_edit.php?id=<?= $team['game_team_id']; ?>"><?= htmlspecialchars($team['team_name']); ?></a></td>
            <td><?= $team['club_name']; ?></td>
            <td><?= $team['season_name']; ?></td>
            <td><?= $team['pitcher_card_id']; ?></td>
            <td><?= $team['catcher_card_id']; ?></td>
            <td><?= $team['first_base_card_id']; ?></td>
            <td><?= $team['second_base_card_id']; ?></td>
            <td><?= $team['third_base_card_id']; ?></td>
            <td><?= $team['short_stop_card_id']; ?></td>
            <td><?= $team['left_field_card_id']; ?></td>
            <td><?= $team['center_field_card_id']; ?></td>
            <td><?= $team['right_field_card_id']; ?></td>
            <td><?= $team['dh_card_id']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
 </div>    
</body>
</html>

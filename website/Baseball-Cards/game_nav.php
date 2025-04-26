
<!-- GAME NAV -->
<style>
#game-nav a{
	color: #204a6d;
}
</style>
	<div id="game-nav" style="width: 700px; margin: 0px 0px 0px 10px; border: 0px solid #0C2340;">
		<fieldset style="border: 1px #0C2340 solid;background-color: rgba(1, 50, 32, 0.1);">   
			<legend style="font-size: 20px; font-weight: bold; font-family: Georgia; border: 1px solid #0C2340; margin-left: 1em; padding: 0.2em 0.8em ">			
			<div style="text-align: center;"><img src="/images/lightning-bolt2.png" style="width: 35px; filter: hue-rotate(180deg) brightness(0.7); transform: scaleX(-1);"> <span style="display: inline; font-size: 22px; font-weight: 300; margin-bottom: 0px; font-family: Eurostile_MN_Extended_Bold;">Cardboard Baseball</span> <img src="/images/lightning-bolt2.png" style="width: 35px; filter: hue-rotate(180deg) brightness(0.7);">
			<br>Game Management
			</div>
			</legend>
			<a href="game_clubs.php">EDIT CLUBS</a> &nbsp; | &nbsp; <a href="game_seasons.php">EDIT SEASONS</a> &nbsp; | &nbsp; <a href="game_teams.php">EDIT TEAMS</a> &nbsp; | &nbsp; 
<?php
	if( $_SESSION['user_role'] == 'admin' && strtolower($_SERVER['SCRIPT_NAME']) != strtolower('/Baseball-Cards/game_team_create.php') ){
?>          	
			<a href="game_team_create.php">ADD A TEAM</a> &nbsp; | &nbsp; 
<?php
	}
?>          	
			<a href="card_game.php">START A NEW GAME</a>
		</fieldset>     
	</div>
<!-- END GAME NAV -->
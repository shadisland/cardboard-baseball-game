<!-- Begin Header -->
<script>
<?php
	//echo 'SN: ' . $_SERVER['SCRIPT_NAME'];
	$this_page = strtolower($_SERVER['SCRIPT_NAME']);
	
	if( $this_page == strtolower('/Baseball-Cards/card_game.php') ){
	 	echo "$(document).ready(function () {";
		echo "$('.confirmation').on('click', function () { return confirm('Have you saved your game? If you click OK, your game will be lost unless you saved it.'); });";
		echo "});";
	}
?>
</script>
<div class="nav" style="border-bottom: 2px solid lightgray;">
	<div id="logoDiv" style="display: inline-block; width: 119px; height: 119px; position: relative; float: left; cursor: pointer;">&nbsp;</div>
	<div id="navbarDiv" style="display: block; width: auto; height: 119px; position: relative;  padding: 10px 0px 0px 40px;"> 

<?php
	
	
	echo '<div style="display: block; margin: 5px 0px 10px 0px;"><img src="/images/lightning-bolt2.png" style="width: 35px; filter: hue-rotate(180deg) brightness(0.7); transform: scaleX(-1);"> <span style="display: inline; font-size: 32px; font-weight: 300; margin-bottom: 0px; font-family: Eurostile_MN_Extended_Bold;">Cardboard Baseball</span> <img src="/images/lightning-bolt2.png" style="width: 35px; filter: hue-rotate(180deg) brightness(0.7);"> <span style="font-family: Georgia; font-size: 32px; font-weight: 400;">Game & Virtual Displays</span></div>';

	$myCtr = 0;
	if( isset($_SESSION['user_role']) ) { 
		//User is logged in
		$user_role = $_SESSION['user_role'];
		
		if( $user_role == 'admin' ){
			//Display the links which are appropriate for the admin user
			if( $this_page != strtolower('/Baseball-Cards/new_display_case.php')){
				echo '<a class="confirmation" href="new_display_case.php">+ ADD NEW VIRTUAL DISPLAY</a>';
				$myCtr +=1;
			}
			if ($myCtr > 0) {
				echo ' &nbsp; | &nbsp; ';
				$myCtr = 0;
			}
			if( $this_page != strtolower('/Baseball-Cards/new_card.php')){		
				echo '<a class="confirmation" href="new_card.php">+ ADD NEW CARD</a>';
				$myCtr +=1;
			}
			if ($myCtr > 0) {
				echo ' &nbsp; | &nbsp; ';
			}
			if( $this_page != strtolower('/Baseball-Cards/display_case.php') ){
				echo ' <a class="confirmation" href="display_case.php">VIEW VIRTUAL DISPLAYS</a> &nbsp; | &nbsp; ';
			}
			if( $this_page != strtolower('/Baseball-Cards/card_game.php') ){
				echo ' <a href="card_game.php" style="font-weight: bold;">PLAY CARD GAME</a> &nbsp; | &nbsp ';
			}
			if( $this_page != strtolower('/Baseball-Cards/game_teams.php') ){
				echo ' <a class="confirmation" href="game_teams.php">MANAGE CARD GAME</a>';
			}	
		} else if( $user_role == 'user' ){
			//Display the links which are appropriate for the 'user' user
			if( $this_page != strtolower('/Baseball-Cards/display_case.php') ){
				echo ' <a class="confirmation" href="display_case.php">VIEW VIRTUAL DISPLAYS</a> &nbsp; | &nbsp; ';
			}
			if( $this_page != strtolower('/Baseball-Cards/card_game.php') ){
				echo ' <a href="card_game.php">PLAY CARD GAME</a> &nbsp; | &nbsp ';
			}
			if( $this_page != strtolower('/Baseball-Cards/game_teams.php') ){
				echo ' <a class="confirmation" href="game_teams.php">MANAGE CARD GAME</a>';
			}	
		
		} else if( $user_role == 'guest' ){
			//Display the links which are appropriate for the 'guest' user
						
			if( $this_page != strtolower('/Baseball-Cards/display_case.php') ){
				echo ' <a class="confirmation" href="display_case.php">VIEW VIRTUAL DISPLAYS</a> &nbsp; | &nbsp; ';
			}
		
		}
	
		echo '&nbsp; &nbsp; <a href="logout.php" class="link-button" style="display: inline-block; position: relative; background-color: white; color: #0C2340;">LOGOUT</a>';
		echo '&nbsp; &nbsp; <span style="font-weight: bold;">Hi '. ucfirst( $_SESSION['username'] ) . '!</span>';
	} else {
		//User is not logged in
		if( $this_page != strtolower('/Baseball-Cards/display_case.php') & $this_page != strtolower("/Baseball-Cards/index.php")){
			echo ' <a href="display_case.php">VIEW VIRTUAL DISPLAYS</a>';
		}
		if( $this_page != strtolower('/Baseball-Cards/login.php' )){
			echo '&nbsp; &nbsp; <a href="login.php" class="link-button" style="display: inline-block; position: relative; ">LOGIN</a>';
		}
	}

?>	
	</div>
</div>	
<div id="contentWrapper" style="margin-left: 20px;">
<!-- End Header -->
<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shadisland Baseball Card Game</title>
    
    <style>
    body, label {
    	cursor: url(/images/baseball-cursor-arrow.png), auto;
    }
    .link-button {	
    	cursor: url(/images/baseball-cursor-arrow.png), auto;
    }
    th {
    	text-align: center;
    }
    ul {
	font-family: monospace;
	font-size: 16px;
	list-style: none;
	margin: 10px 0px 0px 5px;
	padding: 0;
  	li {
	    margin: 0;
	    margin-bottom: 5px;
	    padding-left: 12px;
	    position: relative;
    
		&:after {
		      content: '';
		      height: 6px;
		      width: 6px;
		      background: darkblue;
		      display: block;
		      position: absolute;
		      transform: rotate(45deg);
		      top: 7px;
		      left: 0;
    		}
    	}
    }
    .scoreboard-row td {
    	border: 1px solid darkgray;
    	max-height: 25px;
 	overflow: hidden;
 	white-space: nowrap;
 	padding: 3px;
 	width: 15px;
    }
    .scoreboard-row th {
    	font-weight: 600; 
    }
    #outfield img {
    	width: 100px;
    }
    #infield img {
    	width: 100px;
    }
    #away-dugout img {
    	width: 50px;
    }
    #home-dugout img {
    	width: 50px;
    }
    #first-base img {
    	width: 50px;
	transform: rotate(45deg);
	left: -20px;
	top: -100px;
    	box-shadow: 2px 2px 22px yellow;
    }
    #second-base img {
    	width: 50px;
    	transform: rotate(45deg);
	right: 0px;
	top: -70px;
    	box-shadow: 2px 2px 22px yellow;
    }
    #third-base img {
    	width: 50px;
    	box-shadow: 2px 2px 22px yellow;
    }
    #batters-box-left img {
    	width: 50px;
    	/*left: -255px;
	top: -245px;*/
	left: -30px;
	top: 0px;
    	box-shadow: 2px 2px 22px yellow;
    }
    .enlarged-batter-away {
    	width: 280px !important;
    	left: -500px !important;
    	top: -245px !important;
    }
    .enlarged-batter-home {
    	width: 280px !important;
    	left: 350px !important;
    	top: -245px !important;
    }
    .raw {
    	filter: brightness(120%) contrast(130%);
    }
    .PSA {
    	filter: brightness(120%) contrast(130%);
    }
    .disabled-link-button {
    	background-color: lightgray !important;
    }
    #out-type {
    	font-weight: bold;
    	font-size: 22px;
    	color: darkred;
    	text-shadow: 2px 2px 5px darkgray;
    }
    #hit-type {
    	font-weight: bold;
    	font-size: 26px;
    	color: blue;
    	text-shadow: 4px 4px 8px yellow;
    }    
    .Home-Run {
    	font-size: 34px !important;
    	font-weight: bold !important;
    	color: yellow !important;
  	text-shadow: 5px 5px 8px black, 0 0 50px blue, 0 0 25px darkblue !important;
    }
    .Walk {
    	color: black !important;
    }
    .Strikeout {
    	color: white !important;
	text-shadow: 2px 2px 4px black, 0 0 25px red, 0 0 15px darkred !important;
    }
    @keyframes home-animation {
      0%  { left:0px; top:0px;}
      100% { left:-425px; top:-400px;}
    }
    @keyframes away-animation {
      0%  { right:0px; top:0px;}
      100% { right:-425px; top:-400px;}
    }
    .animated-home {
    	animation-name: home-animation;
    	animation-duration: 3s;
	animation-delay: 1s;  
    }
    .animated-away {
    	animation-name: away-animation;
    	animation-duration: 3s;
	animation-delay: 1s;  
    }
    #box-stats-table {
    	border: 1px solid gray; 
    	padding: 5px;
    	margin-top: 10px;
    }
    #box-stats-table th{
    	border: 0px solid gray; 
    	padding: 5px;
    }
    #box-stats-table tr{
    	border: 0px solid gray; 
    	padding: 5px;
    	text-align: center;
    }
    #season-standings-table {
    	border: 1px solid gray; 
    	padding: 5px;
    	margin-top: 10px;
    }
    #season-standings-table th{
    	border: 0px solid gray; 
    	padding: 5px;
    }
    #season-standings-table tr{
    	border: 0px solid gray; 
    	padding: 5px;
    	text-align: center;
    }
    .out-dot {
      height: 13px;
      width: 13px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
      margin: 0px 3px 0px 3px;
    }
    .dot-on {
      background-color: #cc0000 !important;
    }
    #scoreboard-top-inning {
    	color: lightgreen;
    }
    #scoreboard-bottom-inning {
    	color: lightgreen;
    }
    .scoreboard-extra-innings {
    	letter-spacing: -3px;
    	display: none;
    }
    @keyframes fadeIn {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }
    
    .fadeIn-animation {
     	animation: 10s fadeIn;
    }
    </style>
     <link rel="stylesheet" href="/css/styles.css">
     <link rel="stylesheet" href="/css/animation.css">
     <link rel="stylesheet" type="text/css" href="/water-animation/waterstyle.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" />
    
    <?php require 'head_scripts_include.php';?>
    
    
    <script>
    	function nth(n){return["st","nd","rd"][((n+90)%100-10)%10-1]||"th"}
    	
        $(document).ready(function () {
            
		var game = {

			config:{
				gameid: 0,
				hometeam: 'BOSTON',
				awayteam: 'X',
				hometeamid: '1',
				awayteamid: '2',
				inning: 1,
				toporbottom: 'top',
				runs: 0,
				hits: 0,
				errors: 0,
				outs: 0,
				walks: 0,
				strikeouts: 0,
				batternumberhome: 0,
				batternumberaway: 0,
				firstbaserunner: false,
				secondbaserunner: false,
				thirdbaserunner: false,
				totalwalkshome: 0,
				totalwalksaway: 0,
				totalstrikeoutshome: 0,
				totalstrikeoutsaway: 0,
				totalrunshome: 0,
				totalrunsaway: 0,
				totalhitshome: 0,
				totalhitsaway: 0,
				totalerrorshome: 0,
				totalerrorsaway: 0,
				isfinal: false,
				endOfInning: false
			},
			
			advanceRunners:function(hitType){
				var advanceToSecond = false;
				var advanceToThird = false;
				var advanceToHome = 0;
				var homeoraway = (this.config.toporbottom == 'top' ? 'home' : 'away');
		 
				if( hitType == 'flyout' ) {
					//If fly out was sent to this function (after dice roll), advance runners one base.
					if( this.config.thirdbaserunner ) {
						advanceToHome += 1;
						//send him to the dugout
						if( this.config.toporbottom == 'top' ) {
							$("#away-dugout").prepend( $("#third-base").children('img')[0] );
						} else {
							$("#home-dugout").prepend( $("#third-base").children('img')[0] );
						}
						
					}
					if( this.config.secondbaserunner ) {
						advanceToThird =  true;
						$("#third-base").prepend( $("#second-base").children('img')[0] );		
					}
					if( this.config.firstbaserunner ) {
						advanceToSecond =  true;
						$("#second-base").prepend( $("#first-base").children('img')[0] );
					} 			
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
					
				}
				
				if( hitType == 'Walk' ) {
					if( this.config.thirdbaserunner ) {
						//Only advance if there is a firstbaserunner, and a secondbaserunner 
						if( this.config.firstbaserunner && this.config.secondbaserunner) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						}
					}
					if( this.config.secondbaserunner ) {
						//Only advance if there is a firstbaserunner
						if( this.config.firstbaserunner ) {
							advanceToThird =  true;
							$("#third-base").prepend( $("#second-base").children('img')[0] );
						}
					}
					if( this.config.firstbaserunner ) {
						advanceToSecond =  true;
						$("#second-base").prepend( $("#first-base").children('img')[0] );
					} 
					this.config.firstbaserunner =  true;
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
					this.config.walks += 1;
					$('#scoreboard-walks').text( this.config.walks );
					if( this.config.toporbottom == 'top' ) {
						this.config.totalwalksaway += 1;
						$('#total-walks-away').text( this.config.totalwalksaway );
					} else {
						this.config.totalwalkshome += 1;
						$('#total-walks-home').text( this.config.totalwalkshome );
					}
					//move batter's card to first base
					$("#first-base").append( $("#batters-box-left").children('img')[0] );
				} else {
					if( hitType == 'Single' ) {
						if( this.config.thirdbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						}
						if( this.config.secondbaserunner ) {
							advanceToThird =  true;
							$("#third-base").prepend( $("#second-base").children('img')[0] );
						}
						if( this.config.firstbaserunner ) {
							advanceToSecond =  true;
							$("#second-base").prepend( $("#first-base").children('img')[0] );
						} 
						this.config.firstbaserunner =  true;
						this.config.secondbaserunner = advanceToSecond;
						this.config.thirdbaserunner = advanceToThird;
						this.config.hits += 1;
						$('#scoreboard-hits').text( this.config.hits );
						//move batter's card to first base
						$("#first-base").append( $("#batters-box-left").children('img')[0] );
					} else if( hitType == 'Double' ) {
						if( this.config.thirdbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						}
						if( this.config.secondbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#second-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#second-base").children('img')[0] );
							}
						}
						if( this.config.firstbaserunner ) {
							advanceToThird =  true;
							$("#third-base").prepend( $("#first-base").children('img')[0] );
						} 
						this.config.firstbaserunner = false;
						this.config.secondbaserunner =  true;
						this.config.thirdbaserunner = advanceToThird;
						this.config.hits += 1;
						$('#scoreboard-hits').text( this.config.hits );
						//move card to second base
						$("#second-base").append( $("#batters-box-left").children('img')[0] );

					} else if( hitType == 'Triple' ) {
						if( this.config.thirdbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						}
						if( this.config.secondbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#second-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#second-base").children('img')[0] );
							}
						}
						if( this.config.firstbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#first-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#first-base").children('img')[0] );
							}
						} 
						this.config.firstbaserunner = false;
						this.config.secondbaserunner =  false;
						this.config.thirdbaserunner =  true;
						this.config.hits += 1;
						$('#scoreboard-hits').text( this.config.hits );
						//move card to third base
						$("#third-base").append( $("#batters-box-left").children('img')[0] );

					} else if( hitType == 'Home Run' ) {
						advanceToHome += 1;
						if( this.config.thirdbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						}
						if( this.config.secondbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#second-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#second-base").children('img')[0] );
							}
						}
						if( this.config.firstbaserunner ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#first-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#first-base").children('img')[0] );
							}
						} 
						this.config.firstbaserunner = false;
						this.config.secondbaserunner =  false;
						this.config.thirdbaserunner =  false;
					}
					if( hitType != 'flyout' ) {
						this.config.hits += 1;
						$('#scoreboard-hits').text( this.config.hits );
					}
					//increase runs with advanceToHome value
					this.config.runs += advanceToHome;
					if( this.config.toporbottom == 'top' ) {
						if( hitType != 'flyout' ) {
							this.config.totalhitsaway += 1;
							$('#total-hits-away').text( this.config.totalhitsaway );
						}
						this.config.totalrunsaway += advanceToHome;
						$('#inning-'+ this.config.inning + '-away').text(this.config.runs);
						$('#total-runs-away').text(this.config.totalrunsaway);
						//Update runs against home pitcher
						$('#home-pitcher').attr("data-ingame-runs-against", parseInt($('#home-pitcher').attr("data-ingame-runs-against")) + advanceToHome);	
					} else {
						if( hitType != 'flyout' ) {
							this.config.totalhitshome += 1;
							$('#total-hits-home').text( this.config.totalhitshome );
						}
						this.config.totalrunshome += advanceToHome;
						$('#inning-'+ this.config.inning + '-home').text(this.config.runs);
						$('#total-runs-home').text(this.config.totalrunshome);
						//Update runs against away pitcher
						$('#away-pitcher').attr("data-ingame-runs-against", parseInt($('#away-pitcher').attr("data-ingame-runs-against")) + advanceToHome);
					}	
                			$("#mini-home-score").text( $("#home-team-name").text() + ":" + this.config.totalrunshome);
                			$("#mini-away-score").text( $("#away-team-name").text() + ":" + this.config.totalrunsaway);
					
				}
			},
			
//*****EndGame						
			endGame:function(){	
				//Save results to the database
				
				this.config.isfinal = true;
				
				//disable buttons and announce "Final"
				$(".batter-up").prop("disabled", true);
				$(".batter-up").addClass("disabled-link-button");
				$(".send-pitch").prop("disabled", true);
				$(".send-pitch").addClass("disabled-link-button");
				$('#status-msg').css("display", "block");	
				
				//Update game_team wins/losses/runs for both teams
				var homeTeamId = $("#home-team-id").attr("value");
				var awayTeamId = $("#away-team-id").attr("value");
				var homeTeamRuns = $("#total-runs-home").text();
				var awayTeamRuns = $("#total-runs-away").text();
				
				//Update pitchers' win/loss
				if( homeTeamRuns > awayTeamRuns ) {
					$('#home-pitcher').attr('data-ingame-wins', '1' );
					$('#away-pitcher').attr('data-ingame-losses', '1' );
				} else {
					$('#home-pitcher').attr('data-ingame-losses', '1');
					$('#away-pitcher').attr('data-ingame-wins', '1' );
				}
				//Put player stats into an array
				var playerStats = [];
				var myCtr = 0;
				$("#home-dugout img").each(function(index, value) {
					var playerObj = [];
					//numeric.push([this.name, this.value]);
					playerObj.push( ['card_id', $(this).attr('data-card-id')] );
					playerObj.push( ['atbats', $(this).attr('data-ingame-atbats')] );
					playerObj.push( ['hits', $(this).attr('data-ingame-hits')] );
					playerObj.push( ['strikeouts', $(this).attr('data-ingame-strikeouts')] );
					playerObj.push( ['walks', $(this).attr('data-ingame-walks')] );
					playerObj.push( ['homeruns', $(this).attr('data-ingame-home-runs')] );
					
					if( $(this).attr('id') == 'home-pitcher' ) {
						playerObj.push( ['wins', $(this).attr('data-ingame-wins')] );
						playerObj.push( ['losses', $(this).attr('data-ingame-losses')] );
						playerObj.push( ['ip', $(this).attr('data-ingame-ip')] );
						playerObj.push( ['runsagainst', $(this).attr('data-ingame-runs-against')] );
						playerObj.push( ['strikeoutssagainst', $(this).attr('data-ingame-strikeouts-against')] );
						playerObj.push( ['walksagainst', $(this).attr('data-ingame-walks-against')] );									
						
					} else {
						playerObj.push( ['wins', '0'] );
						playerObj.push( ['losses', '0'] );
						playerObj.push( ['ip', '0'] );
						playerObj.push( ['runsagainst', '0'] );	
						playerObj.push( ['strikeoutssagainst', '0'] );
						playerObj.push( ['walksagainst', '0'] );	
					}
					
					playerStats[myCtr] = playerObj;
					
					myCtr++;
				});
				
				$("#away-dugout img").each(function(index, value) {
					var playerObj = [];
					//numeric.push([this.name, this.value]);
					playerObj.push( ['card_id', $(this).attr('data-card-id')] );
					playerObj.push( ['atbats', $(this).attr('data-ingame-atbats')] );
					playerObj.push( ['hits', $(this).attr('data-ingame-hits')] );
					playerObj.push( ['strikeouts', $(this).attr('data-ingame-strikeouts')] );
					playerObj.push( ['walks', $(this).attr('data-ingame-walks')] );
					playerObj.push( ['homeruns', $(this).attr('data-ingame-home-runs')] );
					
					if( $(this).attr('id') == 'away-pitcher' ) {
						playerObj.push( ['wins', $(this).attr('data-ingame-wins')] );
						playerObj.push( ['losses', $(this).attr('data-ingame-losses')] );
						playerObj.push( ['ip', $(this).attr('data-ingame-ip')] );
						playerObj.push( ['runsagainst', $(this).attr('data-ingame-runs-against')] );
						playerObj.push( ['strikeoutssagainst', $(this).attr('data-ingame-strikeouts-against')] );
						playerObj.push( ['walksagainst', $(this).attr('data-ingame-walks-against')] );	
					} else {
						playerObj.push( ['wins', '0'] );
						playerObj.push( ['losses', '0'] );
						playerObj.push( ['ip', '0'] );
						playerObj.push( ['runsagainst', '0'] );	
						playerObj.push( ['strikeoutssagainst', '0'] );
						playerObj.push( ['walksagainst', '0'] );	
					}
					
					playerStats[myCtr] = playerObj;
					myCtr++;
				});
				
				playerStats = JSON.stringify(playerStats);
				//console.log(playerStats);
				$.ajax({
					type: "POST",
					url: "update_game_team_stats.php",
					data: { player_stats: playerStats, home_team_id: homeTeamId, away_team_id: awayTeamId, home_team_runs: homeTeamRuns,away_team_runs: awayTeamRuns },
					success: function(response){
					    //alert(response);
					     
					},
					error: function(){
					    alert("Error updating teams.");
					}
				});
			},
//***Start Inning			
			startInning:function(){	
				this.config.endOfInning = false;
				var dugout = "";
				var thisTeamId = "" ;
				if( this.config.toporbottom == 'top' ) {
					dugout = "home";
					thisTeamId = $("#home-team-id").attr("value");
					$('#mini-toporbottom').html("&#x25B2"); 
				} else {
					dugout = "away";
					thisTeamId = $("#away-team-id").attr("value");
					$('#mini-toporbottom').html("&#x25BC"); 
				}
				$('#mini-inning').html( this.config.inning + nth(this.config.inning) ); 
							
				//Animated transform location
				$("#" + dugout + "-dugout img").addClass("animated-" + dugout);
			//alert("here");
				//Now put them in positions
				//read the positions from the hidden record for the home team
				setTimeout(function() { 
					$("#" + dugout + "-dugout img").removeClass("animated-"  + dugout);
					$("#" + dugout + "-dugout img").each(function(index, value) {
						//console.log(  $("#game-team-" + home_team_id ).attr('data-pitcher-id') );
						if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("pitcher-id") ) {
							$("#pitcher").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("catcher-id") ) {
							$("#catcher").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("first-base-id") ) {
							$("#first-baseman").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("second-base-id") ) {
							$("#second-baseman").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("short-stop-id") ) {
							$("#short-stop").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("third-base-id") ) {
							$("#third-baseman").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("left-field-id") ) {
							$("#left-fielder").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("center-field-id") ) {
							$("#center-fielder").append( $(this) );						  			
						} else if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data("right-field-id") ) {
							$("#right-fielder").append( $(this) );						  			
						}
					});
				}, 3500);
			},
//****End Inning			
			endInning:function(){	
		//***TODO: Add a 'Save Game' button
				if( this.config.toporbottom == 'top' ) {
					//update home team pitcher's innings pitched
					$('#home-pitcher').attr('data-ingame-ip', parseInt($('#home-pitcher').attr('data-ingame-ip')) +1 );
					
					//update the scoreboard
					$('#inning-'+ this.config.inning + '-away').text(this.config.runs);
					//clear the bases
					$("#away-dugout").append( $("#first-base").children("img:first-child") );
					$("#away-dugout").append( $("#second-base").children("img:first-child") );
					$("#away-dugout").append( $("#third-base").children("img:first-child") );
					this.config.toporbottom = 'bottom';
					//Move the dot from top to bottom of the inning on the scoreboard
					$('#scoreboard-top-inning').html('&nbsp;');
					$('#scoreboard-bottom-inning').html('&#9679');
					//move previous batter to the dugout
					$("#away-dugout").append( $("#batters-box-left").children("img:first-child") );
					this.config.batternumberaway += 1;
					if( this.config.batternumberaway > 9 ) {
						this.config.batternumberaway = 1;
					}
					//Move home team fielders to the dugout
					$("#home-dugout").append( $("#left-fielder").children("img:first-child") );
					$("#home-dugout").append( $("#center-fielder").children("img:first-child") );
					$("#home-dugout").append( $("#right-fielder").children("img:first-child") );
					$("#home-dugout").append( $("#first-baseman").children("img:first-child") );
					$("#home-dugout").append( $("#second-baseman").children("img:first-child") );
					$("#home-dugout").append( $("#short-stop").children("img:first-child") );
					$("#home-dugout").append( $("#third-baseman").children("img:first-child") );
					$("#home-dugout").append( $("#pitcher").children("img:first-child") );
					$("#home-dugout").append( $("#catcher").children("img:first-child") );
					
					if( this.config.inning == 9 && this.config.totalrunshome > this.config.totalrunsaway ){
						$.proxy(this.endGame(), this);
					} else {
						$('#inning-announcement').html("BOTTOM<br>OF THE<br>" + this.config.inning + nth(this.config.inning));
						$('#inning-announcement-wrapper').show();
						
						setTimeout(function() { 
							$('#inning-announcement-wrapper').hide();
							$("#show-pitch").hide();
						}, 3000);
					}
					
				} else {
					//update away team pitcher's innings pitched
					$('#away-pitcher').attr('data-ingame-ip', parseInt($('#away-pitcher').attr('data-ingame-ip')) +1 );
					
					//Update the scoreboard
					$('#inning-'+ this.config.inning + '-home').text(this.config.runs);
					//clear the bases
					$("#home-dugout").append( $("#first-base").children("img:first-child") );
					$("#home-dugout").append( $("#second-base").children("img:first-child") );
					$("#home-dugout").append( $("#third-base").children("img:first-child") );
					this.config.inning += 1;
					this.config.toporbottom = 'top';
					
					$('#inning-announcement').html("TOP<br>OF THE<br>" + this.config.inning + nth(this.config.inning));
					$('#inning-announcement-wrapper').show();
						
					setTimeout(function() { 
						$('#inning-announcement-wrapper').hide();
						$("#show-pitch").hide();
					}, 3000);
					
					
					//move previous batter to the dugout
					$("#home-dugout").append( $("#batters-box-left").children("img:first-child") );
					this.config.batternumberhome += 1;
					if( this.config.batternumberhome > 9 ) {
						this.config.batternumberhome = 1;
					}
					//move away fielders to the dugout
					$("#away-dugout").append( $("#left-fielder").children("img:first-child") );
					$("#away-dugout").append( $("#center-fielder").children("img:first-child") );
					$("#away-dugout").append( $("#right-fielder").children("img:first-child") );
					$("#away-dugout").append( $("#first-baseman").children("img:first-child") );
					$("#away-dugout").append( $("#second-baseman").children("img:first-child") );
					$("#away-dugout").append( $("#short-stop").children("img:first-child") );
					$("#away-dugout").append( $("#third-baseman").children("img:first-child") );
					$("#away-dugout").append( $("#pitcher").children("img:first-child") );
					$("#away-dugout").append( $("#catcher").children("img:first-child") );
			//Testing hack if( this.config.inning == 2 ){
					if( this.config.inning > 9 && this.config.totalrunshome != this.config.totalrunsaway){
						$.proxy(this.endGame(), this);
					} else if( this.config.inning > 9 && this.config.totalrunshome == this.config.totalrunsaway){
						//Go into Extra Innings
						//Unhide extra scoreboard innings
						$('.scoreboard-extra-innings').show();
						//Move the dot from bottom to top of the inning on the scoreboard
						$('#scoreboard-top-inning').html('&#9679');
						$('#scoreboard-bottom-inning').html('&nbsp;');
					} else {
						//Move the dot from bottom to top of the inning on the scoreboard
						$('#scoreboard-top-inning').html('&#9679');
						$('#scoreboard-bottom-inning').html('&nbsp;');
					}
				}
				this.config.runs = 0;
				this.config.hits = 0;
				this.config.errors = 0;
				this.config.outs = 0;
				this.config.walks = 0;
				this.config.strikeouts = 0;
				this.config.firstbaserunner = false;
				this.config.secondbaserunner = false;
				this.config.thirdbaserunner = false;
				this.config.endOfInning = true;
				
				
				//reset the scoreboard
				$('#scoreboard-runs').text( this.config.runs );
				$('#scoreboard-hits').text( this.config.hits );
				$('#scoreboard-errors').text( this.config.errors );
				//$('#scoreboard-outs').text( this.config.outs );
				for(var i=1; i <= 3; i++){
					$("#out-"+i).removeClass("dot-on");
				}
				$('#scoreboard-walks').text( this.config.walks );
				$('#scoreboard-strikeouts').text( this.config.strikeouts );
				if( !this.config.isfinal ) {
					$.proxy(this.startInning(), this);
				}
			},
//***LogHit			
			logHit:function(hitWalkOrStrikeout, batterNumberId){
				batterNumberId = batterNumberId.replace("-back", "");
				if( hitWalkOrStrikeout == "hit" ) {
					//alert(batterNumberId);				
					//$("#"+batterNumberId).data("ingame-hits", $("#"+batterNumberId).data("ingame-hits") + 1 );
					$("#"+batterNumberId).attr("data-ingame-hits", parseInt($("#"+batterNumberId).attr("data-ingame-hits")) + 1 );
				} else if( hitWalkOrStrikeout == "walk" ) {
					$("#"+batterNumberId).attr("data-ingame-walks", parseInt($("#"+batterNumberId).attr("data-ingame-walks")) + 1 );
					if( this.config.toporbottom == 'top' ) {
						$('#home-pitcher').attr("data-ingame-walks-against", parseInt( $('#home-pitcher').attr("data-ingame-walks-against")) + 1 );
					}else {
						$('#away-pitcher').attr("data-ingame-walks-against", parseInt( $('#away-pitcher').attr("data-ingame-walks-against")) + 1 );
					}
				} else {
					$("#"+batterNumberId).attr("data-ingame-strikeouts", parseInt($("#"+batterNumberId).attr("data-ingame-strikeouts")) + 1 );
					$("#"+batterNumberId).attr("data-ingame-walks", parseInt($("#"+batterNumberId).attr("data-ingame-walks")) + 1 );
					if( this.config.toporbottom == 'top' ) {
						$('#home-pitcher').attr("data-ingame-strikeouts-against", parseInt( $('#home-pitcher').attr("data-ingame-strikeouts-against")) + 1);
					}else {
						$('#away-pitcher').attr("data-ingame-strikeouts-against", parseInt( $('#away-pitcher').attr("data-ingame-strikeouts-against")) + 1);
					}
				} 
				
			},
			
			init:function(config){
				$.extend(this.config,config);
				
				
//*******Season and Team Select
				$('#season-select-button').click(function(e) {
					//When they click the season select button, reload the page with ?game_season_id=1	
					location.assign('card_game-previous.php?game_season_id=' + $('#season-select').find('option:selected:first').attr("value"));	
				});				
				$('#home-team-select-button').click(function(e) {
					//hide the selector and display team name in text
					$('#home-team-select-button').hide();
					$('#selected-home-team').text( $("#home-team-select option:selected" ).text() );
					$('#home-team-select').hide();
					//$('#home-team-select').css('display', 'none');
					$('#away-team-select').show();
					//$('#away-team-select').css('display', 'block');
					$('#team-select-submit').show();
					//$('#team-select-submit').css('display', 'block');
					//Remove home team from the away team selector
					 $("#away-team-select option[value='" + $('#home-team-select').val() + "']").remove(); 
				});
				
				$("#team-select-form").submit(function(e) {
					$("#new-game-msg").hide();
					//Get the cards for the selected teams
                			e.preventDefault();
                			//alert(this.home_team_id.value);
                			$("#home-team-id").attr("value", this.home_team_id.value);
                			$("#away-team-id").attr("value", this.away_team_id.value);
                			
                			              			
                			$("#home-team-name-holder").attr("value", $(this).find('option:selected:first').text());
                			$("#away-team-name-holder").attr("value", $(this).find('option:selected:last').text());
                			$("#home-team-name").text( $(this).find('option:selected:first').text() );
                			$("#away-team-name").text( $(this).find('option:selected:last').text() );
                			$("#mini-home-score").text( $("#home-team-name").text() + ":0");
                			$("#mini-away-score").text( $("#away-team-name").text() + ":0");
                			
                 			//temporarily disable Start button
					$(this).find(":submit").attr('disabled', 'disabled');
                			$.ajax({
						type: "POST",
						url: "get_game_team_cards.php",
						data: $(this).serialize(),
						dataType: "json",
						success: function(response){ 
							//alert( response[0].dugout_img_url );
	
							for (let i = 0; i < response.length; i++) {
								//Image Backs
								cardBack = response[i].img_back_data;
								cardBackResult = cardBack.replace(/\\/g,"");
								$("#preload-card-backs").append( cardBackResult );
								 	 
								//Dugouts
								cardFront = response[i].dugout_img;
								cardFrontResult = cardFront.replace(/\\/g,"");
								//console.log('cardfrontresult' + cardFrontResult);
								if( cardFrontResult.indexOf("home-pitcher") > -1 || cardFrontResult.indexOf("home-batter") > -1){
									$("#home-dugout").append( cardFrontResult );
								} else {
									$("#away-dugout").append( cardFrontResult );
									//console.log('away card to dugout'+cardFrontResult);
								}
							}
							
							//Animated transform location
    							setTimeout(function() { 
    								$("#home-dugout img").addClass("animated-home");
    							}, 1000);
    							//alert("here");
							//Now put them in positions
							//read the positions from the hidden record for the home team
							setTimeout(function() { 
							  	$("#home-dugout img").removeClass("animated-home");
								$("#home-dugout img").each(function(index, value) {
							  		//console.log(  $("#game-team-" + home_team_id ).attr('data-pitcher-id') );
							  		var homeTeamId = $("#home-team-id").attr("value");
							  	
							  		if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("pitcher-id") ) {
							  			$("#pitcher").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("catcher-id") ) {
							  			$("#catcher").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("first-base-id") ) {
							  			$("#first-baseman").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("second-base-id") ) {
							  			$("#second-baseman").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("short-stop-id") ) {
							  			$("#short-stop").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("third-base-id") ) {
							  			$("#third-baseman").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("left-field-id") ) {
							  			$("#left-fielder").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("center-field-id") ) {
							  			$("#center-fielder").append( $(this) );						  			
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("right-field-id") ) {
							  			$("#right-fielder").append( $(this) );						  			
							  		}
							  		
							  		//Enable Batter up
									$(".batter-up").prop("disabled", false);
									$(".batter-up").removeClass("disabled-link-button");
								});
    							}, 3500);
    							
						},
						error: function(){
							alert("Error loading teams.");
						}
					});
					
					
				});
				
				$("#status-button").click(function(e) {
						$("#status-msg").css("display", "none");
						location.reload();
					}
				);
//*******Batter Up				
				$(".batter-up").click($.proxy(function(e) {  
						$("#show-pitch").css("display", "none");
						var batterNumberId = "";
						var enlargeString = "";
						if( this.config.toporbottom == 'top' ) {
							//move previous batter to the dugout
							$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-home");
							$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-away");
							$("#away-dugout").append( $("#batters-box-left").children("img:first-child") );
							
							//identify next batter
							this.config.batternumberaway += 1;
							if( this.config.batternumberaway > 9 ) {
								this.config.batternumberaway = 1;
							}
							batterNumberId = "away-batter-" + this.config.batternumberaway;
							enlargeString = "enlarged-batter-away";
							//update soreboard with batter number
							$('#scoreboard-batter').text( this.config.batternumberaway );
						} else {
							//move previous batter to the dugout
							$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-home");
							$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-away");
							$("#home-dugout").append( $("#batters-box-left").children("img:first-child") );
							
							//identify next batter
							this.config.batternumberhome += 1;
							if( this.config.batternumberhome > 9 ) {
								this.config.batternumberhome = 1;
							}
							batterNumberId = "home-batter-" + this.config.batternumberhome;
							enlargeString = "enlarged-batter-home";
							//update soreboard with batter number
							$('#scoreboard-batter').text( this.config.batternumberhome );
													
						}
						//put a batter in the batters box (TODO: add left or right handed batter to database)
						$("#batters-box-left").prepend($("#" + batterNumberId));
						//enlarge the batter until the pitch button is clicked
						$("#batters-box-left").children("img:first-child").addClass(enlargeString);
						$(".batter-up").prop("disabled", true);
						$(".batter-up").addClass("disabled-link-button");
						if( !this.config.isfinal ){
							$(".send-pitch").prop("disabled", false);		
							$(".send-pitch").removeClass("disabled-link-button");
						}
						//alert(batterNumberId);   
			//Testing auto-click of 'Send Pitch' button
			//Delay

			setTimeout(function() { 
				$(".send-pitch").click();
			}, 1500);
				
					}, this ) 
				);
				
//***********pitch
				
				$(".send-pitch").click($.proxy(function(e) {
					//Set wait time for enabling Batter Up button
					var batterButtonWaitTime = 6000;
					
					//reduce batter size, at the plate
					$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-home");
					$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-away");
					
					//Replace the previous card back images to hidden Div if necessary
					if( !$("#pitch-pitcher-img").is(':empty') ) {	
						$("#preload-card-backs").append($("#pitch-pitcher-img").children('img')[0]);
					}
					if( !$("#pitch-batter-img").is(':empty') ) {	
						$("#preload-card-backs").append($("#pitch-batter-img").children('img')[0]);
					}
					
					$("#pitch-pitcher-data").empty();
					$("#pitch-batter-data").empty();
					$("#pitch-batter-name").empty();
					$("#pitch-pitcher-name").empty();
					$("#pitch-batter-img").empty();
					$("#pitch-pitcher-img").empty();
					$("#pitch-results").empty();
					$("#pitch-results").append( '<span style="font-weight: bold;">At-Bat Console</span><br>' );
					
					//show backs (or fronts) of batter and pitcher cards in an otherwise empty div (similar to a modal window)
					var batterNumberId = "";
					var pitcherNumberId = "";
					
					//hitBooster % to make hits happen more often. 
					var hitBooster = 20; // 20 = 2%
					
					if( this.config.toporbottom == 'top' ) {
						batterNumberId = "away-batter-" + this.config.batternumberaway + "-back";
						pitcherNumberId = "home-pitcher-back";
					} else {
						batterNumberId = "home-batter-" + this.config.batternumberhome + "-back";
						pitcherNumberId = "away-pitcher-back";
						//Home team gets extra hit boost for "home team advantage"
						hitBooster = 40; // 40 = 4%
					}
					var seasonEra = $("#"+pitcherNumberId).data("season-era") ;
					var seasonBattingAvg = $("#"+batterNumberId).data("season-batting-avg");
					var seasonDoubles = $("#"+batterNumberId).data("season-2b");
					var seasonTriples = $("#"+batterNumberId).data("season-3b");
					var seasonHRs = $("#"+batterNumberId).data("season-hr");
					var pitcherSo = $("#"+pitcherNumberId).data("season-so") ;
					var pitcherBb = $("#"+pitcherNumberId).data("season-bb") ;
					var seasonIp = $("#"+pitcherNumberId).data("season-ip") ;
					var seasonAtBats = $("#"+batterNumberId).data("season-at-bats");
					//Pitcher's SO9 is (9 x strikeouts) / innings pitched 
					var so9 = ( 9 * pitcherSo) / seasonIp;
					
					$("#show-pitch").css("display", "block");
					$(".send-pitch").prop("disabled", true);
					$(".send-pitch").addClass("disabled-link-button");
					// use elements that contains the backs (preloaded)
					$("#pitch-batter-img").append($("#"+batterNumberId));
					//alert(pitcherNumberId);
					$("#pitch-pitcher-img").append($("#"+pitcherNumberId));
					$("#pitch-batter-name").append( $("#"+batterNumberId).data("player-name") + "<br>" + $("#"+batterNumberId).data("season-year") + " Season" );
					$("#pitch-pitcher-name").append( $("#"+pitcherNumberId).data("player-name") + "<br>" + $("#"+pitcherNumberId).data("season-year") + " Season" );
					if (typeof seasonBattingAvg === "number") {
						$("#pitch-batter-data").append( "<LI>Bat AVG:" + seasonBattingAvg.toFixed(3) + "</LI>" );
					} else {
						$("#pitch-batter-data").append( "<LI>Bat AVG: undefined</LI>" );
					}
					$("#pitch-batter-data").append( "<LI>Doubles:" + seasonDoubles + "</LI>" );
					$("#pitch-batter-data").append( "<LI>Triples:" + seasonTriples + "</LI>" );
					$("#pitch-batter-data").append( "<LI>Home Runs:" + seasonHRs + "</LI>" );
					
					$("#pitch-pitcher-data").append( "<LI>IP: " + seasonIp + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>ERA: " + seasonEra + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>Strikeouts: " + pitcherSo + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>Walks: " + pitcherBb + "</LI>" );

				//Matchup calculations								
									
					// Safe/Out Segment: 
					//Average ERA is 4.25. So, if a great pitcher has a lower than average ERA, it puts downward pressure on the batter's BA. If the pitcher has a higher than average ERA, the batter gets a bonus of upward pressure on their BA.
					//safeOutFactor is 10 x ( ( seasonBattingAvg * 100 ) - ( 2 x 4.25 ) + (2 x seasonEra) )
					
					//.240 against 2.5 = 10 * ( (24) - 8.5 + 5 ) = 205 which is a 20.5% chance of a Safe at-bat
					//.240 against 4.25 = 10 * ( (24) - 8.5 + 8.5 ) = 240 which is a 24% chance
					//.240 against 5.75 = 10 *( (24) - 8.5 + 11.5 ) = 270 which is a 27% chance
					var safeOutFactor =  10 * ( ( seasonBattingAvg * 100 ) - 8.5 + (2 * seasonEra) );
					
					safeOutFactor = safeOutFactor + hitBooster;
					var safeOut = "";
					var outType = "";
					var hitType = "";
					let x = Math.floor((Math.random() * 1000) + 1);
					$("#pitch-results").append( "Safe Segment: <=" + safeOutFactor.toFixed(0) + " /1000<br>");
					$("#pitch-results").append( "Dice Roll... <div id=\"x-roll-result\">" + x + "</div><br>");
					
					//This is where the dot dot dot wait should be
					
//***********SAFE
					if( x <= safeOutFactor ) {
					  //Testing Hack
					  //if( x <= 900 ) {
						safeOut = "Safe";
						$("#pitch-results").append( "Safe/Out: <div id=\"safe-out\">" + safeOut + "</div>");
					
						$("#pitch-results").append( '<div class="loader-container"><div class="loading-text" style="font-size: 20px;"><span class="dots"></span></div></div>');	
						
						//Safe Type: (BB, 1st, 2nd, 3rd, HR)
						//Walk Factor: (pitcher BB, IP) * 100
						var walkFactor = (pitcherBb / seasonIp) * 100;
						let y = Math.floor((Math.random() * 100) + 1);
						$("#pitch-results").append( "<div id=\"walk-segment\">Walk Segement: <=" + walkFactor.toFixed(0) + " /100<br>Dice Roll... </div>");
						$("#pitch-results").append( "" ); //+ y + "<br>");
						var hitString = "";
						if( y <= walkFactor ) {
							hitType = "Walk";
							//update the scoreboard
							this.config.walks += 1;
							$('#scoreboard-walks').text( this.config.walks );
						} else {
						
							//Bug fix 
							//Increment the players ingame at-bat count (**but not if it's a Walk)
							$("#batters-box-left").children("img:first-child").attr("data-ingame-atbats", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-atbats")) + 1 );
						//**TODO-Add a teams' overall defensive rating based on players' fielding stats
						//This will make choosing fielders more influential and will make games have more variation in hit types and overall outcomes
							//using MLB aggregate statistics over time from https://analytics.bet/articles/to-the-extreme-mlb-season-props/
							//Singles = 52%, Doubles = 23%, Triples = 5%, HR = 20%							
							var singlesSegment = 52;
							var doublesSegment = 23;
							var triplesSegment = 5;
							var homerunAdjust = 20;
							
							var averageHRA = 17; //Rough average for recent MLB seasons
							// (600 x seasonHRs) / seasonAtBats
							// 600 is a full season. So if a batter had less than 600 at bats, but has an HRA of 45, 
							// we know that was a great HR season
							//If they have below average HRA, it puts downward pressure on their homerunSegment.
							//If they have above average HRA, it puts upward pressure on it.
							
							homerunAdjust = homerunAdjust - averageHRA;
							//Since homerunSegment started at 20, everyone gets at least a 3% chance of a HR, 
							//after we subtract the averageHRA--even if their HRA is 0.
													
							hitString += "<div id=\"hit-segment\">..." + y + " -->HIT!<br><div class=\"loader-container\"><div class=\"loading-text\" style=\"font-size: 20px;\"><span class=\"dots\" style=\"animation-delay: 10s;\"></span></div></div>";
								
							var hrAvg = (600 * seasonHRs) / seasonAtBats;
							homerunAdjust = homerunAdjust + hrAvg;
							hitString += "HR AVG: " + hrAvg.toFixed(0) + " <span style='font-size: 12px;'>(above " +averageHRA + " gets HR boost)</span><br><span style='font-size: 12px;'>HRA=(600*seasonHRs)/seasonAtBats</span><br>";
							
							//Redistribute the pie
							singlesSegment = singlesSegment + ( 20 - homerunAdjust ) 
							//The singlesSegment now has the homerunSegment captured within it, for the sequential
							//conditions below. If the homeRunSegment is larger, the singlesSegment will
							//have been reduced.
							var val1BSegmentPlus1 = singlesSegment + 1;
							var val2BSegment = singlesSegment + doublesSegment;
							var val2BSegmentPlus1 = singlesSegment + doublesSegment + 1;
							var val3BSegment = singlesSegment  + doublesSegment  + triplesSegment;
							var valHomerunSegment = singlesSegment  + doublesSegment  + triplesSegment + 1;
							val2BSegment = val2BSegment.toString();
							val2BSegment = val2BSegment.slice(0, (val2BSegment.indexOf("."))); 
							val2BSegment = parseInt(val2BSegment);
							val3BSegment = val3BSegment.toString();
							val3BSegment = val3BSegment.slice(0, (val3BSegment.indexOf("."))); 
							val3BSegment = parseInt(val3BSegment);
							
							let z = Math.floor((Math.random() * 100) + 1);
							
							hitString += "<span style='font-size: 12px;'>1B Segment: 1 to " + singlesSegment.toFixed(0) + "</span><br><span style='font-size: 12px;'>2B Segment: " + val1BSegmentPlus1.toFixed(0) + " to " + val2BSegment.toFixed(0) + "</span><br><span style='font-size: 12px;'>3B Segment: " + val2BSegmentPlus1.toFixed(0) + " to " + val3BSegment.toFixed(0) + "</span><br><span style='font-size: 12px;'>HR Segment: " + valHomerunSegment.toFixed(0) + " to 100</span><br>Dice Roll... " + z + "<br></div>";
							
							$("#pitch-results").append( hitString );
							
							if( z <= singlesSegment ) {
								hitType = "Single";
							} else if( z <= val2BSegment ) {
								hitType = "Double";
							}else if( z <= val3BSegment ) {
								hitType = "Triple";
							}else {
								hitType = "Home Run";
							}
							//hack for testing
							//hitType = "Home Run";
						}
						var hitText = hitType;
						if( hitType == "Home Run" ){
							hitText = "<br>!! " + hitText + " !!";
							$("#batters-box-left").children("img:first-child").attr("data-ingame-home-runs", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-home-runs")) + 1 );
						} else if( hitType != "Walk" ) {
							hitText = hitText + "!";
							
						} 
											
						
						if( hitType == "Walk" ){	
							$("#pitch-results").append( "<div id=\"hit-segment-2\">... " + y + "<br>Hit Type: <span id=\"hit-type\" class=\"" + hitType.replace(" ", "-") + "\">" + hitText + "</span></div><br>");	
							$.proxy(this.logHit("walk", batterNumberId), this);
						} else {	
							$("#pitch-results").append( "<div id=\"hit-segment-2\">Hit Type: <span id=\"hit-type\" class=\"" + hitType.replace(" ", "-") + "\">" + hitText + "</span></div><br>");	
							$.proxy(this.logHit("hit", batterNumberId), this);
						}
			
						$.proxy(this.advanceRunners(hitType), this);

					} else {
//*****OUT
						safeOut = "Out";
						this.config.outs += 1;
			
						//Increment the players ingame at-bat count (but not if it's a Walk)
						$("#batters-box-left").children("img:first-child").attr("data-ingame-atbats", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-atbats")) + 1 );

						$("#pitch-results").append( "Safe/Out: <div id=\"safe-out\">" + safeOut + "</div>");
	
						$("#pitch-results").append( '<div class="loader-container"><div class="loading-text" style="font-size: 20px;"><span class="dots"></span></div></div>');				
											
						//Out Type: (SO, Ground Out, Fly Out)
						//SO factor = ( ( pitcher SO / IP ) / 3 ) x 100
						//[estimated 3 at-bats per inning, so divide by 3]
						//Considered changing this to a Strikeout Avg (like the HR avg used for homerun factor)
						//Tried SO9, which is ( (9 x strikeouts) / IP)
						//But the difference is not apparently significant, by ratio, just lower. 
						//Tried multiplying SO9 x 3, to get comparable numbers.
						//( (9 * 274) / 257.2 ) = 9.6 (SO9 Mario Soto) Times 3 = 28.8
						//( (274 / 257.2) / 3 ) * 100 = 35.5 (Current formula Mario Soto)
						//( (9 x 39) / 107 ) = 2.85 (SO9 Ellis Kinder) Times 3 = 8.55
						//( (39 / 107) / 3 ) * 100 = 12.1 (Current formula Ellis Kinder)
				
						var soFactor = ( ( pitcherSo/ seasonIp ) / 3 ) * 100;
						soFactor = soFactor;
						let y = Math.floor((Math.random() * 100) + 1);
						$("#pitch-results").append( "<span id=\"strikeout-segment\">Strikeout Segment: <=" + soFactor.toFixed(0) + " /100<br>Dice Roll.. " + y + "<br></span>");
						
						//Animations: the segment divs (flyout-segment, etc.) have a fadeIn animation attached in the css/animation.css file
						if ( y <= soFactor ) {
							outType = "Strikeout";
							//Update placeholder data field for this batter and pitcher with in-game Strikeout++
							$.proxy(this.logHit("strikeout", batterNumberId), this);
						} else {
							// Using MLB typical seasonal ratio. Of Fly&Ground Outs, 56% are Ground Outs and 43% are Fly Outs
							var flyOutFactor = 56;
							var displayFlyOutFactorPlus1 = flyOutFactor + 1;
							let z = Math.floor((Math.random() * 100) + 1);
							$("#pitch-results").append( "<div id=\"flyout-segment\"><div class=\"loader-container\"><div class=\"loading-text\" style=\"font-size: 20px;\"><span class=\"dots\" style=\"animation-delay: 10s;\"></span></div></div>Fly Out Segment: 1 to " + flyOutFactor.toFixed(0) + "<br>Ground Out Segment: " + displayFlyOutFactorPlus1.toFixed(0) + " to 100<br>Dice Roll... " + z + "<br></div>");
							if( z <= flyOutFactor ) {
								outType = "Fly Out";
								var advanceRunnersSegment = 50;
								let w = Math.floor((Math.random() * 100) + 1);
								$("#pitch-results").append( "<div id=\"double-play-segment\">Advance Runners Segment: <=" + advanceRunnersSegment +  " /100<br>Dice Roll... " + w + "<br></div>");
								if ( this.config.outs < 3 && w <= advanceRunnersSegment && (this.config.firstbaserunner || this.config.secondbaserunner || this.config.thirdbaserunner) ) {
									//Log hit to avance runners
									outType = "Fly Out - <br>Runners Advance!";
									$.proxy(this.advanceRunners("flyout"), this);
								}
							} else {
								outType = "Ground Out";
								//If runner on first, roll for chance of a double play
								var doublePlaySegment = 65;
								let w = Math.floor((Math.random() * 100) + 1);
								$("#pitch-results").append( "<div id=\"double-play-segment\">Double PLay Segment: <=" + doublePlaySegment +  " /100<br>Dice Roll... " + w + "<br></div>");
								if ( this.config.outs < 3 && this.config.firstbaserunner && w <= doublePlaySegment ) {
									//First base runner is out from a double play
									//Send first base runner to the dugout
									if( this.config.toporbottom == 'top' ) {
										$("#away-dugout").prepend( $("#first-base").children('img')[0] );
									} else {
										$("#home-dugout").prepend( $("#first-base").children('img')[0] );
									}
									//Update the inning outs (scoreboard will be updated below)
									this.config.outs += 1;									
									//Update the outType
									outType = "Ground Out - <br>Double Play At Second Base!";
								}
								
							}
						}
						$("#pitch-results").append( "<div id=\"out-type-segment\">Out Type: <span id=\"out-type\" class=\"" + outType + "\">" + outType +"</span><br></div>" );
					
						if( this.config.outs == 3 ){
							batterButtonWaitTime = 7000;
							var myObj = this;
							setTimeout(function() { 
								$.proxy(myObj.endInning(), myObj);
							}, 4000);					
							$('#mini-outs').html("O: 0"); 
											
						} else {
							//update the scoreboard
							//alert(this.config.outs);
							//$('#scoreboard-outs').text( this.config.outs );
							for(var i=1; i <= this.config.outs; i++){
								$("#out-"+i).addClass("dot-on");
							}						
							$('#mini-outs').html("O: " + this.config.outs ); 						
						}
					}
					
					if( hitType != 'Walk' ) {
		//Still a BUG?--when player gets a hit, they go to the base and are not in the batters box any longer, 
			//so the jquery handler fails
			//$("#batters-box-left").children("img:first-child").attr("data-ingame-atbats", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-atbats")) + 1 );
						//console.log( 'At bat incremented for ' + hitType);
					}
					if( !this.config.isfinal ){	
						setTimeout(function() { 	
							//Re-enable Batter up
							$(".batter-up").prop("disabled", false);
							$(".batter-up").removeClass("disabled-link-button");
						}, batterButtonWaitTime);
					} else {
						//alert("this.config.isfinal = " + this.config.isfinal);
					}	
				}, this )
				);				
//**************end of pitch

			}

		};

		var thisGame = game.init({
			awayteam: 'STL'
		});
		game.startInning();
        });
    </script>
    
</head>
<body>

<?php require 'header.php';?>
 
	<h2>Baseball Card Game</h2>

    <div id=stadium-wrapper>
    	
    	<div id="bleachers-scoreboard" style="top: 112px;left: 17px;padding: 10px; position: absolute;border: 2px solid white; background-color: #54796d;color: white;font-weight: 400; font-size: 22px;font-family: Overpass; border-top: 2px solid yellow; border-left: 2px solid yellow; z-index: 600;">
	    		<div style="text-align: center; letter-spacing: 10px;">FENWAY PARK</div>
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
			   		<th></th>
			   		<th colspan="3">OUTS</th>
			   		<th colspan="2"></th>
			   		<th colspan="3">HITS</th>
			   		<th colspan="3">SO</th>
			   		<th colspan="3">WALKS</th>
			   	    </tr>
			   	    <tr class="scoreboard-row"> 
			   	    	<td></td><td><span id="scoreboard-batter">1</span></td>
			   	    	<td></td>
			   	    	<td colspan="4">
			   	    		<div id="scoreboard-outs">
			   	    			<span id="out-1" class="out-dot"></span>
			   	    			<span id="out-2" class="out-dot"></span>
			   	    			<span id="out-3" class="out-dot"></span>
			   	    		</div>
			   	    	</td>
			   	    	<td></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-inning-hits">0</span></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-strikeouts">0</span></td>
			   	    	<td colspan="3" style="text-align: center;"><span id="scoreboard-walks">0</span></td>
			   	    </tr>
			   </table>   
    	</div>
    	<div id="green-monster-1" style="top:112px; left: 539px;position: absolute;width: 381px; height: 125px;background-color: #54796d; border-top: 2px solid yellow; border-bottom: 15px solid tan; z-index: 500;">
		&nbsp;    		
    	</div>
    	<div id="green-monster-2" style="top:112px; left: 920px;position: absolute;width: 317px; height: 125px;background-color: #54796d; border-top: 2px solid yellow; border-right: 2px solid yellow; z-index: 500;">
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
    		<div id="catcher" style="width: 50px; position: absolute; bottom: 2px; left: 555px; z-index: 500;">
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
    		   
    		<div id="home-dugout" style="width: 300px; height: 200px; position: absolute; top: 455px; right: 15px;padding: 3px 7px 3px 7px;background-color: gray; z-index: 350;">
    			<span style="color: white;font-weight: bold;">HOME TEAM</span><br>
    			
    		</div>
    		
    		<div id="away-dugout" style="width: 300px; height: 200px; position: absolute; top: 455px; left: 15px;padding: 3px 7px 3px 7px;background-color: gray; z-index: 350;">
    			<span style="color: white;font-weight: bold;">AWAY TEAM</span><br>
    			
    		</div>
    	
    	<div id="on-deck">
    	
    	</div>
    	
    	<div id="homeplate-wrapper" style="width: 550px; height: 250px; position: absolute; bottom: 80px; left: 350px; z-index: 400; ">
    		<div id="homeplate-circle" style="background-color: tan;width: 200px;height: 200px;border: 5px solid white;border-radius: 50%;position: absolute; bottom: 0px; left: 150px; z-index: 250;">
    		   &nbsp;
    		   <div id="home-plate" style="width: 15px; height: 15px; background-color: white; position: absolute; bottom: 97px; right: 92px;">
    		   	&nbsp;
    		   </div>
    		   <div id="batters-box-left" style="width: 18px; height: 45px;  border: 2px solid white; position: absolute; bottom: 80px; left: 60px; z-index: 390">
    		   
    		   </div>
    		   <div id="batters-box-right" style="width: 18px; height: 45px; border: 2px solid white; position: absolute; bottom: 80px; right: 60px;">
    		   	&nbsp;
    		   </div>
    		</div>
    	</div>
		<div id="behind-homeplate" style="position: absolute;width: 1217px; height: 78px;z-index: -1;border: 1px solid darkgray;background-color: tan;bottom: 0px;left: 0px;z-index: 350;">
			<img id="team-card-away" src="/images/Baseball-Cards/SGC-309-web/1963_Topps_503_Milwaukee_Braves__SGC-Grade-6_Auth-3045059_Front.jpg" style="width: 85px;transform: rotate(90deg);position: absolute; top: -25px; left: 120px;">

			<img id="team-card-home" src="/images/Baseball-Cards/SGC-016-web/1977_Topps_309_Red_Sox__SGC-Grade-8_Auth-5745654_Front.jpg" style="width: 85px;transform: rotate(90deg);position: absolute; top: -25px; right: 120px;">
			
			<div id="play-buttons" style="position: absolute; left: 375px; bottom: 8px;">
				<button class="batter-up link-button disabled-link-button" >Batter Up</button> &nbsp; <button class="send-pitch link-button disabled-link-button" style="font-size: 1px; padding: 0px !important" disabled>Pitch</button>
			</div>
			
			<div id="mini-score-display" style="border: 1px solid gray; font-weight: bold; font-size: 16px; background-color: #54796d; color: white;position: absolute; right: 320px; bottom: 8px;">
				<span id="mini-toporbottom">&#x25B2; &#x25BC;</span> <span id="mini-inning">1st</span> &nbsp; <span id="mini-outs">O: 0</span> &nbsp; <span id="mini-away-score">A:0</span> &nbsp; <span id="mini-home-score">H:0</span>
			</div>
		</div>
		<div id="show-pitch" style="position: absolute;width: 1217px; height: 380px;z-index: -1;border: 0px solid darkgray; top: 30px; left: 0px; z-index: 500; padding: 0px; font-family: monospace; font-size: 16px; display: none;">
			<div style="width: 325px; background-color: white; text-align: center;"><span style="font-weight: bold;">Matchup</span></div>
			<div id="pitch-cards" style="float: left; background-color: white;">
				<div id="pitch-pitcher" style="float: left; padding: 0px 25px 0px 0px;">
					<div id="pitch-pitcher-img" style=""></div>
					<div id="pitch-pitcher-name" style="font-weight: bold; text-align: center;"></div>
					<UL id="pitch-pitcher-data" style=""></UL>
				</div>
				<div id="pitch-batter" style="float: right; font-family: monospace; background-color: white;">
					<div id="pitch-batter-img" style=""></div>
					<div id="pitch-batter-name" style="font-weight: bold; text-align: center;"></div>
					<UL id="pitch-batter-data" style=""></UL>
				</div>
			</div>
			
			<div id="pitch-results" style="float: right; width: 270px; height: 350px; padding: 8px;border: 1px dashed gray; background-color: white;">
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
		</div>
    	</div>
	<div id="preload-card-backs" style="display: none;">
		
	</div>
    </div>
    
 
 
    <div id="status-msg" style="display: none; position: absolute; top: 550px; left: 450px;background-color: white;z-index: 800;">
    	<div id="status-msg-text" style="background-color: #0C2340; color: green;font-size: 35px;font-weight: bold;width: 350px;height: 320px;text-align: center; z-index: 800;">
    		<div class="ring">FINAL
			<span></span>
		</div>
    		<button id="status-button" class="link-button" style="background-color: #54796d; font-size: 18px; position: absolute; bottom: 10px; left: 50px; width: 250px; font-family:sans-serif; z-index: 800;">START A NEW GAME</button>
    		  
    	</div>
    </div>
    
    <?php
    	
        // Database connection
    	$host = 'p3nlmysql39plsk.secureserver.net';
    	$db   = 'ph21100054196_';
    	$user = 'collector';
    	$pass = 'Piltocat22';
    	$port = "3306";
    	$charset = 'utf8mb4';
    
    	$options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,\PDO::ATTR_EMULATE_PREPARES => false,];
    
    	$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    	// $pdo = new \PDO($dsn, $user, $pass, $options);
    	$game_teams = [];
        try {
                $conn = new PDO($dsn, $user, $pass);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $game_teams = [];
                if( isset($_GET['game_season_id']) ) {
                	$game_season_id = $_GET['game_season_id'];
                	$game_season_id2 = $game_season_id;
	//Load teams Query    
			$stmt = $conn->prepare("
    			SELECT distinct team_name, game_team_id, wins, losses, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(runs_for) as runs, SUM(runs_against) as runs_against FROM
			(
			SELECT gt.team_name, gt.game_team_id, gt.wins, gt.losses, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(total_runs_home) as runs_for, SUM(total_runs_away) as runs_against
			FROM game_team gt, game g, game_season_team gst
			WHERE gt.game_team_id = g.home_team_id
			AND gst.game_season_id = ?
			    GROUP BY gt.team_name, gt.wins, gt.losses
			UNION ALL
			SELECT gt.team_name, gt.game_team_id, gt.wins, gt.losses, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id, SUM(total_runs_away) as runs_for, SUM(total_runs_home) as runs_against
			FROM game_team gt, game g, game_season_team gst
			WHERE gt.game_team_id = g.away_team_id
			AND gst.game_season_id = ?
			    GROUP BY gt.team_name, gt.wins, gt.losses
			) t1
			GROUP BY team_name, game_team_id, wins, losses, pitcher_card_id, catcher_card_id, first_base_card_id,second_base_card_id, short_stop_card_id, third_base_card_id, left_field_card_id, center_field_card_id, right_field_card_id
			ORDER BY 3 DESC, 4 ASC, 14 DESC, 15 ASC ");

			$stmt->execute([$game_season_id, $game_season_id2]);
			$game_teams = $stmt->fetchAll();
	    	}
// Fetch all game seasons
		      $stmt10 = $conn->query('SELECT * FROM game_season ORDER by 1');
		      $seasons = $stmt10->fetchAll();
	
            } catch (\PDOException $e) {
	        echo "Connection failed: " . $e->getMessage();
	    }
	    $conn = null;
         
    ?>
    
    <div id="new-game-msg" style="display: block; position: absolute; top: 370px; left: 380px;background-color: white; height: 500px; z-index: 800;">
    	<div id="team_select-wrapper" style="background-color: white; color: green;font-size: 22px;font-weight: bold;width: 400px;height: 320px;text-align: center; z-index: 800;">
  
		<div id="loading-logo" style="display: block; position: absolute; top: 0px; left: 0px;background-color: white; height: 500px; z-index: 800;">		
			<div class="circle-container" >
				<div class="circle" style="background-image: url(/images/Shad-Island-Logo-6-BW.png); background-repeat: no-repeat; background-size: 400px 400px;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave _100" style="opacity: 55%;"></div>
				<div class="wave-below _100" style="opacity: 90%; clip-path: polygon(0% 110%, 0% 125px, 110% 125px, 110% 110%);"></div>
				<div class="desc _100">
				</div>
			</div>
   	    	
			<div id="team-select" class="fadeIn-animation" style="z-index: 1100; background: transparent; position: relative; top: -480px;">
			<form id="team-select-form">	
   	    	<?php
   	    		if( !isset($_GET['game_season_id']) ) {
   	    	?>
   	    				<label for="season-select" style="font-size: 30px; color: #0C2340; text-shadow: white 1px 3px 3px">SEASON</label><br/>
   	    				<select id="season-select" name="game_season_id" class="link-button" style="background-color: #54796d; font-size: 18px; border-radius: 50px 20px;" required>
					     	<?php foreach ($seasons as $season): ?>
						 	<option value="<?php echo htmlspecialchars($season['game_season_id']); ?>">
								<?php echo htmlspecialchars($season['season_name']); ?>
						 	</option>
						<?php endforeach; ?>
					 </select>
					 <button id="season-select-button"  type="button" style="border-radius: 50px 20px; font-size: 18px;" class="link-button">Select Season</button>
		<?php
			} else {
		?>	
					<span id="selected-season" style="font-size: 38px; color: #245ca2; text-shadow: 6px 6px 8px #FFF, -6px -6px 8px #FFF, -6px 6px 8px #FFF, 6px -6px 8px #FFF;">
					<?php 
						foreach ($seasons as $season): 
							if( $season['game_season_id'] == $_GET['game_season_id'] ) {
								echo 'Season: ' . $season['season_name']; 
							}
						endforeach;
					?>
					</span>
					 
					<div style="margin-top: 15px;">
						&nbsp;
					</div>	
		
					
		
					 				
					 <label for="home-team-select" style="font-size: 30px; color: #0C2340; text-shadow: white 1px 3px 3px">HOME TEAM</label><br/>
					 <select id="home-team-select" name="home_team_id" class="link-button" style="background-color: #54796d; font-size: 18px; border-radius: 50px 20px;" required>
					     <?php foreach ($game_teams as $team): ?>
						 <option value="<?php echo htmlspecialchars($team['game_team_id']); ?>">
							<?php echo htmlspecialchars($team['team_name']); ?>
						 </option>
					     <?php endforeach; ?>
					 </select>

					 <span id="selected-home-team" style="font-size: 38px; color: #245ca2; text-shadow: 6px 6px 8px #FFF, -6px -6px 8px #FFF, -6px 6px 8px #FFF, 6px -6px 8px #FFF;"></span>


					<button id="home-team-select-button"  type="button" style="border-radius: 50px 20px; font-size: 18px;" class="link-button">Select Team</button>
					<div style="margin-top: 15px;">
						&nbsp;
					</div>
					 <label for="away-team-select" style="font-size: 30px; color: #54796d; text-shadow: 1px 3px 3px #FFF, -1px -3px 3px #FFF;">AWAY TEAM</label><br/>

		<?php
			}
		?>
					 <select id="away-team-select" name="away_team_id"  class="link-button" style="display: none; background-color: #54796d; font-size: 18px; margin-top: 10px; border-radius: 50px 20px;" required>
					     <?php foreach ($game_teams as $team2): ?>
						 <option value="<?php echo htmlspecialchars($team2['game_team_id']); ?>">
							<?php echo htmlspecialchars($team2['team_name']); ?>
						 </option>
					     <?php endforeach; ?>
					 </select>

					 <?php foreach ($game_teams as $team): ?>
						<input type="hidden" id="game-team-<?php echo htmlspecialchars($team['game_team_id']); ?>"
						 data-pitcher-id="<?php echo htmlspecialchars($team['pitcher_card_id']); ?>" data-catcher-id="<?php echo htmlspecialchars($team['catcher_card_id']); ?>" data-first-base-id="<?php echo htmlspecialchars($team['first_base_card_id']); ?>" data-second-base-id="<?php echo htmlspecialchars($team['second_base_card_id']); ?>" data-short-stop-id="<?php echo htmlspecialchars($team['short_stop_card_id']); ?>" data-third-base-id="<?php echo htmlspecialchars($team['third_base_card_id']); ?>" data-left-field-id="<?php echo htmlspecialchars($team['left_field_card_id']); ?>" data-center-field-id="<?php echo htmlspecialchars($team['center_field_card_id']); ?>" data-right-field-id="<?php echo htmlspecialchars($team['right_field_card_id']); ?>"  data-wins="<?php echo htmlspecialchars($team['wins']); ?>"  data-losses="<?php echo htmlspecialchars($team['losses']); ?>"  data-runs="<?php echo htmlspecialchars($team['runs']); ?>"  >
					 <?php endforeach; ?>
					 <br/>
					 <br/>
					<input id="team-select-submit"  style="display: none; font-size: 18px; border-radius: 50px 20px;" class="link-button" type="submit" value="Play Ball!">  			
			</form>	
			</div>
    

    			<div style="position: absolute; bottom: 10px; left: 160px;">
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
        	    
    	    	
        		$stmt = $conn->prepare(" 
				SELECT c.player_name, c.card_id, gps.at_bats, gps.hits, gps.home_runs, gps.strikeouts, gps.walks, gt.team_name, FORMAT(gps.hits/(gps.at_bats), 3) as batting_avg 
				FROM game_player_stats gps, card c, game_team gt 
				WHERE gps.card_id = c.card_id 
				AND gps.hits > 0 
				AND gps.game_team_id = gt.game_team_id
				GROUP BY c.player_name, c.card_id, gps.at_bats, gps.hits, gps.home_runs, gps.strikeouts, gps.walks, gt.team_name
				ORDER BY 9 DESC, 3 DESC
				LIMIT 10 OFFSET 0
			");

        		$stmt->execute();
        		$myCtr = 0;
    		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    			$league_batting_leaders[$myCtr]['card_id'] = $row['card_id'];
    			$league_batting_leaders[$myCtr]['player_name'] = $row['player_name'];
    			$league_batting_leaders[$myCtr]['at_bats'] = $row['at_bats'];
    			$league_batting_leaders[$myCtr]['team_name'] = $row['team_name'];
    			$league_batting_leaders[$myCtr]['batting_avg'] = $row['batting_avg']; //Calculated in SQL SELECT
    			$league_batting_leaders[$myCtr]['hits'] = $row['hits'];
    			$league_batting_leaders[$myCtr]['home_runs'] = $row['home_runs'];
    			$league_batting_leaders[$myCtr]['strikeouts'] = $row['strikeouts'];
    			$league_batting_leaders[$myCtr]['walks'] = $row['walks'];
    			$myCtr++;
    		}
    		
                } catch (\PDOException $e) {
    	        echo "Connection failed: " . $e->getMessage();
    	    }
    	    $conn = null;

//Batting Leaders Query - Home Runs    	    
            $league_home_run_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
    	    	
        		$stmt3 = $conn->prepare(" 
				SELECT c.player_name, gps.card_id, gps.home_runs, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name, FORMAT(gps.hits/(gps.at_bats), 3) as batting_avg 
				FROM game_player_stats gps, card c, game_team gt 
				WHERE gps.card_id = c.card_id 
				AND gps.home_runs > 0 
				AND gps.game_team_id = gt.game_team_id
				GROUP BY c.player_name, gps.card_id, gps.home_runs, gps.hits, gps.walks, gps.strikeouts, gps.at_bats, gt.team_name
				ORDER BY home_runs DESC, at_bats ASC, 9 DESC
				LIMIT 10 OFFSET 0
			");

        		$stmt3->execute();
        		$myCtr = 0;
    		while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    			$league_home_run_leaders[$myCtr]['card_id'] = $row['card_id'];
    			$league_home_run_leaders[$myCtr]['player_name'] = $row['player_name'];
    			$league_home_run_leaders[$myCtr]['at_bats'] = $row['at_bats'];
    			$league_home_run_leaders[$myCtr]['team_name'] = $row['team_name'];
    			$league_home_run_leaders[$myCtr]['batting_avg'] = $row['batting_avg']; //Calculated in SQL SELECT
    			$league_home_run_leaders[$myCtr]['hits'] = $row['hits'];
    			$league_home_run_leaders[$myCtr]['home_runs'] = $row['home_runs'];
    			$league_home_run_leaders[$myCtr]['strikeouts'] = $row['strikeouts'];
    			$league_home_run_leaders[$myCtr]['walks'] = $row['walks'];
    			$myCtr++;
    		}
    		
                } catch (\PDOException $e) {
    	        echo "Connection failed: " . $e->getMessage();
    	    }
    	    $conn = null;



//Pitching Leaders Query - ERA
            $league_pitching_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
    	    
    	    		//ERA is: 9 x earned runs / innings pitched
        		$stmt = $conn->prepare(" 								
				SELECT c.player_name, c.card_id, gps.innings_pitched, gps.wins, gps.losses, gps.strikeouts_against, gps.walks_against, gt.team_name, FORMAT( (gps.runs_against * 9) / gps.innings_pitched, 3) as era
				FROM game_player_stats gps, card c, game_team gt 
				WHERE gps.card_id = c.card_id 
				AND gps.innings_pitched >= (gt.wins + gt.losses)
				AND gps.game_team_id = gt.game_team_id
				GROUP BY c.player_name, c.card_id, gps.innings_pitched, gps.wins, gps.losses, gps.strikeouts_against, gps.walks_against, gt.team_name
				ORDER BY 9 ASC, 3 DESC, 4 DESC
				LIMIT 10 OFFSET 0
			");
        		$stmt->execute();
        		$myCtr = 0;
    		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    			$league_pitching_leaders[$myCtr]['card_id'] = $row['card_id'];
    			$league_pitching_leaders[$myCtr]['player_name'] = $row['player_name'];
    			$league_pitching_leaders[$myCtr]['team_name'] = $row['team_name'];
    			$league_pitching_leaders[$myCtr]['innings_pitched'] = $row['innings_pitched'];
    			$league_pitching_leaders[$myCtr]['wins'] = $row['wins']; 
    			$league_pitching_leaders[$myCtr]['losses'] = $row['losses'];
    			$league_pitching_leaders[$myCtr]['strikeouts_against'] = $row['strikeouts_against'];
    			$league_pitching_leaders[$myCtr]['walks_against'] = $row['walks_against'];
    			$league_pitching_leaders[$myCtr]['era'] = $row['era'];
    			$myCtr++;
    		}
    		
                } catch (\PDOException $e) {
    	        echo "Connection failed: " . $e->getMessage();
    	    }
    	    $conn = null;
          
//Pitching Leaders Query - Strikeouts
            $league_strikeout_leaders = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
        		$stmt4 = $conn->prepare(" 
				SELECT c.player_name, gps.*, gt.team_name, FORMAT( (gps.runs_against * 9) / gps.innings_pitched, 3) as era
				FROM game_player_stats gps, card c, game_team gt 
				WHERE gps.card_id = c.card_id 
				AND gps.strikeouts_against > 0 
				AND gps.innings_pitched > 1
				AND gps.game_team_id = gt.game_team_id
				ORDER BY gps.strikeouts_against DESC, gps.innings_pitched DESC
				LIMIT 10 OFFSET 0
			");
			
        		$stmt4->execute();
        		$myCtr = 0;
    		while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
    			$league_strikeout_leaders[$myCtr]['card_id'] = $row['card_id'];
    			$league_strikeout_leaders[$myCtr]['player_name'] = $row['player_name'];
    			$league_strikeout_leaders[$myCtr]['team_name'] = $row['team_name'];
    			$league_strikeout_leaders[$myCtr]['innings_pitched'] = $row['innings_pitched'];
    			$league_strikeout_leaders[$myCtr]['wins'] = $row['wins']; 
    			$league_strikeout_leaders[$myCtr]['losses'] = $row['losses'];
    			$league_strikeout_leaders[$myCtr]['strikeouts_against'] = $row['strikeouts_against'];
    			$league_strikeout_leaders[$myCtr]['walks_against'] = $row['walks_against'];
    			$league_strikeout_leaders[$myCtr]['era'] = $row['era'];
    			$myCtr++;
    		}
    		
                } catch (\PDOException $e) {
    	        echo "Connection failed: " . $e->getMessage();
    	    }
    	    $conn = null;  
    	    
    	
//Game matchup table Query
            $game_team_matchups = [];
            try {
                    $conn = new PDO($dsn, $user, $pass);
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	    
        		$stmt5 = $conn->prepare(" 
				SELECT gt.game_team_id, gt.team_name, g.away_team_id
				FROM game g, game_team gt 
				WHERE gt.game_team_id = g.home_team_id
				ORDER BY 1 ASC
				
			");
			
        		$stmt5->execute();
        		$myCtr = 0;
    		while ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
    			$game_team_matchups[$myCtr]['game_team_id'] = $row['game_team_id'];
    			$game_team_matchups[$myCtr]['team_name'] = $row['team_name'];
    			$game_team_matchups[$myCtr]['away_team_id'] = $row['away_team_id'];
    			$myCtr++;
    		}
    		
                } catch (\PDOException $e) {
    	        echo "Connection failed: " . $e->getMessage();
    	    }
    	    $conn = null;              
    ?>
    <div id="box-stats" style="position: absolute; top:130px; left: 1300px;" >
    <span style="font-weight: bold;">GAMES PLAYED</span>
    	<table id="season-standings-table" >
    		<tr>
    			<th colspan="7" style="text-align: center;">Away</th>
    		</td>
    		<tr>
    			<th></th><th>BOSTON</th><th>STL</th><th>MIL</th><th>CIN</th><th>LAD</th><th>HOU</th>
    		</tr>
    	<?php 
        	$prevTeamName = "BOSTON";
    		$prevTeamId = 0;
    		$oneCtr = 0;
    		$twoCtr = 0;
		$threeCtr = 0;
		$fourCtr = 0;
		$fiveCtr = 0;
		$sixCtr = 0;
        	
    		foreach ($game_team_matchups as $matchup): 
    			$idCtr = 0;
    			if( $matchup['team_name'] != $prevTeamName ) {
    				if( $prevTeamId==1 ) {$displayoneCtr = ' X ';
    				} else { $displayoneCtr = $oneCtr; }
    				if( $prevTeamId==2 ) {$displaytwoCtr = ' X ';
    				} else { $displaytwoCtr = $twoCtr; }
    				if( $prevTeamId==3 ) {$displaythreeCtr = ' X ';
    				} else { $displaythreeCtr = $threeCtr; }
    				if( $prevTeamId==4 ) {$displayfourCtr = ' X ';
    				} else { $displayfourCtr = $fourCtr; }
    				if( $prevTeamId==5 ) {$displayfiveCtr = ' X ';
    				} else { $displayfiveCtr = $fiveCtr; }
    				if( $prevTeamId==6 ) {$displaysixCtr = ' X ';
    				} else { $displaysixCtr = $sixCtr; }
    				echo '<tr><td style="font-weight: bold;">' . $prevTeamName . '</td>';
    				echo '<td>' . $displayoneCtr . '</td>';
    				echo '<td>' . $displaytwoCtr . '</td>';
    				echo '<td>' . $displaythreeCtr . '</td>';
    				echo '<td>' . $displayfourCtr . '</td>';
    				echo '<td>' . $displayfiveCtr . '</td>';
    				echo '<td>' . $displaysixCtr . '</td></tr>';
				$idCtr = 0;
				$oneCtr = 0;
				$twoCtr = 0;
				$threeCtr = 0;
				$fourCtr = 0;
				$fiveCtr = 0;
				$sixCtr = 0;
    			}
			if( $matchup['away_team_id'] == 1 ) {
    		      		$oneCtr += 1;
    				
    		 	} else if( $matchup['away_team_id'] == 2 ) { 
    		      		$twoCtr += 1;
    				
    		 	} else if( $matchup['away_team_id'] == 3 ) { 
    		      		$threeCtr += 1;
    				
    		 	} else if( $matchup['away_team_id'] == 4 ) { 
    		      		$fourCtr += 1;
    				
    		 	} else if( $matchup['away_team_id'] == 5 ) { 
    		      		$fiveCtr += 1;
    				
    		 	} else if( $matchup['away_team_id'] == 6 ) { 
    		      		$sixCtr += 1;
    				
    		 	} 
    			$prevTeamId = $matchup['game_team_id'];	
    			$prevTeamName = $matchup['team_name'];
    	 endforeach; 
    		 	
    		 	//if( $matchup['game_team_id'] == 6 ) {
    				if( $prevTeamId==1 ) {$displayoneCtr = ' X ';
    				} else { $displayoneCtr = $oneCtr; }
    				if( $prevTeamId==2 ) {$displaytwoCtr = ' X ';
    				} else { $displaytwoCtr = $twoCtr; }
    				if( $prevTeamId==3 ) {$displaythreeCtr = ' X ';
    				} else { $displaythreeCtr = $threeCtr; }
    				if( $prevTeamId==4 ) {$displayfourCtr = ' X ';
    				} else { $displayfourCtr = $fourCtr; }
    				if( $prevTeamId==5 ) {$displayfiveCtr = ' X ';
    				} else { $displayfiveCtr = $fiveCtr; }
    				if( $prevTeamId==6 ) {$displaysixCtr = ' X ';
    				} else { $displaysixCtr = $sixCtr; }
    				echo '<tr><td  style="font-weight: bold;">' . $prevTeamName . '</td>';
    				echo '<td>' . $displayoneCtr . '</td>';
    				echo '<td>' . $displaytwoCtr . '</td>';
    				echo '<td>' . $displaythreeCtr . '</td>';
    				echo '<td>' . $displayfourCtr . '</td>';
    				echo '<td>' . $displayfiveCtr . '</td>';
    				echo '<td>' . $displaysixCtr . '</td></tr>';
    			//}
    		?>
    	</table>
    	<br>
    <span style="font-weight: bold;">SEASON STANDINGS</span>
    	<table id="season-standings-table" >
    		<tr>
    			<th></th><th>Wins</th><th>Losses</th><th>Runs For</th><th>Runs Against</th><th>Pct.</th>
    		</tr>
    	<?php foreach ($game_teams as $team): ?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($team['team_name']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($team['wins']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($team['losses']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($team['runs']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($team['runs_against']); ?>
    			</td>
    			<td><?php echo htmlspecialchars( number_format($team['wins'] / ($team['wins']+$team['losses']), 3) ); ?>
    			</td>
    		</tr>
    		
    		
    	<?php endforeach; ?>
    	</table>
    	<br>
    <span style="font-weight: bold;">LEAGUE LEADERS</span>
    	<table id="box-stats-table" >
    		<tr>
    			<th colspan="8">&nbsp;ERA Leaders&nbsp;</th>
    		</tr>
    		<tr>
    			<th>&nbsp;Pitcher&nbsp;Name&nbsp;</th><th>ERA</th><th>IP</th><th>Wins</th><th>Losses</th><th>SO</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_pitching_leaders as $pitching_leader): ?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($pitching_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($pitching_leader['era']); ?></span>
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
    		<tr>
    			<th colspan="8">&nbsp;Strikeout Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Pitcher&nbsp;Name&nbsp;</th><th>Strikeouts</th><th>IP</th><th>ERA</th><th>Wins</th><th>Losses</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_strikeout_leaders as $strikeout_leader): ?>
    		
    		<tr>
    			<td><?php echo htmlspecialchars($strikeout_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($strikeout_leader['strikeouts_against']); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['innings_pitched']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['era']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['wins']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['losses']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['walks_against']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($strikeout_leader['team_name']); ?>
    			</td>
    		</tr>
    		
    		<?php endforeach; ?>
    		
    		<tr>
    			<td colspan="8" style="background-color: gray;">&nbsp;</td>
    		</tr>
    		<tr>
    			<th colspan="8">&nbsp;Batting Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Batter&nbsp;Name&nbsp;</th><th>AVG</th><th>At Bats</th><th>Hits</th><th>HR</th><th>SO</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_batting_leaders as $batting_leader): ?>
    		<tr>
    			<td ><?php echo htmlspecialchars($batting_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($batting_leader['batting_avg']); ?></span>
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
    		<tr>
    			<th colspan="8">&nbsp;Home Run Leaders&nbsp;</th>
    		</tr>
    		<tr >
    			<th>&nbsp;Batter&nbsp;Name&nbsp;</th><th>HR</th><th>At Bats</th><th>AVG</th><th>Hits</th><th>SO</th><th>BB</th><th>Team</th>
    		</tr>
    	
    		<?php foreach ($league_home_run_leaders as $home_run_leader): ?>
    		<tr>
    			<td ><?php echo htmlspecialchars($home_run_leader['player_name']); ?>
    			</td>
    			<td><span style="font-style: italic;"><?php echo htmlspecialchars($home_run_leader['home_runs']); ?></span>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['at_bats']); ?>
    			</td>
    			<td><?php echo htmlspecialchars($home_run_leader['batting_avg']); ?>
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
    	
    	</table>

    	
    </div>
    
    
      <?php require 'footer.php';?>
      
</body>
</html>
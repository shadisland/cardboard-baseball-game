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
				homepitcherrelieved: 0,
				awaypitcherrelieved: 0,
				homestartergetswinloss: true,
				awaystartergetswinloss: true,
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
				console.log("Hit/Sac BEFORE- hitType=" + hitType + " firstbaserunner=" + this.config.firstbaserunner + " secondbaserunner=" + this.config.secondbaserunner + " thirdbaserunner=" + this.config.thirdbaserunner);

		 		if( hitType =='stolesecond' ) {
		 			//Advance the firstbase runner to secondbase
		 			//This would not occur if there was already a runner on second
		 			//If there's a runner on third, he stays on third
					advanceToSecond =  true;
					if( this.config.thirdbaserunner ) {
						//set advanceToThird (even though he was already on third)
						advanceToThird =  true;
					}
					$("#second-base").prepend( $("#first-base").children('img')[0] );
					this.config.firstbaserunner = false; //stole second, so firstbase is now empty
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
				} else if( hitType =='outstealingsecond' ) {
		 			//Remove the firstbase runner
		 			//This would not occur if there was already a runner on second
		 			//If there's a runner on third, he stays on third
					advanceToSecond =  false;
					//send him to the dugout
					if( this.config.toporbottom == 'top' ) {
						$("#away-dugout").prepend( $("#first-base").children('img')[0] );
					} else {
						$("#home-dugout").prepend( $("#first-base").children('img')[0] );
					}
					if( this.config.thirdbaserunner ) {
						//set advanceToThird (even though he was already on third)
						advanceToThird =  true;
					}

					this.config.firstbaserunner = false; //failed to steal second, so firstbase is now empty
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
				} else if( hitType == 'groundout' ) {
					//**If ground out was sent to this function (after dice roll), there was a runner on second or third who
					//were not forced, so they advance while forced runners are thrown out
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
						advanceToThird = true;
						$("#third-base").prepend( $("#second-base").children('img')[0] );
					}
					this.config.firstbaserunner = false; //batter is out and all runners advanced, so firstbase is now empty
					this.config.secondbaserunner = false;
					this.config.thirdbaserunner = advanceToThird;

				} else if( hitType == 'flyout' ) {
					//If fly out was sent to this function (after dice roll), it's a sacrifice fly and we advance runners one base.
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
						advanceToThird = true;
						$("#third-base").prepend( $("#second-base").children('img')[0] );
					}
					if( this.config.firstbaserunner ) {
						advanceToSecond =  true;
						$("#second-base").prepend( $("#first-base").children('img')[0] );

					}
					this.config.firstbaserunner = false; //batter is out and all runners advanced, so firstbase is now empty
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;

				} else if( hitType == 'Walk' ) {
					console.log("WALK");
					if( this.config.thirdbaserunner ) {
						//Only advance if there is a firstbaserunner, and a secondbaserunner
						if( this.config.firstbaserunner && this.config.secondbaserunner) {
							console.log("advanceToHome is set");
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#third-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#third-base").children('img')[0] );
							}
						} else {
							//set advanceToThird (even though he was already on third)
							console.log("Set advanceToThird to true - even though he was already on third");
							advanceToThird =  true;
						}

					}
					if( this.config.secondbaserunner ) {
						//Only advance if there is a firstbaserunner
						if( this.config.firstbaserunner ) {
							advanceToThird =  true;
							$("#third-base").prepend( $("#second-base").children('img')[0] );
						} else {
							//set advanceToSecond (even though he was already on second)
							advanceToSecond =  true;
						}
					}
					if( this.config.firstbaserunner ) {
						advanceToSecond =  true;
						$("#second-base").prepend( $("#first-base").children('img')[0] );
					}
					//move batter's card to first base
					$("#first-base").append( $("#batters-box-left").children('img')[0] );

					this.config.firstbaserunner =  true;
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
					this.config.walks += 1;
					$('#scoreboard-walks').text( this.config.walks );
					if( this.config.toporbottom == 'top' ) {
						this.config.totalwalksaway += 1;
						$('#total-bb-away').text( this.config.totalwalksaway );
					} else {
						this.config.totalwalkshome += 1;
						$('#total-bb-home').text( this.config.totalwalkshome );
					}
				} else if( hitType == 'Single' || hitType == 'flyerror' || hitType == 'grounderror' ) {
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
						if( this.config.outs == 2 ) {
							advanceToHome += 1;
							//send him to the dugout
							if( this.config.toporbottom == 'top' ) {
								$("#away-dugout").prepend( $("#second-base").children('img')[0] );
							} else {
								$("#home-dugout").prepend( $("#second-base").children('img')[0] );
							}

						} else {
							advanceToThird =  true;
							$("#third-base").prepend( $("#second-base").children('img')[0] );
						}
					}
					if( this.config.firstbaserunner ) {
						if( this.config.outs == 2 ) {
							advanceToThird =  true;
							$("#third-base").prepend( $("#first-base").children('img')[0] );
						} else {
							advanceToSecond =  true;
							$("#second-base").prepend( $("#first-base").children('img')[0] );
						}
					}

					//move batter's card to first base
					$("#first-base").append( $("#batters-box-left").children('img')[0] );

					this.config.firstbaserunner =  true;
					this.config.secondbaserunner = advanceToSecond;
					this.config.thirdbaserunner = advanceToThird;
					//this.config.hits += 1;
					//$('#scoreboard-hits').text( this.config.hits );
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
					//move batter's card to second base
					$("#second-base").append( $("#batters-box-left").children('img')[0] );

					this.config.firstbaserunner = false;
					this.config.secondbaserunner = true;
					this.config.thirdbaserunner = advanceToThird;
					//this.config.hits += 1;
					//$('#scoreboard-hits').text( this.config.hits );
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
					//move batter's card to third base
					 $("#third-base").append( $("#batters-box-left").children('img')[0] );

					this.config.firstbaserunner = false;
					this.config.secondbaserunner =  false;
					this.config.thirdbaserunner =  true;
					//this.config.hits += 1;
					//$('#scoreboard-hits').text( this.config.hits );
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
				if( hitType == 'flyerror' || hitType == 'grounderror' ) {
					this.config.errors += 1;
					//Update inning errors on main scoreboard
					$('#scoreboard-errors').text( this.config.errors );

				} else if( hitType != 'flyout' ) {
					this.config.hits += 1;
					$('#scoreboard-hits').text( this.config.hits );
					console.log("hitType = " + hitType );
				}
				//increase runs with advanceToHome value
				this.config.runs += advanceToHome;
				var teamInTheLead = "tie";
				var newTeamInTheLead = "tie";
				if( this.config.totalrunshome > this.config.totalrunsaway) {
					teamInTheLead = "home";
				} else if ( this.config.totalrunsaway > this.config.totalrunshome) {
					teamInTheLead = "away";
				}
				if( this.config.toporbottom == 'top' ) {
					if( hitType == 'flyerror' || hitType == 'grounderror' ) {
						this.config.totalerrorshome += 1;
						$('#total-errors-home').text( this.config.totalerrorshome );
					} else if( hitType != 'flyout' && hitType != 'Walk' ) {
						this.config.totalhitsaway += 1;
						$('#total-hits-away').text( this.config.totalhitsaway );
					}
					this.config.totalrunsaway += advanceToHome;
					$('#inning-'+ this.config.inning + '-away').text(this.config.runs);
					$('#total-runs-away').text(this.config.totalrunsaway);
					//Update runs against home pitcher or reliever
					if( this.config.homepitcherrelieved > 0 ) {
						$('#home-reliever').attr("data-ingame-runs-against", parseInt($('#home-reliever').attr("data-ingame-runs-against")) + advanceToHome);
					} else {
						$('#home-pitcher').attr("data-ingame-runs-against", parseInt($('#home-pitcher').attr("data-ingame-runs-against")) + advanceToHome);
					}

				} else {
					if( hitType == 'flyerror' || hitType == 'grounderror' ) {
						this.config.totalerrorsaway += 1;
						$('#total-errors-away').text( this.config.totalerrorsaway );
					} else if( hitType != 'flyout' && hitType != 'Walk' ) {
						this.config.totalhitshome += 1;
						$('#total-hits-home').text( this.config.totalhitshome );
					}
					this.config.totalrunshome += advanceToHome;
					$('#inning-'+ this.config.inning + '-home').text(this.config.runs);
					$('#total-runs-home').text(this.config.totalrunshome);
					//Update runs against away pitcher or reliever
					if( this.config.awaypitcherrelieved > 0 ) {
						$('#away-reliever').attr("data-ingame-runs-against", parseInt($('#away-reliever').attr("data-ingame-runs-against")) + advanceToHome);
					} else {
						$('#away-pitcher').attr("data-ingame-runs-against", parseInt($('#away-pitcher').attr("data-ingame-runs-against")) + advanceToHome);
					}
				}

				//***TODO - decide whether win/loss goes to the starter or the reliever
				//When updating score after a run, check whether winning team changed
				if( this.config.totalrunshome > this.config.totalrunsaway) {
					newTeamInTheLead = "home";
				} else if ( this.config.totalrunsaway > this.config.totalrunshome) {
					newTeamInTheLead = "away";
				} else {
					newTeamInTheLead = "tie";
				}
				if( teamInTheLead != newTeamInTheLead ) {
					if(this.config.homepitcherrelieved > 0) {
						//if home pitcher is the reliever, change homestartergetswinloss = false
						this.config.homestartergetswinloss = false;
					}
					if(this.config.awaypitcherrelieved > 0) {
						//if away pitcher is the reliever, change awaystartergetswinloss = false
						this.config.awaystartergetswinloss = false;
					}

				}
				console.log("homestartergetswinloss = " + this.config.homestartergetswinloss );
				console.log("awaystartergetswinloss = " + this.config.awaystartergetswinloss );
				$("#mini-home-score").text( $("#home-team-name").text() + ":" + this.config.totalrunshome);
				$("#mini-away-score").text( $("#away-team-name").text() + ":" + this.config.totalrunsaway);

				console.log("Hit/Sac Fly AFTER-  hitType=" + hitType + " firstbaserunner=" + this.config.firstbaserunner + " secondbaserunner=" + this.config.secondbaserunner + " thirdbaserunner=" + this.config.thirdbaserunner);
			},

//*****EndGame
			endGame:function(){
				//Save results to the database

				this.config.isfinal = true;

				//disable buttons and announce "Final"

				console.log("Enabled batter-up button-6");
				$(".batter-up").prop("disabled", true);
				$(".batter-up").addClass("disabled-link-button");
				$(".relieve-pitcher").prop("disabled", true);
				$(".relieve-pitcher").addClass("disabled-link-button");
				$(".send-pitch").prop("disabled", true);
				$(".send-pitch").addClass("disabled-link-button");

				//Display Final score
				var homeTeamName = $("#home-team-name").text();
				var awayTeamName = $("#away-team-name").text();
                		//Update game_team wins/losses/runs for both teams
				var homeTeamRuns = $("#total-runs-home").text();
				var awayTeamRuns = $("#total-runs-away").text();
				var homeTeamId = $("#home-team-id").attr("value");
				var awayTeamId = $("#away-team-id").attr("value");
				var homeLogoUrl = $("#game-team-" + homeTeamId).attr("data-team-logo-url");
				var awayLogoUrl = $("#game-team-" + awayTeamId).attr("data-team-logo-url");
				var homeLogo = '<img src="' + homeLogoUrl + '" style="max-width: 100px; margin: 10px 0px 0px 0px;">';
				var awayLogo = '<img src="' + awayLogoUrl + '" style="max-width: 100px; margin: 10px 0px 0px 0px;">';
                		$("#status-msg-text").html( "<span style=\"margin-left: 135px;\">FINAL</span><br>" + homeLogo + " " + homeTeamName + " " + homeTeamRuns + "<br>" + awayLogo + " " + awayTeamName + " " + awayTeamRuns + "<br><button id=\"status-button\" class=\"link-button\" style=\"font-size: 18px; position: absolute; bottom: 10px; left: 50px; width: 250px; font-family:sans-serif; z-index: 800;\">START A NEW GAME</button>");
				$('#status-msg').css("display", "block");


				//Update pitchers' win/loss
				console.log('homeTeamRuns = '  + homeTeamRuns + ', awayTeamRuns = ' + awayTeamRuns);

				//the last pitcher to throw a pitch before his team takes the lead for good gets the win.
				if( homeTeamRuns > awayTeamRuns ) {
					if( this.config.homestartergetswinloss ) {
						$('#home-pitcher').attr('data-ingame-wins', '1' );
					} else {
						$('#home-reliever').attr('data-ingame-wins', '1' );
					}
					if( this.config.awaystartergetswinloss ) {
						$('#away-pitcher').attr('data-ingame-losses', '1' );
					} else {
						$('#away-reliever').attr('data-ingame-losses', '1' );
					}
				} else {
					if( this.config.homestartergetswinloss ) {
						$('#home-pitcher').attr('data-ingame-losses', '1');
					} else {
						$('#home-reliever').attr('data-ingame-losses', '1');
					}
					if( this.config.awaystartergetswinloss ) {
						$('#away-pitcher').attr('data-ingame-wins', '1' );
					} else {
						$('#away-reliever').attr('data-ingame-wins', '1' );
					}

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
					playerObj.push( ['stolen-bases', $(this).attr('data-ingame-stolen-bases')] );

					if( $(this).attr('id') == 'home-pitcher' || $(this).attr('id') == 'home-reliever') {
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
					playerObj.push( ['stolen-bases', $(this).attr('data-ingame-stolen-bases')] );

					if( $(this).attr('id') == 'away-pitcher' || $(this).attr('id') == 'away-reliever') {
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
				gameSeasonId = $('#game-season').attr("data-game-season-id");
				playerStats = JSON.stringify(playerStats);
				//console.log(playerStats);
				$.ajax({
					type: "POST",
					url: "update_game_team_stats.php",
					data: { player_stats: playerStats, home_team_id: homeTeamId, away_team_id: awayTeamId, home_team_runs: homeTeamRuns,away_team_runs: awayTeamRuns, game_season_id:  gameSeasonId},
					success: function(response){
					    //alert(response);

					},
					error: function(){
					    if( confirm("Error updating teams.\nPlease check you internet connection.\nRetry?") ) {
					    	//try again
		//****TODO: This doesn't work. Game is not saved correctly. Test and fix it
					    	var myObject = this;
					    	$.proxy(myObject.endGame(), myObject);
					    }
					}
				});
			},
//***Start Inning
			startInning:function(){
				//Put the team card (e.g., Red Sox Team) below the dugouts
				var homeTeamId = $("#home-team-id").attr("value");
				var awayTeamId = $("#away-team-id").attr("value");
				this.config.endOfInning = false;
				var dugout = "";
				var thisTeamId = "" ;
				var pitcherIdName = "pitcher-id";
				$('#scoreboard-batter').text( "" );
				if( this.config.toporbottom == 'top' ) {
					dugout = "home";
					thisTeamId = $("#home-team-id").attr("value");
					$('#mini-toporbottom').html("&#x25B2");
					if( this.config.homepitcherrelieved > 0 ) {
						pitcherIdName = "reliever-id";
					}
				} else {
					dugout = "away";
					thisTeamId = $("#away-team-id").attr("value");
					$('#mini-toporbottom').html("&#x25BC");
					if( this.config.awaypitcherrelieved > 0 ) {
						pitcherIdName = "reliever-id";
					}
				}
				$('#mini-inning').html( this.config.inning + nth(this.config.inning) );
			//**TODO: Ask to relieve the pitcher if it's >= 5th inning and has not been relieved
			if( this.config.inning >= 5 && this.config.homepitcherrelieved == 0 ) {

				//Pitcher is getting fatigued. Relieve him?

				//$("#show-pitch").css("display", "block");
				//$(".send-pitch").prop("disabled", true);
				//$(".send-pitch").addClass("disabled-link-button");
			}
				//Animated transform location
				$("#" + dugout + "-dugout img").addClass("animated-" + dugout);

				//Now put them in positions
				//read the positions from the hidden record for the home team
				setTimeout(function() {
					$("#" + dugout + "-dugout img").removeClass("animated-"  + dugout);
					$("#" + dugout + "-dugout img").each(function(index, value) {
						//console.log(  $("#game-team-" + home_team_id ).attr('data-pitcher-id') );
						if( $(this).attr('data-card-id') == $("#game-team-" + thisTeamId ).data(pitcherIdName) ) {
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
					//Re-enable Batter up
					//$(".batter-up").prop("disabled", false);
					//$(".batter-up").removeClass("disabled-link-button");
				}, 3500);
				//check whether is top or bottom of the inning and then check whether home/away pitcher has been relieved
				if( (this.config.toporbottom == 'top' && this.config.homepitcherrelieved == 0) || (this.config.toporbottom == 'bottom' && this.config.awaypitcherrelieved == 0) ) {
					setTimeout(function() {
						//Re-enable Batter up
						console.log("Enabled batter-up button-1");
						$(".batter-up").prop("disabled", false);
						$(".batter-up").removeClass("disabled-link-button");
						$(".relieve-pitcher").prop("disabled", false);
						$(".relieve-pitcher").removeClass("disabled-link-button");
					}, 7500);
				} else {
					setTimeout(function() {
						//Re-enable Batter up
						console.log("Enabled batter-up button-2");
						$(".batter-up").prop("disabled", false);
						$(".batter-up").removeClass("disabled-link-button");
						$(".relieve-pitcher").prop("disabled", true);
						$(".relieve-pitcher").addClass("disabled-link-button");
					}, 7500);
				}
			},
//****End Inning
			endInning:function(){
				//***TODO: Maybe add a 'Save Game' button--but this creates complicated situations if someone were to
				//update one of the teams after saving and then tries to resume the saved game
				if( this.config.toporbottom == 'top' ) {
					//End of the top of the inning
					//update home team pitcher's innings pitched
					//check whether to update pitcher or reliever
					if( this.config.homepitcherrelieved > 0 ) {
						$('#home-reliever').attr('data-ingame-ip', parseInt($('#home-reliever').attr('data-ingame-ip')) +1 );
					} else {
						$('#home-pitcher').attr('data-ingame-ip', parseInt($('#home-pitcher').attr('data-ingame-ip')) +1 );
					}

					//update the scoreboard
					$('#inning-'+ this.config.inning + '-away').text(this.config.runs);
					//clear the bases
					$("#away-dugout").append( $("#first-base").children("img:first-child") );
					$("#away-dugout").append( $("#second-base").children("img:first-child") );
					$("#away-dugout").append( $("#third-base").children("img:first-child") );

					//Do it again, because sometimes timing of events leaves 2 runners on a base at end of inning
					$("#away-dugout").append( $("#first-base").children("img:first-child") );
					$("#away-dugout").append( $("#second-base").children("img:first-child") );
					$("#away-dugout").append( $("#third-base").children("img:first-child") );
					this.config.toporbottom = 'bottom';
					//Move the dot from top to bottom of the inning on the scoreboard
					$('#scoreboard-top-inning').html('&nbsp;');
					$('#scoreboard-bottom-inning').html('&#9679');
					//move previous batter to the dugout
					$("#away-dugout").append( $("#batters-box-left").children("img:first-child") );

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

					if( this.config.inning >= 9 && this.config.totalrunshome > this.config.totalrunsaway ){
					//Hack for testing
					//if( this.config.inning >= 1 && this.config.totalrunshome > this.config.totalrunsaway ){
						//The home team is ahead and therefore do not need another at bat and the game is over
						var myObject = this;
						this.config.isfinal = true;
						setTimeout(function() {
							$.proxy(myObject.endGame(), myObject);
						}, 3000);
					} else {
						$('#inning-announcement').html("BOTTOM<br>OF THE<br>" + this.config.inning + nth(this.config.inning));
						$('#inning-announcement-wrapper').show();

						setTimeout(function() {
							$('#inning-announcement-wrapper').hide();
							$("#show-pitch").hide();
						}, 3500);
					}

				} else {
					//End of the bottom of the inning
					//update away team pitcher's innings pitched
					//check whether to update pitcher or reliever
					if( this.config.awaypitcherrelieved > 0 ) {
						$('#away-reliever').attr('data-ingame-ip', parseInt($('#away-reliever').attr('data-ingame-ip')) +1 );
					} else {
						$('#away-pitcher').attr('data-ingame-ip', parseInt($('#away-pitcher').attr('data-ingame-ip')) +1 );
					}
					//Update the scoreboard
					$('#inning-'+ this.config.inning + '-home').text(this.config.runs);
					//clear the bases
					$("#home-dugout").append( $("#first-base").children("img:first-child") );
					$("#home-dugout").append( $("#second-base").children("img:first-child") );
					$("#home-dugout").append( $("#third-base").children("img:first-child") );

					//Do it again, because sometimes timing of events leaves 2 runners on a base at end of inning
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
					}, 3500);


					//move previous batter to the dugout
					$("#home-dugout").append( $("#batters-box-left").children("img:first-child") );

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

					if( this.config.inning > 9 && this.config.totalrunshome != this.config.totalrunsaway){
					//Hack for testing
					//if( this.config.inning >= 1  && this.config.totalrunshome != this.config.totalrunsaway ){
					//if( this.config.inning >= 2 ){
					//Legacy code: this should never happen because game would have ended (above) once the home team went ahead
						this.config.isfinal = true;
						$.proxy(this.endGame(), this);
					} else if( this.config.inning > 9 && this.config.totalrunshome == this.config.totalrunsaway){
						//Go into Extra Innings
						//Unhide extra scoreboard innings
						$('.scoreboard-extra-innings').show();
						//Move the dot from bottom to top of the inning on the scoreboard
						$('#scoreboard-top-inning').html('&#9679');
						$('#scoreboard-bottom-inning').html('&nbsp;');
						//Move center fielder downward
						$('#center-fielder').css('top', '185px');
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



				//Reset console windows
				$("#out-window-text").empty();
				$("#out-window").css('filter', 'brightness(75%)');
				$("#safe-window-text").empty();
				$("#safe-window").css('filter', 'brightness(75%)');
				$("#extra-window-text").empty();
				$("#extra-window").css('filter', 'brightness(75%)');
				$("#window-1").empty();
				$("#window-2").empty();
				$("#window-3").empty();
				$("#window-1-roll").empty();
				$("#window-2-roll").empty();
				$("#window-3-roll").empty();

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
				console.log("Logged " + hitWalkOrStrikeout + " for batterNumberId " + batterNumberId);
				if( hitWalkOrStrikeout == "hit" ) {
					//alert(batterNumberId);
					//$("#"+batterNumberId).data("ingame-hits", $("#"+batterNumberId).data("ingame-hits") + 1 );
					$("#"+batterNumberId).attr("data-ingame-hits", parseInt($("#"+batterNumberId).attr("data-ingame-hits")) + 1 );
				} else if( hitWalkOrStrikeout == "walk" ) {
					$("#"+batterNumberId).attr("data-ingame-walks", parseInt($("#"+batterNumberId).attr("data-ingame-walks")) + 1 );
					if( this.config.toporbottom == 'top' ) {
						if( this.config.homepitcherrelieved > 0 ) {
							$('#home-reliever').attr("data-ingame-walks-against", parseInt( $('#home-reliever').attr("data-ingame-walks-against")) + 1 );
						} else {
							$('#home-pitcher').attr("data-ingame-walks-against", parseInt( $('#home-pitcher').attr("data-ingame-walks-against")) + 1 );
						}
					}else {
						if( this.config.awaypitcherrelieved > 0 ) {
							$('#away-reliever').attr("data-ingame-walks-against", parseInt( $('#away-reliever').attr("data-ingame-walks-against")) + 1 );
						} else {
							$('#away-pitcher').attr("data-ingame-walks-against", parseInt( $('#away-pitcher').attr("data-ingame-walks-against")) + 1 );
						}
					}
				} else {
					$("#"+batterNumberId).attr("data-ingame-strikeouts", parseInt($("#"+batterNumberId).attr("data-ingame-strikeouts")) + 1 );

					//$("#"+batterNumberId).attr("data-ingame-walks", parseInt($("#"+batterNumberId).attr("data-ingame-walks")) + 1 );
					if( this.config.toporbottom == 'top' ) {
						if( this.config.homepitcherrelieved > 0 ) {
							$('#home-reliever').attr("data-ingame-strikeouts-against", parseInt( $('#home-reliever').attr("data-ingame-strikeouts-against")) + 1);
						} else {
							$('#home-pitcher').attr("data-ingame-strikeouts-against", parseInt( $('#home-pitcher').attr("data-ingame-strikeouts-against")) + 1);
						}
					}else {
						if( this.config.awaypitcherrelieved > 0 ) {
							$('#away-reliever').attr("data-ingame-strikeouts-against", parseInt( $('#away-reliever').attr("data-ingame-strikeouts-against")) + 1);
						} else {
							$('#away-pitcher').attr("data-ingame-strikeouts-against", parseInt( $('#away-pitcher').attr("data-ingame-strikeouts-against")) + 1);
						}
					}
				}

			},

			init:function(config){
				$.extend(this.config,config);


//*******Season and Team Select
				$('#season-select-button').click(function(e) {
					//When they click the season select button, reload the page with game_season_id
					location.assign('card_game.php?game_season_id=' + $('#season-select').find('option:selected:first').attr("value"));
				});
				$('#home-team-select-button').click(function(e) {
					//hide the selector and display team name in text
					$('#home-team-select-button').hide();
					$('#selected-home-team').text( $("#home-team-select option:selected" ).text() );
					$('#home-team-select').hide();
					//$('#home-team-select').css('display', 'none');
					$('#away-team-select-wrapper').show();
					//$('#away-team-select-wrapper').css('display', 'block');
					$('#team-select-submit').show();
					//$('#team-select-submit').css('display', 'block');
					//Remove home team from the away team selector
					 $("#away-team-select option[value='" + $('#home-team-select').val() + "']").remove();
				});

				$("#team-select-form").submit(function(e) {
					$("#gas-pump-div").css('opacity', '1.0');
					$("#new-game-msg").hide();
					//Get the cards for the selected teams
                			e.preventDefault();
                			//alert(this.home_team_id.value);
                			$("#home-team-id").attr("value", this.home_team_id.value);
                			$("#away-team-id").attr("value", this.away_team_id.value);

					var homeTeamId = this.home_team_id.value;
					var awayTeamId = this.away_team_id.value;
					var homeLogoUrl = $("#game-team-" + homeTeamId).attr("data-team-logo-url");
					var awayLogoUrl = $("#game-team-" + awayTeamId).attr("data-team-logo-url");

                			//Display splash
                			$("#status-msg").css("display", "block");
                			var homeTeamName = $(this).find('option:selected:first').text();
                			var awayTeamName = $(this).find('option:selected:last').text();
                			var homeLogo = '<img src="' + homeLogoUrl + '" style="max-width: 100px; margin: 10px 0px 0px 0px;">';
                			var awayLogo = '<img src="' + awayLogoUrl + '" style="max-width: 100px; margin: 10px 0px 0px 0px;">';
                			$("#status-msg-text").html( homeLogo + " " + homeTeamName + " <br><span style=\"margin: 0px 0px 0px 170px;\">vs</span><br> " + awayLogo + " " + awayTeamName );

					setTimeout(function() {
						$("#status-msg").css("display", "none");
					}, 5000);

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
								if( cardFrontResult.indexOf("home-pitcher") > -1 || cardFrontResult.indexOf("home-reliever") > -1 || cardFrontResult.indexOf("home-batter") > -1){
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
							  		var awayTeamId = $("#away-team-id").attr("value");

							  		if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("pitcher-id") ) {
							  			$("#pitcher").append( $(this) );
							  		} else if( $(this).attr('data-card-id') == $("#game-team-" + homeTeamId ).data("reliever-id") ) {
							  			$("#home-dugout").append( $(this) );
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
									console.log("Enabled batter-up button-3");
									$(".batter-up").prop("disabled", false);
									$(".batter-up").removeClass("disabled-link-button");
									$(".relieve-pitcher").prop("disabled", false);
									$(".relieve-pitcher").removeClass("disabled-link-button");


									console.log("Setting team card pics");
									$("#team-card-home").attr( "src", "/images/Baseball-cards/" + $("#game-team-" + homeTeamId).attr("data-team-card-url") );
									$("#team-card-away").attr("src", "/images/Baseball-cards/" + $("#game-team-" + awayTeamId).attr("data-team-card-url") );
									console.log("Setting team logo pics");
									var homeLogoUrl = $("#game-team-" + homeTeamId).attr("data-team-logo-url");
									var awayLogoUrl = $("#game-team-" + awayTeamId).attr("data-team-logo-url");
									$("#logo-home").attr("src", homeLogoUrl);
									$("#logo-away").attr("src", awayLogoUrl);

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

						console.log("Disabled batter-up button-A");
						$(".batter-up").prop("disabled", true);
						$(".batter-up").addClass("disabled-link-button");
						$(".relieve-pitcher").prop("disabled", true);
						$(".relieve-pitcher").addClass("disabled-link-button");
						if( !this.config.isfinal ){
							$(".send-pitch").prop("disabled", false);
							$(".send-pitch").removeClass("disabled-link-button");
						}

						//If runner on first or second, Display odds of roll and popup: Ask user if they want to steal/double steal based on these odds
						$askToSteal = false;
						//console.log("this.config.firstbaserunner=" + this.config.firstbaserunner + " this.config.secondbaserunner="+this.config.secondbaserunner);
						if( this.config.firstbaserunner && (!this.config.secondbaserunner) ) {
							//Popup with steal odds and confirm Y/N
							$askToSteal = true;

					//stealOdds based on assists per game (A/G fielding table) for catchers picking off the base runners.
					//Use Assists instead of PutOuts because catchers get PO for strikeouts, not for picking off a guy stealing second.
					//Also can use putouts per game (PO/G) for the second baseman
					//Include those in stealOdds calculation

							//Default value for assisst by catcher per game
							//Carlton Fisk 1973
							// (50/131) * 100 = 38
							//Carlton Fisk 1972
							// (72/131) * 100 = 55
							//Milt May 1973
							// (36/79 ) * 100 = 46
							//Jeff Newman 1981
							// (15/37) * 100 = 41

							//Made up average value
							$catcherAssistsAvg = 30;
							$catcherAssistsG = 0;
							$catcherID = $("#catcher").children("img:first-child").attr("id");
							console.log("$catcherID = " + $catcherID);
							console.log("season-games-in-position = " + $( "#" + $catcherID + "-back" ).data("season-games-in-position") );
							if( $( "#" + $catcherID + "-back" ).data("season-games-in-position") > 0 ) {
								console.log("assists = " + $( "#" + $catcherID + "-back" ).data("season-assists") );
								//Calculate assists per game for the catcher
								$catcherAssistsG = $( "#" + $catcherID + "-back" ).data("season-assists") / $( "#" + $catcherID + "-back" ).data("season-games-in-position");
								console.log("$catcherAssistsG = " + $catcherAssistsG);
								$catcherAssistsG = Math.round( $catcherAssistsG * 100 );
								if( $catcherAssistsG < $catcherAssistsAvg ) {
									//Ensure at least an average assist value
									$catcherAssistsG = $catcherAssistsAvg;
								}
								console.log("$catcherAssistsG = " + $catcherAssistsG);
							} else {
								$catcherAssistsG = 30;
							}

							//Default value for putouts by second baseman per 9 innings
							//Jerry Remy 1982
							// (432/1340) * 9 * 10 = 29
							//Made up average value
							$2bPutOutsAvg = 20;
							$2bPutOutsG = 0;
							$2bID = $("#second-baseman").children("img:first-child").attr("id");
							console.log("2bID = " + $2bID);
							if( $( "#" + $2bID + "-back" ).data("season-games-in-position") > 0 ) {
								//Calculate putouts per game for the second baseman
								$2bPutOutsG = $( "#" + $2bID + "-back" ).data("season-assists") / $( "#" + $2bID + "-back" ).data("season-games-in-position");
								$2bPutOutsG = Math.round( $2bPutOutsG * 10 );
								if( $2bPutOutsG < $2bPutOutsAvg ) {
									//Ensure at least an average putouts value
									$2bPutOutsG = $2bPutOutsAvg;
								}
								console.log("$2bPutOutsG = " + $2bPutOutsG);
							} else {
								$2bPutOutsG = 20;
							}
							//Start with 100% odds and reduce based on fielding stats
							//Avergage fielding stats (catcherAssistsG + 2bPutOuts90) adds up to 50
							$stealOdds = 100;
							$sbFieldingStats = Math.round( ($catcherAssistsG * 0.8) + ($2bPutOutsG * 0.8) );
							// 100 - (30 + 20) = 50
							$stealOdds = $stealOdds - $sbFieldingStats;

							//Get baserunner's stat
							//check img id of runner on first and add "-back"
							//then check season-sb value on card-back img
							$playerStatStealsID = $("#first-base").children("img:first-child").attr("id");
							$playerStatSteals = $( "#" + $playerStatStealsID + "-back" ).data("season-sb");
							console.log("playerStatSteals of runner on first base = " + $playerStatSteals );

							//Add runner's steals to $stealOdds
							$stealOdds += $playerStatSteals;
							if( $stealOdds <= 0 ) {
								$stealOdds = 35;
							}
							//Add a bonus, to make the odds more favorable for the baserunner
							$stealOdds += 5;
							if( $stealOdds > 90 ) {
								//In case of a Rickey Henderson!
								$stealOdds = 90;
							}

							$("#first-base").children("img:first-child").attr("steal-odds", $stealOdds.toString() );

							$odds = $stealOdds.toString() + "% chance to steal second<br>Catcher:" + $catcherAssistsG + ", 2B:" + $2bPutOutsG + ", RunnerSteals:" + $playerStatSteals;
							$("#steal-segment").html("000");
							$stealQuestion = "Do you want to attempt to steal second?";

							//Hide the next batter's matchup box
							$("#pitch-cards").css("display", "none");

							$("#steal-base-question").html( $odds + "<br>" + $stealQuestion );
							$("#steal-base-wrapper").css("display", "block");

							//**Bug fix
							$("#show-pitch").css("display", "block");

							//Enable the Yes/No buttons
							$(".steal-no").prop("disabled", false);
							$(".steal-no").removeClass("disabled-link-button");
							$(".steal-yes").prop("disabled", false);
							$(".steal-yes").removeClass("disabled-link-button");
						}
						if( $askToSteal ) {
							//delaying the auto-click of 'Send Pitch' button
							//console.log( "Debug: steal-no is disabled =" + $(".steal-no").prop("disabled") );
							//setTimeout(function() {
							//console.log("Auto-clicked No for steal question");
								//$(".steal-no").click();
							//}, 6000);
						} else {
							//immediate auto-click of 'Send Pitch' button
							setTimeout(function() {
								$("#show-pitch").css("display", "none");
								$(".send-pitch").click();
							}, 1500);
						}
					}, this )
				);
//**Steal-no
				$(".steal-no").click(function(e) {
					//auto-click of 'Send Pitch' button
					//$("#show-pitch").css("display", "none");
					$("#steal-base-wrapper").css("display", "none");
					$(".send-pitch").prop("disabled", false);
					$(".send-pitch").removeClass("disabled-link-button");

					setTimeout(function() {
						//Show the next batter's matchup box
						$("#pitch-cards").css("display", "block");
						$(".send-pitch").click();
					}, 1500);
				});
//**Steal-yes
				$(".steal-yes").click($.proxy(function(e) {
					//Disable the Yes/No buttons
					$(".steal-no").prop("disabled", true);
					$(".steal-no").addClass("disabled-link-button");
					$(".steal-yes").prop("disabled", true);
					$(".steal-yes").addClass("disabled-link-button");
					//Show a dice-roll
					$stealOdds = $("#first-base").children("img:first-child").attr("steal-odds");

					let s = Math.floor((Math.random() * 100) + 1);
					//Display s
					$("#steal-segment").html( s.toString().padStart(3, '0') );
					if( s <= $stealOdds ) {
						//Successful stolen base
						//Advance baserunner to second base
						$.proxy(this.advanceRunners("stolesecond"), this);

						//Display "Safe at second!"
						$("#steal-segment").html( $("#steal-segment").html() + "<br><br><span style=\"font-size: 28px; color: green; text-shadow: 8px 8px 10px #FFF, -8px -8px 10px #FFF, -8px 8px 10px #FFF, 8px -8px 10px #FFF;\">SAFE AT SECOND!</span>" );

						//**Increment stolen base (I'm not using LogHit() because we don't need to update the scoreboards with stolen bases, just the base runner's ingame total.)
						$("#second-base").children("img:first-child").attr("data-ingame-stolen-bases", parseInt($("#second-base").children("img:first-child").attr("data-ingame-stolen-bases")) + 1 );
						//$.proxy(this.logHit("stolen-base", batterNumberId), this);

					} else {
						//Out! failed to steal the base
						//Send baserunner to the dugout
						console.log("Failed to steal second - out");

						//Increase outs
						this.config.outs += 1;
						$.proxy(this.advanceRunners("outstealingsecond"), this);
						//Display "Out at second!"
						$("#steal-segment").html( $("#steal-segment").html() + "<br><br><span style=\"font-size: 28px; color: red; text-shadow: 8px 8px 10px #FFF, -8px -8px 10px #FFF, -8px 8px 10px #FFF, 8px -8px 10px #FFF;\">OUT AT SECOND!</span>" );


						if( this.config.outs < 3){
							//disable buttons

							console.log("Disabled batter-up button-B");
							$(".batter-up").prop("disabled", true);
							$(".batter-up").addClass("disabled-link-button");
							$(".relieve-pitcher").prop("disabled", true);
							$(".relieve-pitcher").addClass("disabled-link-button");

							//update the scoreboards
							var tempOuts = this.config.outs;
							setTimeout(function() {
								//Add a red dot to "Outs" on main scoreboard
								for(var i=1; i <= tempOuts; i++){
									$("#out-"+i).addClass("dot-on");
								}
								//Update outs on mini scoreboard
								$('#mini-outs').html("O: " +  tempOuts );
							}, 2000);
						}
					}
					//Pitch to the next batter unless it's now 3 outs
					if( this.config.outs < 3 ) {
							//disable buttons
							console.log("Disabled batter-up button-C");
							$(".batter-up").prop("disabled", true);
							$(".batter-up").addClass("disabled-link-button");
							$(".relieve-pitcher").prop("disabled", true);
							$(".relieve-pitcher").addClass("disabled-link-button");

						setTimeout(function() {
							//Hide the base stealing popup
							$("#steal-base-wrapper").css("display", "none");
							$(".send-pitch").prop("disabled", false);
							$(".send-pitch").removeClass("disabled-link-button");
							//Show the next batter's matchup box
							$("#pitch-cards").css("display", "block");
							$(".send-pitch").click();
						}, 4000);
					} else {
						//3rd out, End the inning
						//Shrink the batter image back down
						$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-home");
						$("#batters-box-left").children("img:first-child").removeClass("enlarged-batter-away");
						//disable buttons

						console.log("Disabled batter-up button-D");
						$(".batter-up").prop("disabled", true);
						$(".batter-up").addClass("disabled-link-button");
						$(".relieve-pitcher").prop("disabled", true);
						$(".relieve-pitcher").addClass("disabled-link-button");

						//**TODO-verify if need to rollback the batter order by one

						var myObj = this;
						setTimeout(function() {
							$.proxy(myObj.endInning(), myObj);
							$('#mini-outs').html("O: 0");
							$("#steal-base-wrapper").css("display", "none");
							$("#pitch-cards").css("display", "block");
						}, 5000);

					}
				}, this )
				);
//**relieve pitcher
				$(".relieve-pitcher").click($.proxy(function(e) {

					//$("#steal-base-wrapper").css("display", "none");

					$(".relieve-pitcher").prop("disabled", true);
					$(".relieve-pitcher").addClass("disabled-link-button");

					//Change the pitcher to the relief pitcher in the dugout
					if( this.config.toporbottom == 'top' ) {
						this.config.homepitcherrelieved = this.config.inning;
						//send home team pitcher to the dugout
						$("#home-dugout").append( $("#pitcher").children("img:first-child") );
						//put reliever on the mound
						$("#pitcher").append( $("#home-dugout").children("#home-reliever") );

					} else {
						this.config.awaypitcherrelieved = this.config.inning;
						//send away team pitcher to the dugout
						$("#away-dugout").append( $("#pitcher").children("img:first-child") );
						//put reliever on the mound
						$("#pitcher").append( $("#away-dugout").children("#away-reliever") );

					}


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

					//Reset console windows
					$("#out-window-text").empty();
					$("#out-window").css('filter', 'brightness(75%)');
					$("#safe-window-text").empty();
					$("#safe-window").css('filter', 'brightness(75%)');
					$("#extra-window-text").empty();
					$("#extra-window").css('filter', 'brightness(75%)');
					$("#window-1").empty();
					$("#window-2").empty();
					$("#window-3").empty();
					$("#window-1-roll").empty();
					$("#window-2-roll").empty();
					$("#window-3-roll").empty();

					//show backs (or fronts) of batter and pitcher cards in an otherwise empty div (similar to a modal window)
					var batterNumberId = "";
					var pitcherNumberId = "";

					//hitBooster % to make hits happen more often.
					var hitBooster = 25; // 25 = 2.5%
					//Hack for testing
					//hitBooster = 400;
					if( this.config.toporbottom == 'top' ) {
						batterNumberId = "away-batter-" + this.config.batternumberaway + "-back";
						if( this.config.homepitcherrelieved > 0 ) {
							pitcherNumberId = "home-reliever-back";
						} else {
							pitcherNumberId = "home-pitcher-back";
						}
					} else {
						batterNumberId = "home-batter-" + this.config.batternumberhome + "-back";
						if( this.config.awaypitcherrelieved > 0 ) {
							pitcherNumberId = "away-reliever-back";
						} else {
							pitcherNumberId = "away-pitcher-back";
						}
						//Home team gets extra hit boost for "home team advantage"
						hitBooster = 45; // 45 = 4.5%
						//Hack for testing
						//hitBooster = 400;
					}


					var seasonBattingAvg = $("#"+batterNumberId).data("season-batting-avg");
					var seasonDoubles = $("#"+batterNumberId).data("season-2b");
					var seasonTriples = $("#"+batterNumberId).data("season-3b");
					var seasonHRs = $("#"+batterNumberId).data("season-hr");
					var seasonSBs = $("#"+batterNumberId).data("season-sb");
					var seasonSOs = $("#"+batterNumberId).data("season-so");
					var seasonBBs = $("#"+batterNumberId).data("season-bb");
					var seasonAtBats = $("#"+batterNumberId).data("season-at-bats");

					//season-so and bb need to be filled for pitchers
					var seasonEra = $("#"+pitcherNumberId).data("season-era") ;
					var pitcherSo = $("#"+pitcherNumberId).data("season-so") ;
					var pitcherBb = $("#"+pitcherNumberId).data("season-bb") ;
					var seasonIp = $("#"+pitcherNumberId).data("season-ip") ;
					//Pitcher's SO9 is (9 x strikeouts) / innings pitched
					var so9 = ( 9 * pitcherSo) / seasonIp;

					$("#show-pitch").css("display", "block");
					$("#steal-base-wrapper").css("display", "none");
					$("#pitch-cards").css("display", "block");

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
					$("#pitch-batter-data").append( "<LI>Stolen Bases:" + seasonSBs + "</LI>" );

					$("#pitch-pitcher-data").append( "<LI>IP: " + seasonIp + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>ERA: " + seasonEra + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>Strikeouts: " + pitcherSo + "</LI>" );
					$("#pitch-pitcher-data").append( "<LI>Walks: " + pitcherBb + "</LI>" );

				//*****Matchup calculations

					// Safe/Out Segment:
					//Average ERA is 4.25. So, if a great pitcher has a lower than average ERA, it puts downward pressure on the batter's BA.
					//If the pitcher has a higher than average ERA, the batter gets a bonus of upward pressure on their BA.
					//safeOutFactor is 10 x ( ( seasonBattingAvg * 100 ) - ( 2 x 4.25 ) + (2 x seasonEra) )

					//.240 against 2.5 = 10 * ( (24) - 8.5 + 5 ) = 205 which is a 20.5% chance of a Safe at-bat
					//.240 against 4.25 = 10 * ( (24) - 8.5 + 8.5 ) = 240 which is a 24% chance
					//.240 against 5.75 = 10 *( (24) - 8.5 + 11.5 ) = 270 which is a 27% chance
					var pitcherStat = (2 * seasonEra);
					//Fatigue is tracked by inningsPitched and increases with each inning the pitcher is pitching
					//Pitcher can be relieved once by a fresh pitcher
//*********Pitcher fatigue
					var inningsPitched = 0;
					if( this.config.toporbottom == 'top' ){
						inningsPitched = this.config.inning - this.config.homepitcherrelieved;
					} else {
						//bottom
						inningsPitched = this.config.inning - this.config.awaypitcherrelieved;
					}

					//The 'minus 2' is to give each pitcher a couple of innings at their strongest
					pitcherStat = pitcherStat + ( inningsPitched - 2 );
					console.log("PitcherStat: " + pitcherStat );

//*****STRIKEOUT / BB / FAIR BALL
					//SO Range = ( ( pitcher SO / IP ) / 3 ) x 100
					//[estimated 3 at-bats per inning, so divide by 3]
					//Considered changing this to a Strikeout Avg (like the HR avg used for homerun factor)
					//Tried SO9, which is ( (9 x strikeouts) / IP)
					//But the difference is not apparently significant, by ratio, just lower.
					//Tried multiplying SO9 x 3, to get comparable numbers.
					//( (9 * 274) / 257.2 ) = 9.6 (SO9 Mario Soto) Times 3 = 28.8
					//( (274 / 257.2) / 3 ) * 100 = 35.5 (Current formula Mario Soto)
					//( (9 x 39) / 107 ) = 2.85 (SO9 Ellis Kinder) Times 3 = 8.55
					//( (39 / 107) / 3 ) * 100 = 12.1 (Current formula Ellis Kinder)
					var safeOut = "";
					var outType = "";
					var hitType = "";

					var strikeoutRange = ( ( ( pitcherSo/ seasonIp ) / 3 ) * 100 ) - (inningsPitched * 3);
					//Nolan Ryan: ( ( ( 140/ 149) / 3) * 100 ) = 31 - [Fatigue] (inningsPitched * 3)
					strikeoutRange += 3;
					if( strikeoutRange  < 10 ){
						strikeoutRange = 10;
					}
					var batterSOavg = ( seasonSOs / seasonAtBats ) * 100;
					//batterSOavg += 2;
					if( batterSOavg  < 10 ){
						batterSOavg = 10;
					}
					//Eric Chavez: ( 100 / 485 ) * 100 = 20.6
					//using the average of batterSOavg and strikeoutRange
					//strikeoutRange = ( strikeoutRange + batterSOavg ) / 2;
					strikeoutRange = ( (strikeoutRange * 2) + batterSOavg ) / 3;
					if( strikeoutRange  < 10 ){
						strikeoutRange = 10;
					}
					var walkRange = strikeoutRange + ( ( (pitcherBb / seasonIp) / 3) * 100 );
					walkRange += 4;
					if( walkRange < strikeoutRange + 8 ){
						walkRange = strikeoutRange + 8;
					}
					var batterBBavg = ( seasonBBs / seasonAtBats ) * 100;
					//batterBBavg += 4;
					if( batterBBavg  < 8 ){
						batterBBavg = 8;
					}
					//Eric Chavez: ( 84 / 485 ) * 100 = 17.3
					//get batterBBavg and use the average of walkRange and batterBBavg
					walkRange = ( (walkRange * 2) + batterBBavg ) / 3;
					if( walkRange < strikeoutRange + 8 ){
							walkRange = strikeoutRange + 8;
					}

					let w = Math.floor((Math.random() * 100) + 1);
					$("#window-1").html('Strikeout < <span style="font-size: 20px;">' + strikeoutRange.toFixed(0) + '</span>, Walk < ' + walkRange.toFixed(0));
					$("#window-1-roll").html( "<span id=\"walk-segment\">" + w.toString().padStart(3, '0') + "</span>" );
					if( w < strikeoutRange ) {
			//***STRIKEOUT
						outType = "Strikeout";
						//Update placeholder data field for this batter and pitcher with in-game Strikeout++
						$.proxy(this.logHit("strikeout", batterNumberId), this);

						this.config.strikeouts += 1;
						this.config.outs += 1;
						$('#scoreboard-strikeouts').text( this.config.strikeouts );
						if( this.config.toporbottom == 'top' ) {
							this.config.totalstrikeoutsaway += 1;
							$('#total-so-away').text( this.config.totalstrikeoutsaway );
							//Put him in the dugout
				//**TODO
						} else {
							this.config.totalstrikeoutshome += 1;
							$('#total-so-home').text( this.config.totalstrikeoutshome );
							//Put him in the dugout
				//**TODO
						}
						hitTypeDisplay = "STRIKEOUT";
						$("#out-window-text").html( "<span id=\"out-type-segment\">" + hitTypeDisplay + "</span>");
						$("#out-window").css('filter', 'brightness(150%)');

					} else if( w < walkRange ){
		//***BASE ON BALLS
						hitType = "Walk";
						$.proxy(this.logHit("walk", batterNumberId), this);
						hitTypeDisplay = "BASE ON BALLS";
						$tmpObj = this;

						$.proxy($tmpObj.advanceRunners(hitType), $tmpObj);
						$("#safe-window").css('filter', 'brightness(150%)');
						$("#safe-window-text").html( "<span id=\"hit-segment-2\">" + hitTypeDisplay + "</span>");
					} else {
		//***FAIR BALL
						//***Is it SAFE OR OUT?
						var safeOutFactor =  10 * ( ( ( seasonBattingAvg * 100 ) - 8.5 ) + pitcherStat );
						safeOutFactor = safeOutFactor + hitBooster;
						let x = Math.floor((Math.random() * 1000) + 1);
						$("#window-2").html('Roll < <span style="font-size: 20px;">' + safeOutFactor.toFixed(0) + '</span> for a HIT');
						$("#window-2-roll").html( "<span id=\"safe-out\">" + x.toString().padStart(3, '0') + "</span>");

						if( x <= safeOutFactor ) {
					//*****SAFE
							//Testing Hack
							//if( x <= 500 ) {
							safeOut = "Safe";
							console.log("safeOut = Safe");

							//**NOTE: pitch-results was the original way of outputting the results, before I added the gas pump image and animations
							var hitString = "";
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

							hitString += "<div id=\"hit-segment\">..." + w + " -->HIT!<br><div class=\"loader-container\"><div class=\"loading-text\" style=\"font-size: 20px;\"><span class=\"dots\" style=\"animation-delay: 10s;\"></span></div></div>";
							var hrAvg = (600 * seasonHRs) / seasonAtBats;
							homerunAdjust = homerunAdjust + hrAvg;
							hitString += "HR AVG: " + hrAvg.toFixed(0) + " <span style='font-size: 12px;'>(above " +averageHRA + " gets HR boost)</span><br><span style='font-size: 12px;'>HRA=(600*seasonHRs)/seasonAtBats</span><br>";

							//Redistribute the pie
							singlesSegment = singlesSegment + ( 20 - homerunAdjust )
							//The singlesSegment now has the homerunSegment reduced from it, for the sequential
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
							//Hack for testing
							//hitType = "Single";

							var hitText = hitType;
							if( hitType == "Home Run" ){
								hitText = "<br>!! " + hitText + " !!";
								$("#batters-box-left").children("img:first-child").attr("data-ingame-home-runs", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-home-runs")) + 1 );
							} else if( hitType != "Walk" ) {
								hitText = hitText + "!";
							}
							$.proxy(this.logHit("hit", batterNumberId), this);
							hitTypeDisplay = hitType;
							$tmpObj = this;
							//setTimeout(function() {
								$.proxy($tmpObj.advanceRunners(hitType), $tmpObj);
								$("#safe-window").css('filter', 'brightness(150%)');
							//}, 4000);
									$("#safe-window-text").html( "<span id=\"hit-segment-2\">" + hitTypeDisplay + "</span>");
						} else {
					//*****OUT
							safeOut = "Out";
							console.log("safeOut = Out");
							this.config.outs += 1;

							//Increment the players ingame at-bat count (but not if it's a Walk)
							$("#batters-box-left").children("img:first-child").attr("data-ingame-atbats", parseInt($("#batters-box-left").children("img:first-child").attr("data-ingame-atbats")) + 1 );
							$("#out-window").css('filter', 'brightness(150%)');

							// Using MLB typical seasonal ratio. Of Fly&Ground Outs, 56% are Ground Outs and 43% are Fly Outs
							var flyOutFactor = 56;
							//Hack for testing
							//flyOutFactor = 100;
							var displayFlyOutFactorPlus1 = flyOutFactor + 1;
							let z = Math.floor((Math.random() * 100) + 1);
							$("#pitch-results").append( "<div id=\"flyout-segment\"><div class=\"loader-container\"><div class=\"loading-text\" style=\"font-size: 20px;\"><span class=\"dots\" style=\"animation-delay: 10s;\"></span></div></div>Fly Out Segment: 1 to " + flyOutFactor.toFixed(0) + "<br>Ground Out Segment: " + displayFlyOutFactorPlus1.toFixed(0) + " to 100<br>Dice Roll... " + z + "<br></div>");
							if( z <= flyOutFactor ) {
						//**FLY OUT
								outType = "FLY OUT";
								var advanceRunnersSegment = 50;
								var errorSegment =  advanceRunnersSegment + 6;
								//Hack for testing
								//advanceRunnersSegment = 100;
								let w = Math.floor((Math.random() * 100) + 1);
								//Hack for testing
								//w = 51;
								//$("#pitch-results").append( "<div id=\"double-play-segment\">Advance Runners Segment: <" + advanceRunnersSegment +  " /100<br>Dice Roll... " + w + "<br></div>");
								if ( this.config.outs < 3 && (this.config.firstbaserunner || this.config.secondbaserunner || this.config.thirdbaserunner) ) {
									$("#window-3").html('Roll < <span style="font-size: 20px;">' + advanceRunnersSegment + '</span> runners advance');
									$("#window-3-roll").html( "<span id=\"runners-advance-segment\">" + w.toString().padStart(3, '0') + "</span>");
								}
								if ( this.config.outs < 3 && w <= advanceRunnersSegment && (this.config.firstbaserunner || this.config.secondbaserunner || this.config.thirdbaserunner) ) {
									//Log hit to advance runners
									outType = "SACRIFICE FLY - Runners Advance!";
									$.proxy(this.advanceRunners("flyout"), this);
								}
								if (  this.config.outs < 3 && w > advanceRunnersSegment && w <= errorSegment ) {
									//Error - runner to first base
									//Pull back the out
									this.config.outs -= 1;
									$.proxy(this.advanceRunners("flyerror"), this);
									//Update the outType
									outType = "Outfield ERROR !!";
								}
							} else {
						//**GROUND OUT
								outType = "GROUND OUT";
								//If runner on first, roll for chance of a double play
								var doublePlaySegment = 65;
								var errorSegment = doublePlaySegment + 10;
								//Hack for testing
								//doublePlaySegment = 100;
								let w = Math.floor((Math.random() * 100) + 1);
								//$("#pitch-results").append( "<div id=\"double-play-segment\">Double PLay Segment: <" + doublePlaySegment +  " /100<br>Dice Roll... " + w + "<br></div>");
								if ( this.config.outs < 3 && this.config.firstbaserunner ) {
									$("#window-3").html('Roll < <span style="font-size: 20px;">' + doublePlaySegment + '</span> for double play');
									$("#window-3-roll").html(  "<span id=\"runners-advance-segment\">" + w.toString().padStart(3, '0') + "</span>");
								}
								if ( this.config.outs < 2 && this.config.firstbaserunner && w <= doublePlaySegment ) {
									//Double PLay
									console.log("Double Play BEFORE- firstbaserunner=" + this.config.firstbaserunner + " secondbaserunner=" + this.config.secondbaserunner + " thirdbaserunner=" + this.config.thirdbaserunner);
									if( this.config.secondbaserunner && this.config.thirdbaserunner ) {
										//Fielder's Choice Double Play - Bases loaded, There is a force play, 1b to 2b to 3b
										//Batter is safe at first.
										//Batter on first is safe at second
										//Baserunner on second is out at third
										//baserunner on third is out at home
										this.config.firstbaserunner = true;
										this.config.secondbaserunner = true;
										this.config.thirdbaserunner = false;

										//Send second and third base runners to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#second-base").children('img')[0] );
											$("#away-dugout").prepend( $("#third-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#second-base").children('img')[0] );
											$("#home-dugout").prepend( $("#third-base").children('img')[0] );
										}
										//Update the outType for display
										outType = "GROUND OUT - <span style='text-decoration: none;'>Fldr's Choice Double Play!!</span>";
										//Put the first base runner on second
										$("#second-base").prepend( $("#first-base").children('img')[0] );
										//Put the batter on first
										$("#first-base").prepend( $("#batters-box-left").children('img')[0] );
									} else if( this.config.secondbaserunner ) {
										//Fielder's Choice Double Play- There is a force play, 1b to 2b
										//Batter is safe at first. Baserunners on first and second are both out
										//Update the outType for display
										outType = "GROUND OUT - <span style='text-decoration: none;'>Fldr's Choice Double Play!!</span>";
										this.config.firstbaserunner = true;
										this.config.secondbaserunner = false;
										this.config.thirdbaserunner = false;

										//Send first and second base runners to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#first-base").children('img')[0] );
											$("#away-dugout").prepend( $("#second-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#first-base").children('img')[0] );
											$("#home-dugout").prepend( $("#second-base").children('img')[0] );
										}
										//Put the batter on first
										$("#first-base").prepend( $("#batters-box-left").children('img')[0] );
									} else {
										//Batter is out and First base runner is out
										//(Batter gets cleared automatically so we don't need to clear him here.)
										//Update the outType for display
										outType = "GROUND OUT - <span style='text-decoration: none;'>Double Play!!</span>";
										if( this.config.thirdbaserunner ) {
											//If runners on first and third, we need to advance the third base runner
											//Thirdbaserunner scores
											//****Ask Ron what usually happens to the not-forced runners?
											//$.proxy(this.advanceRunners("groundout"), this);

										}
										//Send first base runner to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#first-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#first-base").children('img')[0] );
										}
										this.config.firstbaserunner = false;
									}
									//Update the inning outs with an additional out (scoreboard will be updated below)
									this.config.outs += 1;
									console.log("Double Play AFTER- thirdbaserunner=" + this.config.thirdbaserunner + " secondbaserunner=" + this.config.secondbaserunner + " firstbaserunner=" + this.config.firstbaserunner);
								} else {
									//Not a double play
									//Fielder's Choice, if there's a force play
									if( this.config.firstbaserunner && this.config.secondbaserunner && this.config.thirdbaserunner ) {
										//Third base runner is out at home
										//Second base runner is safe at third
										//first base runner is safe at second
										//batter is safe at first
										this.config.firstbaserunner = true;
										this.config.secondbaserunner = true;
										this.config.thirdbaserunner = true;
										//Send the third base runner to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#third-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#third-base").children('img')[0] );
										}
										//Put the second base runner on third
										$("#third-base").prepend( $("#second-base").children('img')[0] );
										//Put the batter on first
										$("#first-base").prepend( $("#batters-box-left").children('img')[0] );
										outType = "GROUND OUT - Fielder's Choice";
									} else if(  this.config.firstbaserunner && this.config.secondbaserunner ) {
										//Second base runner is out at third
										//first base runner is safe at second
										//batter is safe at first
										this.config.firstbaserunner = true;
										this.config.secondbaserunner = true;
										this.config.thirdbaserunner = false;
										//Send the second base runner to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#second-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#second-base").children('img')[0] );
										}
										//Put the first base runner on second
										$("#second-base").prepend( $("#first-base").children('img')[0] );
										//Put the batter on first
										$("#first-base").prepend( $("#batters-box-left").children('img')[0] );
										outType = "GROUND OUT - Fielder's Choice";

									} else if( this.config.firstbaserunner  ) {
										//first base runner is out at second
										//batter is safe at first
										this.config.firstbaserunner = true;
										this.config.secondbaserunner = false;
								//If there is a thirdbaserunner, he can stay
								//this.config.thirdbaserunner = false;

										//Send the first base runner to the dugout
										if( this.config.toporbottom == 'top' ) {
											$("#away-dugout").prepend( $("#first-base").children('img')[0] );
										} else {
											$("#home-dugout").prepend( $("#first-base").children('img')[0] );
										}
										//Put the batter on first
										$("#first-base").prepend( $("#batters-box-left").children('img')[0] );
										outType = "GROUND OUT - Fielder's Choice";

									} else if( this.config.secondbaserunner ) {
										//If there is just a runner on second, we need to advance the second base runner to third
										//****Ask Ron what usually happens to the not-forced runners?
										//$.proxy(this.advanceRunners("groundout"), this);

									}
									//else do nothing. batter is out and will get sent to dugout.
								}
								if (  this.config.outs < 3 && w > doublePlaySegment && w <= errorSegment ) {
									//Ground Ball Error - runner gets to first base
									this.config.outs -= 1;
									$.proxy(this.advanceRunners("grounderror"), this);
									//Update the outType
									outType = "Infield ERROR !!";
								}

							}//End Ground Out

							if( outType.substring(0, 12) == 'GROUND OUT -' ) {
								$("#out-window-text").html( "<span id=\"out-type-segment\">" + outType.substring(0, 10) + "</span>");
								//setTimeout(function() {
									$("#extra-window").css('filter', 'brightness(150%)');
								//}, 5000);
								$("#extra-window-text").html( "<span id=\"extra-window-segment\">" + outType.substring(12) + "</span>");
							} else if( outType.substring(0, 15) == 'SACRIFICE FLY -' ) {
								$("#out-window-text").html(  "<span id=\"out-type-segment\">" + outType.substring(0, 13) + "</span>");
								//setTimeout(function() {
									$("#extra-window").css('filter', 'brightness(150%)');
								//}, 5000);
								$("#extra-window-text").html(  "<span id=\"extra-window-segment\">" + outType.substring(16) )+ "</span>";
							} else if( outType.includes("ERROR") ) {
								//setTimeout(function() {
									$("#out-window").css('filter', 'brightness(75%)');
									$("#safe-window").css('filter', 'brightness(150%)');
									$("#safe-window-text").html( 'FIRST BASE ROE');
									$("#extra-window").css('filter', 'brightness(150%)');
								//}, 2000);
								$("#extra-window-text").html(  "<span id=\"extra-window-segment\">" + outType + "</span>");
							} else {
								$("#out-window-text").html( "<span id=\"out-type-segment\">" + outType + "</span>");
							}
						}//End of OUT
					}//End of Fair Ball

					if( this.config.outs == 3 ){
						batterButtonWaitTime = 7000;
						//disable buttons

						console.log("Disabled batter-up button-E");
						$(".batter-up").prop("disabled", true);
						$(".batter-up").addClass("disabled-link-button");
						$(".relieve-pitcher").prop("disabled", true);
						$(".relieve-pitcher").addClass("disabled-link-button");

						var myObj = this;
						setTimeout(function() {
							$.proxy(myObj.endInning(), myObj);
							$('#mini-outs').html("O: 0");
						}, 3000);
					}
					else {
						//update the scoreboards
						var tempOuts = this.config.outs;
						//Add a red dot to "Outs" on main scoreboard
						for(var i=1; i <= tempOuts; i++){
							$("#out-"+i).addClass("dot-on");
						}
						//Update outs on mini scoreboard
						$('#mini-outs').html("O: " +  tempOuts );
					}

					if( this.config.toporbottom == 'bottom' && this.config.inning >= 9 && this.config.totalrunshome > this.config.totalrunsaway){
						//Home team is ahead and has won the game
						//Hack for testing
						//if( this.config.toporbottom == 'bottom' && this.config.inning >= 1 && this.config.totalrunshome > this.config.totalrunsaway){
						var myThis = this;
						setTimeout(function() {
							$.proxy(myThis.endInning(), myThis);
						}, 5000);
					} else if( this.config.outs < 3 ){
						//check whether is top or bottom of the inning and then check home/away pitcherrelieved
						if( (this.config.toporbottom == 'top' && this.config.homepitcherrelieved == 0) || (this.config.toporbottom == 'bottom' && this.config.awaypitcherrelieved == 0) ) {
							setTimeout(function() {
								//Re-enable Batter up

								console.log("Enabled batter-up button-4");
								$(".batter-up").prop("disabled", false);
								$(".batter-up").removeClass("disabled-link-button");
								$(".relieve-pitcher").prop("disabled", false);
								$(".relieve-pitcher").removeClass("disabled-link-button");
							}, batterButtonWaitTime);
						} else {
							setTimeout(function() {
								//Re-enable Batter up

								console.log("Enabled batter-up button-5");
								$(".batter-up").prop("disabled", false);
								$(".batter-up").removeClass("disabled-link-button");
								//Disable Relieve Pitcher
								$(".relieve-pitcher").prop("disabled", true);
								$(".relieve-pitcher").addClass("disabled-link-button");
							}, batterButtonWaitTime);
						}
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
		//game.startInning();
        });
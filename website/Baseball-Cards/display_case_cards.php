<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Baseball Cards Display</title>
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .grid-container {
           max-width: 2480px;
	     margin: 0px 20px 20px 0px;
	     padding: 10px;
	     display: grid;    
	     grid-gap: 0px;
	     border: 5px solid #000;
        }
        .grid-item {
            width: auto;
            margin: 0px;
            padding: 5px 5px 0px 5px;
            /*border: 1px solid #ddd;*/
            border-bottom: 10px solid #000;
            text-align: center;        
        }
        .grid-item img {
	  width: 100%;
	  height: 100%;
	  object-fit: contain; 
	}
        .float-container {
            width: 100%;
	    border: 0px solid #fff;
	    padding: 0px ;
	    margin-right: 20px;
	}
	.float-container:after {
	    content:'';
	    display:block;
	    clear: both;
	}
	.float-child-left {
	    width: 40%;
	    float: left;
	    padding: 5px;
	    border: 0px solid black;   
	} 
	.float-child-right {
	    width: 45%;
	    float: right;
	    padding: 5px;
	    border: 0px solid black;
	    text-align: right;
	    margin-right: 20px;
	}
	#display-case-top {
		max-width: 2500px;
		margin: 0px 20px 0px 0px;
		border: 1px solid black;	     
	}
	#display-case-title {
		font-family: Brush Script MT;
		font-size: 60px;
		text-align: center;
		width: 100%;
		text-shadow: #C00 1px 0 10px;
		border: 3px solid silver;
	}
	#display-case-title-plate {
		text-align: center;
		width: 55%;
		padding: 7px 0px 7px 0px;
		margin: auto;
	}
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            padding: 10px;
            background: #fff;
            z-index: 1000;
           /*max-width: 600px;*/
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            border: 1px solid black;
            padding: 10px;
        }
        .modal-close {
        	float: right;
        	margin: 0px 0px 10px 0px; 
        	font-size: 22px;
        }
        #modal-content {
        	font-weight: bold;
        }
        #card-details {
        	font-weight: bold;
        	font-size: 18px;
        	float: right; 
        	padding-left: 7px;
        	margin: 0px 0px 0px 7px;	
        }
        #dynamic-details {
        	border: 1px solid darkblue;
        	padding: 5px;
        	width: 215px;
        	height: 210px;
        	background-color: #CBE2FE;
        }
        #card-info {
        	margin: 10px 0px 20px 0px;
        }
        #refined-avg {
        	font-weight: normal;
        	font-size: 14px;
        }
        #auction-count {
        	font-weight: normal;
        	font-size: 14px;
        	margin-bottom: 5px;
        }
        #no-data {
        	font-weight: normal;
        	font-size: 14px;
        	font-style: italic;
        	padding-left: 15px;
        }
        #api-calculated {
        	font-weight: normal; 
        	text-decoration: line-through;
        	font-size: 14px;
        }
        #beta {
        	font-weight: bold;
        	font-family: Brush Script MT;
        	font-size: 24px;
        	margin: 25px 0px 0px 0px;
        }
        #ebay-title {
        	font-weight: bold;
        	font-size: 16px;
        	margin-top: 5px;
        }
        #population-total {
        	font-weight: bold;
        	font-size: 16px;
        	margin-top: 5px;
        }
        #population-value {
        	font-weight: normal; 
        	font-size: 16px;
        }
        #population-above {
        	font-weight: normal; 
        	font-size: 14px;
        	margin-bottom: 5px;
        }
        #flip-button {
        	padding: 0px 170px 20px 0px;
        }
        #flip-button a{
        	display: inline-block; 
        	position: relative; 
        	float: right; 
        	background-color: lightgray; 
        	color: #FFF;
        }
        #no-flip-button {
        	padding: 0px 170px 20px 0px;
        }
        #no-flip-button a{
        	display: inline-block; 
        	position: relative; 
        	float: right; 
        	background-color: lightgray; 
        	color: #FFF;
        }
        #click-title {
        	font-size: 18px;
        	font-weight: bold;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" />
    
<?php require 'head_scripts_include.php';?>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
   
        $(document).ready(function () {
		
		$(".grid-button").click(function(e) {
		    e.preventDefault();
		    var dcID = $('#dc').text();
		    $.ajax({
			type: "POST",
			url: "update_display_case_grid.php",
			data: { 
			    dc: dcID,
			    grid: $(this).val() 
			},
			success: function(response) {
			    //alert('ok');
			    location.reload();
			},
			error: function(response) {
			    alert('error');
			}
		    });
		});
		
			
		$(".grid-item").click(function() {	
			var details = $(this).data("details");
			$("#modal-content").html(details);
			$(".modal").show();
			$(".modal-overlay").show();
			var population = "";
			var showingWaitingGif = true;
			var grade = $(this).data("grade");
			if( $(this).data("grade-company" ) == "PSA" ) {	
				var psaCert = $(this).data("serial");
				var settings = {
				    "async": true,
				    "crossDomain": true,

				   "url": "https://api.psacard.com/publicapi/cert/GetByCertNumber/" + psaCert,
				    "method": "GET",
				    "headers": {
					  "authorization": "bearer eepqSms9APKK3dWDg53q0IALKyh3lbQNiYcs6VhaZVTH1MVITKhdYAdkU5ka6ZHTQ2QugDf--B6HPWkMEiPcxAzk09Ch4kgdAOM6TgrojQB2CzY7DeHdjBTmC6PAbzXIblD9OI0zugJv7G4w6lioLXZ7SwsYPX_KxRy1tn2PqhGCsy2FiQ6MOJj9lUATvynAcTv_hf37wWC5okvW17WD3itLcgvux1p_U72AQKsJBxSegveMA3d_7oGmQLa8Mk88LJRKr7bfkMrDaU9GzRc3w8EGbRI6SQ0KVqYOwZ49hM7THcK2"
				     },
				     dataType: "json"
				}
				$.ajax(settings).done(function (response) {
				    console.log(response);
					//alert( response.PSACert.TotalPopulation);
					
					//Declare the Population
					population += "<div id=population-total>Population @ grade " + grade + ": <span id=population-value>" + response.PSACert.TotalPopulation + "</span></div>";
					population += "<div id=population-above>Population above this grade: " + response.PSACert.PopulationHigher + "</div>";
					if( showingWaitingGif ) {
						$("#card-pop").html(population);
					} else {
						$("#card-pop").html($("#card-pop").html() + population);
					}
					showingWaitingGif = false;
				});
				
			}
			
			
		 	//Now try SGC population
			//------Requested API access from SGC
			
			
			//Now pull Ebay Auction sale prices and calculate the average price, etc.
			var gradeCompany = $(this).data("grade-company");
			var playerName = $(this).data("player-name");
			var year = $(this).data("year");
			var manufacturer = $(this).data("brand");
			var cardNumber = $(this).data("number");
			var keywordString = "+" + year + "+" + manufacturer + "+" + playerName + "+#" + cardNumber + "+" + gradeCompany;
			var cardSet = year + " " + manufacturer;
			var dataArray =  {"keywords":keywordString, "allow_rewritten_results": "false", "remove_outliers": "true","listing_type": "auction","LH_Auction":"1", "LH_BIN":"0","max_search_results":"60","category_id": "261328","aspects": [  {  "name": "Graded",  "value": "Yes" }, {  "name": "Grade",  "value": grade }, {"name": "Professional Grader", "value": "Professional Sports Authenticator (PSA)" }, {"name": "Player/Athlete","value":playerName}, {"name": "Manufacturer","value":manufacturer},{"name": "Season","value":year} ,{"name": "Year Manufactured","value":year},{"name": "Set","value":cardSet} ]};
			var jsonArray = JSON.stringify(dataArray); 

			//This is the default, for PSA cards
			var settings = {
				async: true,
				crossDomain: true,
				url: 'https://ebay-average-selling-price.p.rapidapi.com/findCompletedItems',
				method: 'POST',
				headers: {
					'x-rapidapi-key': '650288e3e8msh6ea1f67967cebf1p1a091fjsn1c77318c1e26',
					'x-rapidapi-host': 'ebay-average-selling-price.p.rapidapi.com',
					'Content-Type': 'application/json'
				},
				processData: false,
				data: jsonArray			
			};
			if( gradeCompany == "SGC" ) {
				settings = {
					async: true,
					crossDomain: true,
					url: 'https://ebay-average-selling-price.p.rapidapi.com/findCompletedItems',
					method: 'POST',
					headers: {
						'x-rapidapi-key': '650288e3e8msh6ea1f67967cebf1p1a091fjsn1c77318c1e26',
						'x-rapidapi-host': 'ebay-average-selling-price.p.rapidapi.com',
						'Content-Type': 'application/json'
					},
					processData: false,
					data: jsonArray
				};
			}
						
			$.ajax(settings).done(function (response) {
				console.log(response);	
				var population = '<div id=ebay-title>Average Ebay Auction Price</div>';
				population += '<div id=api-calculated>API Calculated Avg: $' + response.average_price + '</div>';

				//Now exclude the 'Buy It Now', wrong year, wrong grade company
				//console.log(response.products);
				var cardCount = 0;
				var moneyCount = 0;
				var moneyAvg = 0;
				$.each(response.products, function(key,valueObj){
				    //console.log(key + "/" + valueObj.buying_format );
				    if( valueObj.buying_format == "Auction" ){
					if( gradeCompany == "SGC" ) {
						if( valueObj.title.indexOf("PSA") == -1 ) {
							cardCount++;
							moneyCount += valueObj.sale_price;
							console.log(key + "/" + valueObj.sale_price );
						}
					} else if( gradeCompany == "PSA" ) {
						if( valueObj.title.indexOf("SGC") == -1 ) {
							cardCount++;
							moneyCount += valueObj.sale_price;
							console.log(key + "/" + valueObj.sale_price );
						}
					}
				    }
				});
				moneyAvg = moneyCount / cardCount;
				var moneyAvgFormatted = "";
				if (isNaN(moneyAvg)) {
					moneyAvg = "<div id=no-data>No Data Available</div>";
					moneyAvgFormatted = moneyAvg;
				} else {
					moneyAvgFormatted = "$" + moneyAvg;
					moneyAvgFormatted = moneyAvgFormatted.substring(0, moneyAvgFormatted.indexOf(".") + 3);
				}
				
				
				population += "<div id=refined-avg>Refined Calculated Avg: " +  moneyAvgFormatted + "</div>";
				population += "<div id=auction-count>Number Of Recent Auctions: " + cardCount + "</div>";
				if( showingWaitingGif ) {
					$("#card-pop").html(population);
				} else {
					$("#card-pop").html($("#card-pop").html() + population);
				}
				showingWaitingGif = false;
			});
		 });
		 

		 $(".modal-overlay, .modal-close").click(function() {
			$(".modal").hide();
			$(".modal-overlay").hide();
			//clear details data
			$("#modal-content").html("");
		});
		
		
	<?php 
	  if( isset($_SESSION['user_id'])) { 	
	?>
	
			$(".grid-container").sortable({
				update: function (event, ui) {
				    var sortedIDs = $(this).sortable("toArray");
				    var dcID = $('#dc').text();
				    $.ajax({
					type: "POST",
					url: "update_order.php",
					data: { sortedIDs: sortedIDs, dc: dcID },
					success: function (response) {
					    //alert("Sort Order updated successfully!");
					   $('#status-msg-text').text("Status: Sort Order updated successfully");

					    setTimeout(function() { $('#status-msg-text').text("Status:"); },3000);

					},
					error: function (response) {
					    alert("Error updating order.");
					}
				    });
				}
			});
		
	    <?php
	      }  
	      ?>
		    //$(".grid-container").disableSelection();
        });
    </script>
</head>
<body>
	
	
<?php require 'header.php';?>

<?php
	
        // Database connection
	require 'db.php';

        try {
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    
	    $display_case_id = 0;
	    		    
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		 $display_case_id = $_POST['dc'];
	    }  else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		$display_case_id = $_GET['dc'];
	    }
	    if ( $display_case_id == 0) {
	    	$stmt = $conn->prepare("SELECT c.*, dc.* FROM card c, display_case dc WHERE dc.display_case_id = 0 ORDER BY c.player_name ASC");
	    	
	    } else if ( $display_case_id == -1) {
	    	$stmt = $conn->prepare("SELECT c.*, dc.* FROM card c, display_case dc WHERE dc.display_case_id = -1 ORDER BY c.grade_company, ABS(c.grade),c.player_name ASC");
	    	
	    } else {
            	$stmt = $conn->prepare("SELECT * FROM card c, display_case dc, display_case_cards dcc WHERE dc.display_case_id = :display_case_id AND dcc.display_case_id = dc.display_case_id AND c.card_id = dcc.card_id ORDER BY dcc.dcc_sort_order ASC");
            	$stmt->bindParam(':display_case_id', $display_case_id, \PDO::PARAM_INT);
            }
               
            
            $stmt->execute();
	    $myCtr = 1;
	    $jquery_script_string = '';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            	if($myCtr == 1) { 
            		echo '<span id="dc" style="font-size: 1px;">' . $display_case_id . '</span>';
            		if( isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin' ) {              			
				echo '<form >';
				echo '<div class="float-container"><div  id="click-title" class="float-child-left"  >Drag cards to rearrange the display</div>';
				echo '<div class="float-child-right">';
  			
  				echo'<a href="display_case_edit.php?dc=' . $display_case_id .'" style="font-size: 16px;"><img src="/images/edit-button-2.jpg" style="padding-bottom: 3px;width: 17px;vertical-align:middle;" border="0" />Edit Virtual Display</a>  &nbsp; | &nbsp; ';
  			
				echo 'Grid Columns ';
				$grid_sizes = array(4,6,8,10, 12);
				if (!in_array($row["display_case_grid_size"] , $grid_sizes)) {
					echo '(current: ' . $row["display_case_grid_size"] . ') ';
				}
				echo '<input type="button" class="grid-button" id="grid-4" value="4" /> ';
				echo '<input type="button" class="grid-button" id="grid-6" value="6" /> ';
				echo '<input type="button" class="grid-button" id="grid-8" value="8" /> ';
				echo '<input type="button" class="grid-button" id="grid-10" value="10" /> ';
				echo '<input type="button" class="grid-button" id="grid-12" value="12" /> ';
				echo '<input type="button" class="grid-button" id="grid-14" value="14" />';
				echo '</div></div></form>';
            		} else {
            			echo '<div id="click-title">Click on a card to view details</div>';
            		}
            		
            		//Title Plate
            		echo '<div id="display-case-top" style="background-color: '. $row['display_case_color'] .';">';
            		echo '<div id="display-case-title-plate" style="background-color: '. $row['display_case_color'] .';">';
            		echo '<div id="display-case-title" style="font-family: ' . $row["display_case_font"] . '; color: ' . $row["display_case_font_color"] . '; text-shadow: 2px 2px 5px ' . $row["display_case_font_shadow"] . ' ;">' . $row['display_case_name'] . '</div>';
            		echo '</div>';
            		echo '</div>';
            		
            		//Disable current grid-size button
			echo '<script>$("#grid-"+' . $row["display_case_grid_size"] . ').css("background-color", "lightblue"); $("#grid-"+'. $row["display_case_grid_size"] .').prop("disabled",true); $("#display-case-title").css("background-color", "' . $row['display_case_title_bg_color'] . '");</script>';
			
            		echo '<div id="status-msg" style="position: fixed; bottom: 0; right: 0;background-color: gray;"><div id="status-msg-text" style="background-color: gray;">Status:</div></div>';
            		echo '<div class="grid-container" style="color: green; font-weight: bold; background-color: ' . $row['display_case_color'] .';';
            		echo '  grid-template-columns: repeat( ' . $row['display_case_grid_size'] . ', minmax(20px, 1fr));">';
            		
            	}
            	//Display each card
                
                if ( $row['grade_company'] == 'PSA' ) {
                	echo '<div class="grid-item" style="padding-bottom: 0px; margin-bottom: 0px;" ';
                	//Details for modal popup window
                	echo ' id="' . $row['card_id'] . '" ';
                	echo ' data-grade-company="' .$row['grade_company']  . '" ';
                	echo 'data-serial="' .$row['grade_serial'] . '" ';
                	echo 'data-player-name="' .$row['player_name'] . '" ';
                	echo 'data-year="' .$row['year'] . '" ';
                	echo 'data-brand="' .$row['brand'] . '" ';
                	echo 'data-number="' .$row['number'] . '" ';
                	echo 'data-grade="' .$row['grade'] . '" ';
                	
                	//data-details contains the html that will be displayed in the modal popup window when a card is clicked.
                	echo 'data-details="<div style=&quot;float: left;&quot;>';
                	echo '<img src=&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;';
                	echo ' style=&quot;filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; width: 350px; &quot; ></div>';
                	echo '<div id=card-details><div id=card-info>' . $row['year'] . ' ' . $row['brand'] . ' #' . $row['number'] . '<br>' . $row['player_name'] . '<br>' . $row['grade_company'] . ' Grade ' . $row['grade'] . '<br>' . $row['grade_company'] . ' Serial #' . $row['grade_serial'] . '</div><div id=flip-button><a class=&quot;link-button&quot;>Flip Card</a></div><div id=beta>Insights (Beta)</div><div id=&quot;dynamic-details&quot;><div id=&quot;card-pop&quot;><img src=&quot;/images/waiting.gif&quot; style=&quot;height: 40px; margin: 0px 0px 0px 80px;&quot; ></div></div></div>">';
                	
                	
                	//display with padding/margin because PSA slabs are smaller but the scans are similar size as SGC
                	echo '<img id="card-img-' . $row['card_id'] . '" src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.91, 0.91); ">';

                } elseif ( $row['grade_company'] == 'SGC' ) {
                	echo '<div class="grid-item" ';
                     //Data for modal popup window
                	echo ' id="' . $row['card_id'] . '" ';
                	echo 'data-serial="' .$row['grade_serial'] . '" ';
                	echo 'data-player-name="' .$row['player_name'] . '" ';
                	echo 'data-year="' .$row['year'] . '" ';
                	echo 'data-brand="' .$row['brand'] . '" ';
                	echo 'data-number="' .$row['number'] . '" ';
                	echo 'data-grade="' .$row['grade'] . '" ';
                	echo 'data-img-back-url="' .$row['img_back_url'] . '" ';
                	
                     //Modal window data: "data-details" contains the html that will be displayed in the modal popup window when a card is clicked.
                	echo 'data-details="';
                		echo '<div id=card-wrapper style=&quot;position: relative; height: 560px;width:355px;float: left;&quot;>';
 		     //Card image
 			echo '<img id=modal-card-img data-rotation=&quot;0&quot; src=&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;';
			     //Check which folder the card image resides in and adjust the brightness and contrast as needed, for those images 
				if ( str_contains( $row['img_url'], 'SGC-Scans-web') ) {
					echo ' style=&quot;filter: brightness( 120% ) contrast( 140% ); width: 350px;position: absolute;&quot;></div>';
				} else {
					echo ' style=&quot;filter: brightness( 115% ) contrast( 75% ); width: 350px;position: absolute;&quot;></div>';
				}
		     //Card details from the database
                	echo '<div id=card-details><div id=card-info>' . $row['year'] . ' ' . $row['brand'] . ' #' . $row['number'] . '<br>' . $row['player_name'] . '<br>' . $row['grade_company'] . ' Grade ' . $row['grade'] . '<br>' . $row['grade_company'] . ' Serial #' . $row['grade_serial'] . '</div>';
			     //check if there is an image of the card's back
				if( $row['img_back_url'] == 'default.jpg' ) {
					//show a disabled 'Flip Card' link
					echo '<div id=no-flip-button><a class=&quot;link-button&quot;>Flip Card</a></div>';
					echo '<div id=rotate-button><a class=&quot;link-button&quot; style=&quot;background-color: lightgray;&quot;>Rotate Card</a></div>';
				} else {
					//show enabled 'Flip Card' button
					echo '<div id=flip-button ><button class=&quot;link-button flip-button-class&quot; >Flip Card</button></div>';
					echo '<div id=rotate-button><button class=&quot;link-button rotate-button-class&quot; >Rotate Card</button></div>';
				}
				echo '<div id=beta>Insights (Beta)</div><div id=&quot;dynamic-details&quot;><div id=&quot;card-pop&quot;><img src=&quot;/images/waiting.gif&quot; style=&quot;height: 40px; margin: 0px 0px 0px 80px;&quot; ></div></div></div>';
				//jquery script toggles the modal window's card image front/back
				echo '<script> ';
				echo '$(&quot;.flip-button-class&quot;).click(function() {
						if( $(&quot;#modal-card-img&quot;).attr(&quot;src&quot;).indexOf(&quot;' . $row['img_url'] . '&quot;) >= 0 ){
							$(&quot;#modal-card-img&quot;).attr(&quot;src&quot;,&quot;/images/Baseball-Cards/' . $row['img_back_url'] . '&quot;); 
						} else {
							$(&quot;#modal-card-img&quot;).attr(&quot;src&quot;,&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;);
						}
					});';
				echo '$(&quot;.rotate-button-class&quot;).click(function() {
						$(&quot;#modal-card-img&quot;).css({
							//TODO: This is incomplete for offbeat browsers
						        &quot;-webkit-transform&quot;: &quot;rotate(90deg)&quot;,
						        &quot;-moz-transform&quot;: &quot;rotate(90deg)&quot;
    						});	
						
    						if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;0&quot; ) {
								$(&quot;#card-wrapper&quot;).width(550);
								$(&quot;#card-wrapper&quot;).height(350);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;90&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;-95px&quot;,
									&quot;left&quot;: &quot;100px&quot;,
									&quot;transform&quot;: &quot;rotate(90deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;90&quot; ){
								$(&quot;#card-wrapper&quot;).width(350);
								$(&quot;#card-wrapper&quot;).height(550);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;180&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;0px&quot;,
									&quot;left&quot;: &quot;0px&quot;,
									&quot;transform&quot;: &quot;rotate(180deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;180&quot; ) {
								$(&quot;#card-wrapper&quot;).width(550);
								$(&quot;#card-wrapper&quot;).height(350);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;270&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;-95px&quot;,
									&quot;left&quot;: &quot;100px&quot;,
									&quot;transform&quot;: &quot;rotate(270deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;270&quot; ){
								$(&quot;#card-wrapper&quot;).width(350);
								$(&quot;#card-wrapper&quot;).height(550);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;0&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;0px&quot;,
									&quot;left&quot;: &quot;0px&quot;,
									&quot;transform&quot;: &quot;rotate(0deg)&quot;
								});
						}
    						
					});';
				echo '</script>">';
                     
                     //display the card
                	echo '<img id="card-img-' . $row['card_id'] . '" src="/images/Baseball-Cards/' . $row['img_url'] . '" ';
                	if ( str_contains( $row['img_url'], 'SGC-Scans-web') ) {
				echo ' style="filter: brightness( 120% ) contrast( 140% ); object-position: 50% 35%;">';
			} else {
				echo ' style="filter: brightness( 115% ) contrast( 75% ); object-position: 50% 35%;">';
                	}
                } elseif ( $row['grade_company'] == 'raw' ) {
                	echo '<div class="grid-item" style="padding-bottom: 0px; margin-bottom: 0px;" ';
                     //Data for modal popup window
                	echo ' id="' . $row['card_id'] . '" ';
                	echo ' data-grade-company="' .$row['grade_company']  . '" ';
                	echo 'data-serial="' .$row['grade_serial'] . '" ';
                	echo 'data-player-name="' .$row['player_name'] . '" ';
                	echo 'data-year="' .$row['year'] . '" ';
                	echo 'data-brand="' .$row['brand'] . '" ';
                	echo 'data-number="' .$row['number'] . '" ';
                	echo 'data-grade="' .$row['grade'] . '" ';
                	echo 'data-img-back-url="' .$row['img_back_url'] . '" ';
                	
                     //Modal window data: "data-details" contains the html that will be displayed in the modal popup window when a card is clicked.
                	echo 'data-details="';
                		echo '<div id=card-wrapper style=&quot;position: relative; height: 560px;width:355px;float: left;&quot;>';
                	     //Card image
                		echo '<img id=modal-card-img data-rotation=&quot;0&quot; src=&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;';
                		echo ' style=&quot;filter: brightness( 120% ) contrast( 130% );  width: 350px; position: absolute;&quot;></div>';
                   	     //Card details from the database
                		echo '<div id=card-details><div id=card-info>' . $row['year'] . ' ' . $row['brand'] . ' #' . $row['number'] . '<br>' . $row['player_name'] . '<br>' . $row['grade_company'] . ' Grade ' . $row['grade'] . '<br>' . $row['grade_company'] . ' Serial #' . $row['grade_serial'] . '</div>'; 	
                	     //check if there is an image of the card's back
				if( $row['img_back_url'] == 'default.jpg' ) {
					//show a disabled 'Flip Card' link
					echo '<div id=no-flip-button><a class=&quot;link-button&quot;>Flip Card</a></div>';
					echo '<div id=rotate-button><a class=&quot;link-button&quot; style=&quot;background-color: lightgray;&quot;>Rotate Card</a></div>';
				} else {
					//show enabled 'Flip Card' button
					echo '<div id=flip-button ><button class=&quot;link-button flip-button-class&quot; >Flip Card</button></div>';
					echo '<div id=rotate-button><button class=&quot;link-button rotate-button-class&quot; >Rotate Card</button></div>';
				}
				echo '<div id=beta>Insights (Beta)</div><div id=&quot;dynamic-details&quot;><div id=&quot;card-pop&quot;><img src=&quot;/images/waiting.gif&quot; style=&quot;height: 40px; margin: 0px 0px 0px 80px;&quot; ></div></div></div>';
				//jquery script toggles the modal window's card image front/back
				echo '<script> ';
				echo '$(&quot;.flip-button-class&quot;).click(function() {
						if( $(&quot;#modal-card-img&quot;).attr(&quot;src&quot;).indexOf(&quot;' . $row['img_url'] . '&quot;) >= 0 ){
							$(&quot;#modal-card-img&quot;).attr(&quot;src&quot;,&quot;/images/Baseball-Cards/' . $row['img_back_url'] . '&quot;); 
						} else {
							$(&quot;#modal-card-img&quot;).attr(&quot;src&quot;,&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;);
						}
					});';
				echo '$(&quot;.rotate-button-class&quot;).click(function() {
						$(&quot;#modal-card-img&quot;).css({
							//TODO: This is incomplete for offbeat browsers
						        &quot;-webkit-transform&quot;: &quot;rotate(90deg)&quot;,
						        &quot;-moz-transform&quot;: &quot;rotate(90deg)&quot;
    						});
    						
    						if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;0&quot; ) {
								$(&quot;#card-wrapper&quot;).width(465);
								$(&quot;#card-wrapper&quot;).height(350);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;90&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;-50px&quot;,
									&quot;left&quot;: &quot;55px&quot;,
									&quot;transform&quot;: &quot;rotate(90deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;90&quot; ){
								$(&quot;#card-wrapper&quot;).width(350);
								$(&quot;#card-wrapper&quot;).height(465);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;180&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;0px&quot;,
									&quot;left&quot;: &quot;0px&quot;,
									&quot;transform&quot;: &quot;rotate(180deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;180&quot; ) {
								$(&quot;#card-wrapper&quot;).width(465);
								$(&quot;#card-wrapper&quot;).height(350);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;270&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;-50px&quot;,
									&quot;left&quot;: &quot;55px&quot;,
									&quot;transform&quot;: &quot;rotate(270deg)&quot;
								});
						} else if( $(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;) == &quot;270&quot; ){
								$(&quot;#card-wrapper&quot;).width(350);
								$(&quot;#card-wrapper&quot;).height(465);
								$(&quot;#modal-card-img&quot;).data(&quot;rotation&quot;, &quot;0&quot;) 
								$(&quot;#modal-card-img&quot;).css({
									&quot;top&quot;: &quot;0px&quot;,
									&quot;left&quot;: &quot;0px&quot;,
									&quot;transform&quot;: &quot;rotate(0deg)&quot;
								});
						}
					});';
				echo '</script>">';
                	
                     //display the card
                	echo '<img id="card-img-' . $row['card_id'] . '" src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.865, 0.865); ">';

		} elseif ( $row['grade_company'] == 'GMA' ) {
			echo '<div class="grid-item" id="' . $row['card_id'] . '" >';
                	echo '<img src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" ';
                	echo ' style="filter: brightness( 120% ) contrast( 120% ); object-position: 50% 60%; transform: scale(0.96, 0.96);">';
                	
                } elseif ( $row['grade_company'] == 'HGR' ) {
			echo '<div class="grid-item" id="' . $row['card_id'] . '" >';
                	echo '<img src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 120% ); object-position: 50% 35%; transform: scale(0.98, 0.98);">';
                } else {
                	echo '<div class="grid-item" id="' . $row['card_id'] . '">';
                	echo '<img src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" style="">';
                }
                echo '</div>';
                $myCtr++;
            }
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
         $conn = null;
         
         echo $jquery_script_string;
        ?>
    </div>
    
        <div class="modal-overlay"></div>
        <div class="modal">
        	<button class="link-button modal-close" >X</button>
            	<div id="modal-content"></div>
            
    </div>
    <?php require 'footer.php';?>
	
</body>
</html>
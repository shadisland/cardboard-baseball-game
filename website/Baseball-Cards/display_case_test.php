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
	    font-weight: bold;
	    font-size: 25px;
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
            padding: 20px;
            background: #fff;
            z-index: 1000;
           
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
        #modal-content {
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
			var gradeCompany = $(this).data("grade-company");
			var playerName = $(this).data("player-name");
			var year = $(this).data("year");
			var manufacturer = $(this).data("brand");
			var cardNumber = $(this).data("number");
			var grade = $(this).data("grade");
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
				var population = ' <br>Average Ebay Auction Price<br><span style="text-decoration: line-through;">API Calculated Avg: $' + response.average_price + '</span>';

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
				var moneyAvgFormatted = moneyAvg + "";
				moneyAvgFormatted = moneyAvgFormatted.substring(0, moneyAvgFormatted.indexOf(".") + 3);
				population += "<br>Refined Calculated Avg: $" +  moneyAvgFormatted + "<br>Number Of Auctions: " + cardCount;
				$("#card-pop").html(population);
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
	$host = 'p3nlmysql39plsk.secureserver.net';
	$db   = 'ph21100054196_';
	$user = 'collector';
	$pass = 'Piltocat22';
	$port = "3306";
	$charset = 'utf8mb4';

	$options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,\PDO::ATTR_EMULATE_PREPARES => false,];

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
	// $pdo = new \PDO($dsn, $user, $pass, $options);

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
            		if( isset($_SESSION['user_id'])) { 
				echo '<form >';
				echo '<div class="float-container"><div class="float-child-left" style="font-size: 18px;" >Drag cards to rearrange the display</div>';
				echo '<div class="float-child-right">';
  			
  				echo'<a href="display_case_edit.php?dc=' . $display_case_id .'" style="font-size: 16px;"><img src="/images/edit-button-2.jpg" style="padding-bottom: 3px;width: 17px;vertical-align:middle;" border="0" />Edit Display Case</a>  &nbsp; | &nbsp; ';
  			
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
                	echo 'data-details="<div style=&quot;float: left;&quot;>';
                	echo '<img src=&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;';
                	echo ' style=&quot;filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; width: 350px; &quot; ></div>';
                	echo '<div style=&quot;float: right; padding-left: 10px;&quot;>' . $row['year'] . ' ' . $row['brand'] . ' #' . $row['number'] . '<br>' . $row['player_name'] . '<br>' . $row['grade_company'] . ' Grade ' . $row['grade'] . '<br>' . $row['grade_company'] . ' Serial #' . $row['grade_serial'] . '<br><span id=&quot;card-pop&quot;></span></div>">';
                	
                	//display with padding/margin because PSA cases are smaller but the scans are similar size as SGC
                	echo '<img id="card-img-' . $row['card_id'] . '" src="/images/Baseball-Cards/' . $row['img_url'] . '" alt="Baseball Card" style="filter: brightness( 120% ) contrast( 130% ); object-position: 50% 100%; transform: scale(0.91, 0.91); ">';
                	//dynamic jquery goes here 
                	//$jquery_script_string = $jquery_script_string . '<script> </script>';

                } elseif ( $row['grade_company'] == 'SGC' ) {
                	echo '<div class="grid-item" ';
                	echo ' id="' . $row['card_id'] . '" ';
                	echo ' data-grade-company="' .$row['grade_company']  . '" ';
                	echo 'data-serial="' .$row['grade_serial'] . '" ';
                	echo 'data-player-name="' .$row['player_name'] . '" ';
                	echo 'data-year="' .$row['year'] . '" ';
                	echo 'data-brand="' .$row['brand'] . '" ';
                	echo 'data-number="' .$row['number'] . '" ';
                	echo 'data-grade="' .$row['grade'] . '" ';
                	echo 'data-details="<div style=&quot;float: left;&quot;>';
 			echo '<img src=&quot;/images/Baseball-Cards/' . $row['img_url'] . '&quot;';
                	
                	if ( str_contains( $row['img_url'], 'SGC-Scans-web') ) {
                		echo ' style=&quot;filter: brightness( 120% ) contrast( 140% ); object-position: 50% 35%;object-position: 50% 100%; width: 350px;&quot;></div>';
                	} else {
                		echo ' style=&quot;filter: brightness( 115% ) contrast( 75% ); object-position: 50% 35%;object-position: 50% 100%; width: 350px;&quot;></div>';
                	}
                	echo '<div style=&quot;float: right; padding-left: 10px;&quot;>' . $row['year'] . ' ' . $row['brand'] . ' #' . $row['number'] . '<br>' . $row['player_name'] . '<br>' . $row['grade_company'] . ' Grade ' . $row['grade'] . '<br>' . $row['grade_company'] . ' Serial #' . $row['grade_serial'] . '<br><span id=&quot;card-pop&quot;></span></div>">';
                	echo '<img id="card-img-' . $row['card_id'] . '" src="/images/Baseball-Cards/' . $row['img_url'] . '" ';
                	if ( str_contains( $row['img_url'], 'SGC-Scans-web') ) {
				echo ' style="filter: brightness( 120% ) contrast( 140% ); object-position: 50% 35%;">';
			} else {
				echo ' style="filter: brightness( 115% ) contrast( 75% ); object-position: 50% 35%;">';
                	}
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
        	<button class="link-button modal-close" style="float: right;margin: 0px 0px 10px 0px; font-size: 22px;">X</button>
            	<div id="modal-content"></div>
            
    </div>
    <?php require 'footer.php';?>
	
</body>
</html>
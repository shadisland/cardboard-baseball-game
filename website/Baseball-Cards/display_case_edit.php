<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Virtual Display</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <?php require 'head_scripts_include.php';?>
    
    <style>
    ul {
  	list-style-type: none;
    }
    li {
    	margin: 0px 0px 5px 0px;
    }
    input[type=submit] {
    	background: #0066A2;color: white;border-style: outset;border-color: #0066A2;
    	height: 25px;
    	width: auto;font: bold15px arial,sans-serif;text-shadow: none;
    	border:0 none;
	cursor:pointer;
	-webkit-border-radius: 5px;
    	border-radius: 5px;
    }

    </style>
    <script>
    
   
        $(document).ready(function(){
        
            $('#addCardForm').on('submit', function(e){
                e.preventDefault();
                 //temporarily disable Add button
		$(this).find(":submit").attr('disabled', 'disabled');
                $.ajax({
                    type: "POST",
                    url: "add_display_case_card.php",
                    data: $(this).serialize(),
                    success: function(response){
                        //alert(response);   
                         $('#change-notification').text(response).fadeIn("slow").delay(2000).fadeOut("slow", function () {
			        setTimeout(function () {
			            location.reload();
			        }, 1000);
			});
                        
                    },
                    error: function(){
                        alert("Error adding card.");
                    }
                });
            });

            $('.removeCardForm').on('submit', function(e){
                e.preventDefault();
		$(this).find(":submit").attr('disabled', 'disabled');
                $.ajax({
                    type: "POST",
                    url: "remove_display_case_card.php",
                    data: $(this).serialize(),
                    success: function(response){
                        //alert(response);
                         $('#change-notification').text(response).fadeIn("slow").delay(2000).fadeOut("slow", function () {
				setTimeout(function () {
					location.reload();
				}, 1000);
			});
                        //location.reload();
                    },
                    error: function(){
                        alert("Error removing card.");
                    }
                });
            });
            
            $('#updateNameForm').on('submit', function(e){
	                    e.preventDefault();
	    		$(this).find(":submit").attr('disabled', 'disabled');
	                    $.ajax({
	                        type: "POST",
	                        url: "update_display_case_name.php",
	                        data: $(this).serialize(),
	                        success: function(response){
	                            //alert(response);
	                             $('#change-notification').text(response).fadeIn("slow").delay(2000).fadeOut("slow", function () {
				    	setTimeout(function () {
				    		location.reload();
				    	}, 1000);
				      });
	                        },
	                        error: function(){
	                            alert("Error updating name.");
	                        }
	                    });
            });
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
$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get display case
        //$display_case_id = 1; 
        $display_case_id = $_GET['dc'];
        
        $stmt = $pdo->prepare("SELECT * FROM display_case WHERE display_case_id = :display_case_id");
        $stmt->execute(['display_case_id' => $display_case_id]);
        $display_case = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get display case's cards
        $stmt = $pdo->prepare("SELECT c.* FROM card c JOIN display_case_cards dcc ON c.card_id = dcc.card_id WHERE dcc.display_case_id = :display_case_id ORDER BY c.year, c.brand, c.number, c.player_name ASC");
        $stmt->execute(['display_case_id' => $display_case_id]);
        $display_case_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all cards for drop-down list
        $stmt = $pdo->prepare("SELECT c.* FROM card c WHERE card_id not in ( select dcc.card_id from display_case_cards dcc where dcc.display_case_id = :display_case_id ) ORDER BY c.year, c.brand, c.number, c.player_name ASC ");
        $stmt->execute(['display_case_id' => $display_case_id]);
        $all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    if ( $display_case == null) {
    	echo '<h1>[URL PROBLEM] OOPS! Incorrect or Missing dc value</h1>';
    }
    ?>

 
    <h2>Edit Virtual Display: "<a href="display_case_cards.php?dc=<?php echo $display_case_id; ?>" style="text-decoration: underline;"><?php echo htmlspecialchars($display_case['display_case_name']); ?></a>"</h2>
  
 <div style="height: 25px;"><div id="change-notification" style="color: green; font-weight: bold;">&nbsp;</div></div>
  
 <form id="updateNameForm">
        Name: <input type="text" name="display_case_name" value="<?php echo htmlspecialchars($display_case['display_case_name']); ?>" required>
        <br/>
        Color: <input type="text" name="display_case_color" style="width: 140px;" value="<?php echo htmlspecialchars($display_case['display_case_color']); ?>" required> (white, black, #CCCCCC, etc.) 
        <br/>
        Grid Columns: <input type="text" name="display_case_grid_size" style="width: 40px;" value="<?php echo htmlspecialchars($display_case['display_case_grid_size']); ?>" required> 
        <br/>
        Font Family: <input type="text" name="display_case_font" style="width: 140px;" value="<?php echo htmlspecialchars($display_case['display_case_font']); ?>" required> (See font list below)
        <br/>
        Font Color: <input type="text" name="display_case_font_color" style="width:1 40px;" value="<?php echo htmlspecialchars($display_case['display_case_font_color']); ?>" required>  (white, black, #CCCCCC, etc.) 
        <br/>
        Font Shadow Color <input type="text" name="display_case_font_shadow" style="width: 140px;" value="<?php echo htmlspecialchars($display_case['display_case_font_shadow']); ?>" required>  (white, black, #CCCCCC, etc.) 
        <br/>
        <input type="hidden" name="display_case_id" value="<?php echo $display_case_id; ?>">
        <input type="submit" style="margin-top: 5px;" value="Update Virtual Display Settings">
    </form>
 <hr/>
 
 <?php if ( $display_case_id < 1) {
  	echo '<!-- ';
 }
 ?>
   <h3>Add A Card To This Virtual Display</h3>
     <form id="addCardForm">
         <label for="card">Available Cards: </label>
         <select id="card" name="card_id" required>
             <?php foreach ($all_cards as $card): ?>
                 <option value="<?php echo htmlspecialchars($card['card_id']); ?>"><?php echo htmlspecialchars($card['year']) . ' ' . htmlspecialchars($card['brand']) . ' #' . htmlspecialchars($card['number']) . ' ' . htmlspecialchars($card['player_name']); ?></option>
             <?php endforeach; ?>
         </select>
         <input type="hidden" name="display_case_id" value="<?php echo $display_case_id; ?>">
         <input type="submit" value="Add">
    </form>
    <hr/>
    <h3>Currently Displayed Cards</h3>
    
    <ul>
        <?php foreach ($display_case_cards as $card): ?>
            <li> 
                <form class="removeCardForm" style="display:inline;">
                    <input type="hidden" name="display_case_id" value="<?php echo $display_case_id; ?>">
                    <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                    <input type="submit" value="Remove">
                </form>
                <?php echo htmlspecialchars($card['year']) . ' ' . htmlspecialchars($card['brand']) . ' #' . htmlspecialchars($card['number']) . ' ' . htmlspecialchars($card['player_name']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
     <?php if ( $display_case_id == 0) {
      	echo '--> ';
     }
 ?>

<br/>
Copy & Paste a font name into the "Font Family" field above.

<div style="margin-top: 10px; max-width: 850px; height: 500px;">
	<div style="float: left;">
	
		Standard web fonts: 
		<ul style="margin-top: 5px; padding-left: 25px;">        
		    <li style="font-family: Arial; font-size: 22px;">Arial</li>
		    <li style="font-family: Verdana; font-size: 22px;">Verdana</li>
		    <li style="font-family: Tahoma; font-size: 22px;">Tahoma</li>
		    <li style="font-family: Trebuchet MS; font-size: 22px;">Trebuchet MS</li>
		    <li style="font-family: Times New Roman; font-size: 22px;">Times New Roman</li>
		    <li style="font-family: Georgia; font-size: 22px;">Georgia</li>
		    <li style="font-family: Garamond; font-size: 22px;">Garamond</li>
		    <li style="font-family: Courier New; font-size: 22px;">Courier New</li>
		    <li style="font-family: Brush Script MT; font-size: 22px;">Brush Script MT (cursive)</li>
		</ul>
	</div>
	<div style="float: right;">
		Installed fonts: 
		<ul style="margin-top: 5px; padding-left: 25px;">
		    <li style="font-family: LHF_Ballpark_Script; font-size: 22px;">LHF_Ballpark_Script</li>
		    <li style="font-family: Bosox_Full; font-size: 22px;">Bosox_Full</li>
		    <li style="font-family: Eurostile_MN_Extended_Bold; font-size: 22px;">Eurostile_MN_Extended_Bold</li>
		    <li style="font-family: The_Blendhes; font-size: 22px;">The_Blendhes</li>
		    <li style="font-family: BlacklightD_Regular; font-size: 22px;">BlacklightD_Regular</li>
		    <li style="font-family: FabioloSmallCap-Light; font-size: 22px;">FabioloSmallCap-Light</li>
		    <li style="font-family: Adequate-ExtraLight; font-size: 22px;">Adequate-ExtraLight</li>
		</ul>
	</div>
</div>

<?php require 'footer.php';?>
  
</body>
</html>

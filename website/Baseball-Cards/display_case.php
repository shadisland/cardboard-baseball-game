<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Baseball Card Virtual Displays</title>
    
    <style>
        .grid-container {
            display: flex;
            flex-wrap: wrap;
        }
        .grid-item {
        <?php
        if( isset($_SESSION['user_id'])) { 
        	echo ' width: 575px;';
        } else {
        	echo ' width: 500px;';
        }
        ?>
            
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .grid-item img {
            width: 100%;
            height: auto;
        }
    </style>
     <link rel="stylesheet" href="/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" />
    
    <?php require 'head_scripts_include.php';?>
    
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
            
            $(".grid-button").click(function(e) {
	    		    e.preventDefault();
	    		    var dcID = $(this).attr("href");
	    		    if (confirm('Are you sure you want to delete this display?')) {
				    $.ajax({
					type: "POST",
					url: "delete_display_case.php",
					data: { 
					    dc: dcID	  
					},
					success: function(response) {
					    //alert('ok');
					   $('#status-msg-text').text(response).fadeIn("slow").delay(2000).fadeOut("slow", function () {
					   	setTimeout(function () {
					   	         location.reload();
			        		}, 1000);
			        	   });
					},
					error: function(response) {
					    alert('error');
					}
				    });
				}
		});
	<?php 
	if( isset($_SESSION['user_id'])) { 	
	?>
            $(".grid-container").sortable({
                update: function (event, ui) {
                    var sortedIDs = $(this).sortable("toArray");
                    
                    $.ajax({
                        type: "POST",
                        url: "update_display_case_order.php",
                        data: { sortedIDs: sortedIDs},
                        success: function (response) {
                            //alert("Order updated successfully!");
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
            $(".grid-container").disableSelection();
        });
    </script>
    
    
    
</head>
<body>

<?php require 'header.php';?>
 
	<h2>Choose A Virtual Display</h2>
<?php
	if( isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { 
		echo '<div>Drag cells to rearrange the order</div>';
	}
?>

    <div class="grid-container">
   	<?php
        // Database connection
    	require 'db.php';

        try {
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT dc.display_case_id, dc.display_case_name, dc.display_case_sort_order, dc.display_case_font, dc.display_case_font_color, dc.display_case_font_shadow, count(DISTINCT dcc.card_id ) as CardCount FROM display_case dc, display_case_cards dcc WHERE dc.display_case_id = dcc.display_case_id  GROUP BY dc.display_case_id,dc.display_case_name,dc.display_case_font, dc.display_case_font_color, display_case_font_shadow  UNION DISTINCT SELECT dc2.display_case_id, dc2.display_case_name, dc2.display_case_sort_order, dc2.display_case_font, dc2.display_case_font_color, dc2.display_case_font_shadow, 0 as CardCount FROM display_case dc2  WHERE dc2.display_case_id NOT IN (Select distinct display_case_id from display_case_cards) ORDER BY display_case_sort_order, display_case_name ASC");
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ( $row['display_case_id'] < 1) {
                	echo '<div class="grid-item" style="background-color: #d8f7ff;" id="' . $row['display_case_id'] . '">';
                } else{
                	echo '<div class="grid-item" style="background-color: #fcfcfc;" id="' . $row['display_case_id'] . '">';
                }
                
                if( isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin' ) { 
                	echo '<img src="/images/dots-six-vertical-bold.svg" style="float: left; width: 30px; height: 30px; cursor:move;"> ';
                }
                echo '<a href="display_case_cards.php?dc='. $row['display_case_id'] . '"><span style="color: ' . $row['display_case_font_color'] . '; font-size: 30px; font-family: '. $row['display_case_font']  .'; text-shadow: ' . $row['display_case_font_shadow'] . ' 1px 0 3px;">' . $row['display_case_name'] . '</span></a> &nbsp; <br>';
                if ( $row['display_case_id'] < 1) {
                	echo ' &nbsp; | &nbsp; ';
                } else {
                	echo '(' . $row['CardCount'] . ' cards) &nbsp; | &nbsp; ';
                }
                echo '<a href="display_case_cards.php?dc='. $row['display_case_id'] . '">View</a> ';
                if( isset($_SESSION['user_role'])) { 
                	if( $_SESSION['user_role'] == 'admin' ) {
                		echo '&nbsp; | &nbsp; <a href="display_case_edit.php?dc='. $row['display_case_id'] . '">Edit</a>';
                		echo '&nbsp; | &nbsp; <a class="link-button grid-button" style="background-color: lightblue;" href="'. $row['display_case_id'] . '">Delete</a>';
                	}
                }
                echo '</div>';
            }
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn = null;
        ?>
    </div>
    <div id="status-msg" style="position: fixed; bottom: 0; right: 0;background-color: gray;"><div id="status-msg-text" style="background-color: gray; color: white;">Status:</div></div>
    
      <?php require 'footer.php';?>
      
</body>
</html>
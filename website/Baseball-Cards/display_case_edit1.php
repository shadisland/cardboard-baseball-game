<!DOCTYPE html>
<html>
<head>
    <title>Baseball Cards Edit Display Case</title>
    <style>
        .grid-container {
            display: flex;
            flex-wrap: wrap;
        }
        .grid-item {
            width: 300px;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
            $(document).ready(function(){
                $('#submitForm').on('submit', function(e){
                    e.preventDefault();
    
                    $.ajax({
                        type: "POST",
                        url: "update_display_case.php",
                        data: $(this).serialize(),
                        success: function(response){
                            alert(response);
                            //$('#submitForm')[0].reset();
                        },
                        error: function(){
                            alert("Error submitting data.");
                        }
                    });
                });
            });
    </script>
</head>
<body>
	<h1>Baseball Cards Edit Display Case</h1>
	
    <div class="nav"><a href="display_case.php">Display Cases</a> </div>
    
      <form id="submitForm" >
           
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
echo '<div class="gothere"></div>';
        try {
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM  display_case dc WHERE dc.display_case_id = :display_case_id ");
            $display_case_id = 0;
 	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    		 $display_case_id = $_POST['dc'];
	    }  else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	    	$display_case_id = $_GET['dc'];
	    }
	    
            $stmt->bindParam(':display_case_id', $display_case_id, \PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            	 echo '<label for="display_case_name">Name:</label>';
            	echo '<input type="hidden" name="display_case_id" value="' . $row['display_case_id'] . '"/>';
		echo '<input type="text" id="display_case_name" name="display_case_name" value="' . $row['display_case_name'] . '" required><br><br>';
		echo '<label for="display_case_sort_order">Display Case Sort Order:</label>';
		echo '<input type="text" id="display_case_sort_order" name="display_case_sort_order" value="' . $row['display_case_sort_order'] . '" required><br><br>';
			           
	      	
            }
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
       echo '<input type="submit" value="Submit" >';
       echo '</form></div>';
       echo '<div >Year | Brand | Number | Player Name</div>';
       $stmt = $conn->prepare("SELECT * FROM card c, display_case_cards dcc WHERE dcc.display_case_id = :display_case_id AND c.card_id = dcc.card_id ORDER BY dcc.dcc_sort_order ASC");
                   
                  $stmt->bindParam(':display_case_id', $display_case_id, \PDO::PARAM_INT);
                  $stmt->execute();
       	    
       	    	$myCtr = 1;
                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                   	echo '<input type="hidden" name="card_id" value="' . $row['card_id'] . '"/>';
       			echo  $row['year'] . ' ' . $row['brand'] . ' #' . $row['number']. ' ' . $row['player_name'] . '<br><br>';
       		
                   }
              
       
       
       $conn = null;
       
        ?>
        
	           
	       
    
    <div>
    <h2>Coming soon: Ask ChatGPT for code that will Add/Remove Cards to this Display Case</h2>
    </div>
</body>
</html>
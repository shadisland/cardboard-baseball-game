<!DOCTYPE html>
<html>
<head>
    <title>Person's Clothing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#addClothingForm').on('submit', function(e){
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "add_display_case_card.php",
                    data: $(this).serialize(),
                    success: function(response){
                        alert(response);
                        location.reload();
                    },
                    error: function(){
                        alert("Error adding clothing.");
                    }
                });
            });
        });
    </script>
</head>
<body>
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


    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get person
        $display_case_id = 1; // Assume we are working with person with ID 1 for this example
        
        $stmt = $pdo->prepare("SELECT * FROM display_case WHERE display_case_id = :display_case_id");
        $stmt->execute(['display_case_id' => $display_case_id]);
        $display_case = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get display case's cards
        $stmt = $pdo->prepare("SELECT c.player_name FROM display_case_cards dcc, card c WHERE dcc.card_id = c.card_id AND dcc.display_case_id = :display_case_id");
        $stmt->execute(['display_case_id' => $display_case_id]);
        $display_case_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all cards for drop-down list
        $stmt = $pdo->prepare("SELECT * FROM card ORDER BY player_name");
        $stmt->execute();
        $all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    ?>

    <h2><?php echo htmlspecialchars($display_case['display_case_name']); ?>'s Cards</h2>

    <ul>
        <?php foreach ($display_case_cards as $display_case_card): ?>
            <li><?php echo htmlspecialchars($display_case_card['player_name']); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Add New Clothing</h3>
    <form id="addClothingForm">
        <label for="card">Card:</label>
        <select id="card" name="card_id" required>
            <?php foreach ($all_cards as $card): ?>
                <option value="<?php echo htmlspecialchars($card['card_id']); ?>"><?php echo htmlspecialchars($card['player_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="display_case_id" value="<?php echo $display_case_id; ?>">
        <input type="submit" value="Add">
    </form>
</body>
</html>

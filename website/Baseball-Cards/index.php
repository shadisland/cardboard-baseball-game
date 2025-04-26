<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Baseball Card Display Cases</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <style>
	
	
    </style>
    
    <?php require 'head_scripts_include.php';?>
    
</head>
<body>
	
        <?php require 'header.php';?>
    <br/>
	<div class="splash" style="height: 80px;">
		<ul>
			<li><a href="display_case.php">View Display Cases</a></li>
			
			<?php
			session_start();
			if( isset($_SESSION['user_id'])) { 
				echo '<li style="color: green;">(Already logged in)</li>';
			} else {
				echo '<li><a href="login.php">Admin Login</a></li>';
			}
			
			
			?>
		</ul>
	</div>
	
	<img style="width: 350px;" src="/images/1952-Bowman-Baseball-Box-1.png" >
	
   <?php require 'footer.php';?>
   
</body>
</html>
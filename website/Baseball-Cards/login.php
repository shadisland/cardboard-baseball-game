<?php
session_start();
if( isset($_SESSION['user_id'])) { 
	header('Location: display_case.php');
	exit();
} else {

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cardboard Baseball - Game & Virtual Displays - User Login</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <?php require 'head_scripts_include.php';?>
    
    <style>
	#loginDiv {
		border: 1px solid #0C2340;
		padding: 15px 10px 10px 15px;
		width: 280px;
		height: 125px;
	}
    	.login div {
		height: 40px;
    		font-size: 20px;
    	}
    	.splash label {
    		display:inline-block;
    		width: 97px;
    	}
    	.splash input[type="text"] {
    		display:inline-block;
    		width: 120px;
    		background-color: lightblue;
  		font-size: 20px;
  		padding: 3px;
    	}
    	.splash input[type="password"] {
    		display:inline-block;
    		width: 120px;
    		background-color: lightblue;
  		font-size: 20px;
  		padding: 3px;
    	}
    	
    	.splash button {
		background-color: #0C2340;
		border: none;
		color: white;
		padding: 10px 20px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 20px;
		margin: 6px 47px 4px 0px;
		cursor: pointer;
		position: relative;
  		float: right;
    	}
    	
        #togglePassword {
            	cursor: pointer;
        	width: 35px;
        	height: 35px;
        	background-image: url("/images/eye-icon.png");
		/*background-position: 100% 100%;*/
  		background-repeat: no-repeat;
  		background-size: 70px 35px;
  		display: inline-block;
        }
	.eye {
	    	background-position: 0% 100%;
	}
	.eye-slash {
		background-position: 100% 100%;
	}
	#loginMessage {
		color: green;
		font-weight: bold;
		font-size: 20px;
	}
    </style>
    <script>
        $(document).ready(function() {
	
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('eye');
                $(this).toggleClass('eye-slash');
               

		//const icon = $(this).src() === togglePasswordEye ? togglePasswordEyeSlash : togglePasswordEye;
                //const icon = $(this).text() === 'Show' ? 'Hide' : 'Show';
                //$(this).text(icon);
            });

            $('#loginForm').on('submit', function(event) {
                event.preventDefault();
                
                $.ajax({
                    url: 'authenticate.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'display_case.php';
                        } else {
                        	//alert('here2'+response.message);
                            $('#loginMessage').text(response.message);
                        }
                    }
                });
            });
        });
    </script>
    
</head>
<body>
<?php require 'header.php';?>

	<h2>Login to Cardboard Baseball - Game & Virtual Displays</h2>
    
	<div class="splash">
	  		<div id="loginDiv" class="login">
			    <form id="loginForm" method="post">
				<div>
				    <label for="username">Username:</label>
				    <input type="text" id="username" name="username" value="" required>
				</div>
				<div>
				    <label for="password">&nbsp;Password:</label>
				    <input type="password" id="password" name="password" value="" required>
				    <div id="togglePassword" class="eye-slash password-toggle">&nbsp;</div>
				</div>
				<div>
				    <button type="submit">Login</button>
				</div>
			    </form>
			</div>
	
		    <div id="loginMessage"></div>
		    
	</div>
    
    <?php require 'footer.php';?>
    
</body>
</html>
<?php

//end else
}

?>

